<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use EllisLab\ExpressionEngine\Library\CP\Table;

require_once PATH_THIRD . 'hop_404_reporter/helper.php';

class hop_404_reporter_mcp
{
	public $version		= HOP_404_REPORTER_VERSION;
	public $module_name	= HOP_404_REPORTER_CLASS_NAME;
	public $short_name	= HOP_404_REPORTER_SHORT_NAME;

	private $_base_url;
	private $_base_url_params;
	private $_perpage = 25;
	private $_page = 1;
	private $_offset = 0;
	private $_limit;
	private $_sort;
	private $_keywords;
	private $_date_range_filter;
	private $_referrer_url_filter;
	private $_frequency_notification_filter;
	private $_filters;
	private $_filters_base_url;

	public function __construct()
	{
		$this->_base_url = ee('CP/URL')->make('addons/settings/hop_404_reporter');
		$this->module_name = lang('hop_404_reporter_module_name');

		// Check if hop_404_reporter_emails is setup correctly
		if (version_compare($this->version, '3.0.0', '<')) {
			ee('CP/Alert')->makeInline('shared-form')
				->asWarning()
				->withTitle('Error')
				->addToBody('Please update ' . $this->module_name)
				->defer();
			ee()->functions->redirect(ee('CP/URL')->make('addons'));
		}

		// Should we run the setup wizard?
		$setup_wizard = ee('Model')->get($this->short_name . ':Config')->filter('setting_name', 'setup_wizard')->first();

		if (empty($setup_wizard)) {
			$this->_runSetupWizard();
		}
	}

	private function _runSetupWizard()
	{
		// We only do this redirection once
		$setup_wizard = ee('Model')->make($this->short_name . ':Config', [
			'setting_name' 	=> 'setup_wizard',
			'value'			=> 'displayed'
		]);

		$setup_wizard->save();

		ee('CP/Alert')->makeInline('shared-form')
						->asWarning()
						->withTitle(lang('404_template'))
						->addToBody(lang('setup_wizard_notice'))
						->defer();
		ee()->functions->redirect(ee('CP/URL')->make('addons/settings/' . $this->short_name . '/setup_wizard'));
	}

	public function setup_wizard()
	{
		$this->_buildNav();

		$vars = [
			'all_good' => false,
			'edit_404' => false,
			'create_404' => false,
			'create_notification' => false,
			'body' => ''
		];

		// Are we running the wizard?
		if (ee()->input->get('run')) {

			$run_notification_setup = false;

			if (!empty(ee()->config->item('site_404'))) { // site/not_found
				// Site 404 is setup
				list($template_group, $template_name) = explode('/', ee()->config->item('site_404'));
				$template = ee('Model')->get('Template')->with('TemplateGroup')->filter('TemplateGroup.group_name', $template_group)->filter('template_name', $template_name)->first();

				if (empty($template)) {
					// In case the specified 404 page doesn't exist
					$vars['create_404'] = ee('CP/Alert')->makeInline('shared-form')
															->asIssue()
															->withTitle(lang('404_template'))
															->addToBody(lang('create_404_missing'))
															->render();
				} else {
					// Does it already have the 404 logic
					if (strpos($template->template_data, '{exp:hop_404_reporter:process_url}') !== false) {
						// All good
						$vars['all_good'] = ee('CP/Alert')->makeInline('shared-form')
															->asSuccess()
															->withTitle(lang('setup_wizard'))
															->addToBody(lang('all_good'))
															->render();
						$run_notification_setup = true;
					} else {
						// Let's add in the Hop 404 Reporter magic
						// Prepend Hop 404 Reporter code
						$template->template_data = '{exp:hop_404_reporter:process_url}' . $template->template_data;
						$template->save();

						$vars['edit_404'] = ee('CP/Alert')->makeInline('shared-form')
															->asSuccess()
															->withTitle(lang('404_template'))
															->addToBody(lang('edit_404'))
															->render();

						// We will want to also setup a basic notification
						$run_notification_setup = true;
						$vars['all_good'] = true;
					}
				}
			} else {
				$default_template_group = ee('Model')->get('TemplateGroup')->filter('is_site_default', 'y')->first();

				if (empty($default_template_group)) {
					$vars['create_404'] = ee('CP/Alert')->makeInline('shared-form')
														->asIssue()
														->withTitle(lang('404_template'))
														->addToBody(lang('create_404_missing_group'))
														->render();
				} else {
					// Check if there is a template named not_found
					$template = ee('Model')->get('Template')->with('TemplateGroup')->filter('TemplateGroup.group_name', $default_template_group->group_name)->filter('template_name', 'not_found')->first();

					if (empty($template)) {
						// Great! There's no template called not_found. Let's create one!
						$template = ee('Model')->make('Template', [
							'template_name'	=> 'not_found',
							'template_type' => 'webpage',
							'template_data' => lang('default_page_not_found'),
							'group_id'		=> $default_template_group->group_id
						]);

						$template->save();

						// Save to default 404
						ee()->config->update_site_prefs(['site_404' => $default_template_group->group_name . '/not_found']);

						$vars['create_404'] = ee('CP/Alert')->makeInline('shared-form')
															->asSuccess()
															->withTitle(lang('404_template'))
															->addToBody(lang('create_404'))
															->render();
						$vars['all_good'] = true;
					} else {
						// Already a not_found template but not set as default site 404
						$vars['create_404'] = ee('CP/Alert')->makeInline('shared-form')
															->asIssue()
															->withTitle(lang('404_template'))
															->addToBody(lang('create_404_duplicate'))
															->render();
					}

					// We will want to also setup a basic notification
					$run_notification_setup = true;
				}
			}

			if ($run_notification_setup) {
				// Create notification
				$notifications = ee()->db->get('hop_404_reporter_emails');

				if ($notifications->num_rows() == 0) {
					ee()->db->insert(
						'hop_404_reporter_emails',
						[
							'email_address'	=> ee()->config->item('webmaster_email'),
							'referrer'		=> 'n',
							'frequency'		=> 'always'
						]
					);

					$vars['create_notification'] = ee('CP/Alert')->makeInline('shared-form')
																	->asSuccess()
																	->withTitle(lang('email_notifications'))
																	->addToBody(lang('create_notification'))
																	->render();
				}

				// We also check if the 404 template has been setup properly to make it so that this setup wizard don't need running again
				if ($vars['all_good']) {
					$setup_wizard = ee('Model')->get($this->short_name . ':Config')->filter('setting_name', 'setup_wizard')->first();

					// No longer need to run the update
					$setup_wizard->value = 'done';
					$setup_wizard->save();
				}
			}
		} else {
			$setup_wizard = ee('Model')->get($this->short_name . ':Config')->filter('setting_name', 'setup_wizard')->first();

			if ($setup_wizard->value == 'done') {
				$vars['all_good'] = ee('CP/Alert')->makeInline('shared-form')
													->asSuccess()
													->withTitle(lang('setup_wizard'))
													->addToBody(lang('all_good'))
													->render();
			} else {
				$vars['body'] = '<h2>' . lang('setup_wizard') . '</h2><hr>' . sprintf(lang('setup_wizard_steps'), ee('CP/URL')->make('addons/settings/hop_404_reporter/setup_wizard', ['run' => 'wizard']));
			}
		}

		return [
			'heading'			=> lang('setup_wizard'),
			'body'				=> ee('View')->make('hop_404_reporter:setup_wizard')->render($vars),
			'breadcrumb'		=> [
				ee('CP/URL', 'addons/settings/hop_404_reporter')->compile() => lang('hop_404_reporter_module_name')
			]
		];
	}

	/**
	 * Build the navigation menu for the module
	 */
	private function _buildNav()
	{
		$sidebar = ee('CP/Sidebar')->make();

		$sd_div = $sidebar->addHeader(lang('reports'));
		$sd_div_list = $sd_div->addBasicList();
		$sd_div_list->addItem(lang('404_urls'), ee('CP/URL', 'addons/settings/hop_404_reporter'));
		$sd_div = $sidebar->addHeader(lang('notifications'))
			->withButton(lang('new'), ee('CP/URL', 'addons/settings/hop_404_reporter/add_email'));
		$sd_div_list = $sd_div->addBasicList();
		$sd_div_list->addItem(lang('email_notifications'), ee('CP/URL', 'addons/settings/hop_404_reporter/notifications'));

		$sd_div = $sidebar->addHeader(lang('settings'));
		$sd_div_list = $sd_div->addBasicList();
		$sd_div_list->addItem(lang('settings'), ee('CP/URL', 'addons/settings/hop_404_reporter/settings'));
		$sd_div_list->addItem(lang('setup_wizard'), ee('CP/URL', 'addons/settings/hop_404_reporter/setup_wizard'));
		$sd_div_list->addItem(lang('support_and_help'), ee('CP/URL', 'addons/manual/hop_404_reporter'));
		$sd_div_list->addItem(lang('license') . ($this->_checkLicenseValid() ? '<span class="st-open" style="float: right;">' . lang('license_valid') . '</span>' : '<span class="st-closed" style="float: right;">' . lang('license_invalid') . '</span>'), ee('CP/URL', 'addons/settings/' . $this->short_name . '/license'));
	}

	//--------------------------------------------------------------------------
	//		  INDEX PAGE (URLs LIST)
	//--------------------------------------------------------------------------

	/*
	 * Because if no method found, this one will be returned
	 * We're making it the URLs list
	 */
	public function index()
	{
		// If POST action, do that first
		if (ee()->input->post('action')) {
			$this->modify_urls();
		}
		
		$this->_buildNav();
		$header = [
			'title' 	=> lang('hop_404_reporter_module_name'),
			'form_url'	=> $this->_create_base_url_with_existing_parameters('sort_col', 'sort_dir', 'filter_by_ref_url', 'filter_by_date_range'),
			// 'toolbar_items' => array(
			// 	'settings' => array(
			//		 'href' => ee('CP/URL')->make('settings/template'),
			//		 'title' => lang('settings')
			//	 ),
			// ),
			'search_button_value' => lang('search_urls')
		];
		ee()->cp->load_package_css('hop_404');
		ee()->view->header = $header;

		ee()->load->library('table');
		ee()->load->library('pagination');
		ee()->load->library('javascript');
		ee()->load->helper('form');

		$table = ee('CP/Table', ['autosort' => false, 'autosearch' => false, 'limit' => $this->_perpage, 'sort_col' => (ee()->input->get('sort_col') ?: 'last_occurred'), 'sort_dir' => (ee()->input->get('sort_dir') ?: 'desc')]);
		$table->setColumns(
			[
				'last_occurred',
				'count',
				'url',
				'referrer_url',
				[
					'type'  => Table::COL_CHECKBOX
				]
			]
		);

		//--- Get Data ---
		
		//Setup query parameters (using GET/POST params...)
		$this->urls_query_setup();
		
		//Setup pagination params
		if (ee()->input->get('page') != null) {
			$this->_offset = $this->_perpage*(intval(ee()->input->get('page'))-1);
			$this->_page = intval(ee()->input->get('page'));
		}

		$url_query = ee()->db->get('hop_404_reporter_urls', $this->_perpage, $this->_offset);
		$urls = $url_query->result();

		// Process data and format it for Table
		$data = [];
		foreach ($urls as $url) {
			$data[] = [
				$url->last_occurred,
				$url->count,
				$url->url,
				$url->referrer_url,
				[
					'name' => 'urls[]',
					'value' => $url->url_id,
					'data'  => [
						'confirm' => lang('url') . ': <b>' . htmlentities($url->url, ENT_QUOTES) . '</b>'
					]
				]
			];
		}
		$table->setData($data);

		$vars['table'] = $table->viewData($this->_create_base_url_with_existing_parameters(['filter_by_date_range', 'filter_by_ref_url', 'search'], ['search']));

		// -- Pagination --
		// Get count
		ee()->db->select('count(*) AS count')
			->from('hop_404_reporter_urls');
		
		//Setup params (because we ran our first query, params need to be set again)
		$this->urls_query_setup();
		
		//Get results
		$query = ee()->db->get();
		$query_result_array = $query->result_array();
		$total_count = intval($query_result_array[0]['count']);

		$pagination = ee('CP/Pagination', $total_count);
		$pagination->perPage($this->_perpage);
		$pagination->currentPage($this->_page);

		$vars['pagination'] = $pagination->render(
			$this->_create_base_url_with_existing_parameters(
				['sort_col', 'sort_dir', 'filter_by_ref_url', 'filter_by_date_range', 'search', 'filter_by_date', 'perpage'], 
				['search', 'filter_by_date', 'perpage']
			)
		);

		// Default vars

		$vars['action_url'] = $this->_create_base_url_with_existing_parameters(
			['sort_col', 'sort_dir', 'filter_by_ref_url', 'filter_by_date_range', 'search', 'filter_by_date', 'perpage'], 
			['search', 'filter_by_date', 'perpage']
		);
		$vars['search_keywords'] = $this->_keywords;
		$vars['search_url'] = $this->_create_base_url_with_existing_parameters('sort_col', 'sort_dir', 'filter_by_ref_url', 'filter_by_date_range');
		$vars['form_hidden'] = null;

		$vars['filter_keywords'] = $this->_keywords;
		// $vars["filter_referrer_url_options"] = $this->_get_filter_referrer_url_options();
		// $vars["filter_referrer_url_selected"] = $this->_referrer_url_filter;
		// $vars["filter_date_range_options"] = $this->_get_filter_date_range_options();
		// $vars["filter_date_range_selected"] = $this->_date_range_filter;
		
		//Setup filters
		$this->setup_url_list_filters($total_count);
		$vars['filters'] = $this->_filters;
		$vars['filters_base_url'] = $this->_filters_base_url;

		// View related stuff
		ee()->cp->add_js_script([
			'file' 	=> 'cp/sort_helper',
			'plugin'=> 'ee_table_reorder',
			'file' 	=> ['cp/confirm_remove'],
		]);
		ee()->javascript->compile();

		// return ee()->load->view('index', $vars, true);
		return [
			'heading'			=> lang('404_urls'),
			'body'				=> ee('View')->make('hop_404_reporter:index')->render($vars),
			'breadcrumb'		=> [
				ee('CP/URL', 'addons/settings/hop_404_reporter')->compile() => lang('hop_404_reporter_module_name')
			]
		];
	}
	
	/**
	 * Setup filters for the index page (URLs list)
	 * @return void
	 */
	private function setup_url_list_filters($total_count)
	{	
		//Build filters base url (keep some parameters)
		// In order to keep the search for later, we set it as a GET variable.
		$this->_filters_base_url = $this->_create_base_url_with_existing_parameters(['sort_col', 'sort_dir', 'search'], ['search']);
		
		$referers = ee('CP/Filter')->make('filter_by_ref_url', 'filter_referrer_url', [
			'referrer_saved'	=> lang('filter_referrer_saved'),
			'no_referrer' 		=> lang('filter_no_referrer_url'),
			'referrer_not_saved'=> lang('filter_referrer_url_not_saved')
		]);
		$referers->disableCustomValue();
		
		$filters = ee('CP/Filter')
			->add($referers)
			// ->add($dates)
			->add('Date')
			->add('Perpage', $total_count)
			;
		
		// ee()->view->filters = $filters->render($this->_base_url);
		$this->_filters = $filters->render($this->_filters_base_url);
	}
	
	/**
	 * Will get parameters and add proper query parameters
	 * @return void
	 */
	private function urls_query_setup()
	{
		// Get parameters
		if (ee()->input->get('sort_col') != null) {
			if (ee()->input->get('sort_dir') != null && ee()->input->get('sort_dir') == 'desc') {
				ee()->db->order_by(ee()->input->get('sort_col'), 'DESC');
			} else {
				ee()->db->order_by(ee()->input->get('sort_col', 'ASC'));
			}
		} else {
			ee()->db->order_by('last_occurred', 'DESC'); // By default sort by date
		}

		$search_phrase = null;
		// We verify POST first, because it's what is sent by the search form
		if (ee()->input->post('search') != null && ee()->input->post('search') != '') {
			$search_phrase = ee()->input->post('search');
		} else if	(ee()->input->get('search') != null && ee()->input->get('search') != '') {
			$search_phrase = ee()->input->get('search');
		}

		if ($search_phrase) {
			$this->_keywords = $search_phrase;
			$sql_filter_where = "(`url` LIKE '%" . ee()->db->escape_like_str($search_phrase) . "%' OR `referrer_url` LIKE '%".ee()->db->escape_like_str($search_phrase)."%' )";
			ee()->db->where($sql_filter_where, null, true);
		}
		
		if (ee()->input->get('filter_by_ref_url')) {
			$referrer_param = ee()->input->get('filter_by_ref_url');
			if ($referrer_param == 'referrer_saved') {
				ee()->db->where('referrer_url !=', 'referrer_not_specified');
				ee()->db->where('referrer_url !=', 'referrer_not_tracked');
			} else if ($referrer_param == 'no_referrer') {
				ee()->db->where('referrer_url', 'referrer_not_specified');
			} else if ($referrer_param == 'referrer_not_saved') {
				ee()->db->where('referrer_url', 'referrer_not_tracked');
			}
		}
		
		if (ee()->input->get('filter_by_date_range')) {
			$days = intval(ee()->input->get('filter_by_date_range'));
			$datetime = new DateTime();
			$datetime->setTimestamp(ee()->localize->now);
			$datetime->sub(new Datefrequency('P'.$days.'D'));
			ee()->db->where('last_occurred >', $datetime->format('Y-m-d H:i:s'));
		}
		
		// In case user clicked on a filter
		// The value is a number of seconds if user clicked on a default filter
		// OR the value is a custom date that was inputed by the user
		// if (ee()->input->get('filter_by_date') && !ee()->input->post('filter_by_date'))
		// {
		// 	if (ctype_digit(ee()->input->get('filter_by_date')))
		// 	{
		// 		$seconds = ee()->input->get('filter_by_date');
		// 		$days = $seconds/3600/24;
		// 		$datetime = new DateTime();
		// 		$datetime->setTimestamp(ee()->localize->now);
		// 		$datetime->sub(new Datefrequency('P'.$days.'D'));
		// 		ee()->db->where('last_occurred >', $datetime->format('Y-m-d H:i:s'));
		// 	}
		// 	else
		// 	{
		// 		if (!is_array(ee()->input->get('filter_by_date')))
		// 		{
		// 			// var_dump(ee()->input->get('filter_by_date'));
		// 			$datetime = new DateTime(ee()->input->get('filter_by_date'));
		// 			ee()->db->where('last_occurred >', $datetime->format('Y-m-d H:i:s'));
		// 		}
		// 		
		// 	}
		// 	
		// }
		// 
		// // In case user input a custom value
		// // The value is a date M/D/Y (I hate that format)
		// // Note : we will pass this along as a GET var for pagination
		// if (ee()->input->post('filter_by_date'))
		// {
		// 	$datetime = new DateTime(ee()->input->post('filter_by_date'));
		// 	ee()->db->where('last_occurred >', $datetime->format('Y-m-d H:i:s'));
		// }
		
		// Setup temporary filters to automatically retrieve good values
		$filters = ee('CP/Filter')->add('Date');
		$values = $filters->values();
		$date_value = $values['filter_by_date'];
		
		if ($date_value) {
			if (is_array($date_value)) {
				$dt_from = new DateTime();
				$dt_from->setTimestamp(($date_value[0] + 0));
				$dt_to = new DateTime();
				$dt_to->setTimestamp(($date_value[1] + 0));
				ee()->db->where('last_occurred >=', $dt_from->format('Y-m-d H:i:s'));
				ee()->db->where('last_occurred <', $dt_to->format('Y-m-d H:i:s'));
			} else {
				$days = $date_value/3600/24;
				$datetime = new DateTime();
				$datetime->setTimestamp(ee()->localize->now);
				$datetime->sub(new Datefrequency('P'.$days.'D'));
				ee()->db->where('last_occurred >', $datetime->format('Y-m-d H:i:s'));
			}
		}

		if (ee()->input->get('perpage') && !ee()->input->post('perpage')) {
			$this->_perpage = intval(ee()->input->get('perpage'));
		}
		// In case user input a custom value
		// Note : we will pass this along as a GET var for pagination
		if (ee()->input->post('perpage')) {
			$this->_perpage = intval(ee()->input->post('perpage'));
		}
	}

	/**
	 * Receive and process POST data from list page
	 **/
	function modify_urls()
	{
		$urls_to_modify = ee()->input->post('urls');

		if ($urls_to_modify == null || !is_array($urls_to_modify)) {
			ee()->functions->redirect(ee('CP/URL')->make('addons/settings/hop_404_reporter'));
		}

		$count = 0;
		foreach($urls_to_modify as $url_id) {

			if (ee()->input->post('bulk_action') == 'delete') {
				ee()->db->delete('hop_404_reporter_urls', ['url_id' => $url_id]);
				$count++;
			}
		}

		if (ee()->input->post('bulk_action') == 'delete') {
			ee('CP/Alert')
				->makeInline('url_deleted_success')
				->asSuccess()
				->withTitle(lang('url_deleted_success'))
				->addToBody(sprintf(lang('url_deleted_message'), $count))
				->defer();
		}
		ee()->functions->redirect(ee('CP/URL')->make('addons/settings/hop_404_reporter'));
	}

	/**
	 * Displays list of email to be notified when 404 occurs
	 */
	public function notifications()
	{
		$this->_buildNav();
		$this->_base_url = ee('CP/URL')->make('addons/settings/hop_404_reporter/notifications');
		$header = [
			'title' 	=> lang('hop_404_reporter_module_name'),
			'form_url'	=> $this->_create_base_url_with_existing_parameters('sort_col', 'sort_dir', 'filter_by_ref_url', 'filter_by_date_range'),
			// 'toolbar_items' => array(
			//	 'settings' => array(
			//		 'href' => ee('CP/URL')->make('settings/template'),
			//		 'title' => lang('settings')
			//	 ),
			// ),
			'search_button_value' => lang('search_emails_notif')
		];
		ee()->view->header = $header;

		ee()->cp->load_package_css('hop_404');

		ee()->load->library('pagination');
		ee()->load->library('javascript');
		ee()->load->helper('form');
		
		$table = ee('CP/Table', ['autosort' => false, 'autosearch' => false, 'limit' => $this->_perpage]);
		$table->setColumns(
			[
				'email_address',
				'url_to_match',
				'referrer',
				'frequency',
				[
					'type'  => Table::COL_CHECKBOX
				]
			]
		);
		$table->setNoResultsText(sprintf(lang('no_found'), lang('email_notifications')), 'create_new_one', ee('CP/URL')->make('addons/settings/hop_404_reporter/add_email'));
		
		//--- Get Data ---

		// Get parameters
		$this->email_notification_query_setup();

		if (ee()->input->get('page') != null) {
			$this->_offset = $this->_perpage*(intval(ee()->input->get('page'))-1);
			$this->_page = intval(ee()->input->get('page'));
		}

		$emails_query = ee()->db->get('hop_404_reporter_emails', $this->_perpage, $this->_offset);
		$emails = $emails_query->result();
		
		// Process data and format it for Table
		$data = [];
		foreach ($emails as $email) {
			$data[] = [
				$email->email_address,
				$email->url_to_match,
				($email->referrer == 'y' ? lang('required') : lang('not_required')),
				ucfirst($email->frequency),
				[
					'name' => 'emails[]',
					'value' => $email->email_id,
					'data'  => [
						'confirm' => lang('email') . ': <b>' . htmlentities($email->email_address, ENT_QUOTES) . '</b>'
					]
				]
			];
		}
		$table->setData($data);

		$vars['table'] = $table->viewData($this->_create_base_url_with_existing_parameters(['filter_by_frequency', 'search'], ['search']));

		$vars['search_keywords'] = $this->_keywords;
		$vars['search_url'] = $this->_create_base_url_with_existing_parameters('sort_col', 'sort_dir', 'filter_by_ref_url', 'filter_by_date_range');
		
		//Setup pagination
		ee()->db->select('count(*) AS count')
			->from('hop_404_reporter_emails');
		// Setup query params
		$this->email_notification_query_setup();
		$query = ee()->db->get();
		$query_result_array = $query->result_array();
		$total_count = intval($query_result_array[0]['count']);

		$pagination = ee('CP/Pagination', $total_count);
		$pagination->perPage($this->_perpage);
		$pagination->currentPage($this->_page);

		$vars['pagination'] = $pagination->render($this->_create_base_url_with_existing_parameters(['filter_by_frequency', 'search'], ['search']));

		$vars['action_url'] = ee('CP/URL', 'addons/settings/hop_404_reporter/modify_emails');
		$vars['form_hidden'] = null;

		$vars['options'] = [
			'reset'  	=> lang('email_reset_selected'),
			'delete'	=> lang('delete_selected')
		];

		$vars['filter_keywords'] = $this->_keywords;
		//$vars["filter_frequency_selected"] = $this->_frequency_notification_filter;
		//$vars["filter_frequency_options"] = $this->_get_filter_email_notification_frequency_options();
		
		//Setup filters
		$this->setup_email_list_filters();
		$vars['filters'] = $this->_filters;

		ee()->cp->add_js_script(['plugin' => 'dataTables']);
		ee()->javascript->compile();

		// return ee()->load->view('emails', $vars, true);
		return [
			'heading'		=> lang('email_notifications'),
			'body'			=> ee('View')->make('hop_404_reporter:emails')->render($vars),
			'breadcrumb'	=> [
				ee('CP/URL', 'addons/settings/hop_404_reporter')->compile() => lang('hop_404_reporter_module_name')
			],
		];
	}
	
	/**
	 * Create filter options for the email notifications list
	 * @return void
	 */
	private function setup_email_list_filters()
	{	
		//Build filters base url (keep some parameters)
		$this->_filters_base_url = $this->_create_base_url_with_existing_parameters(['sort_col', 'sort_dir', 'search'], ['search']);
		
		$frequencys = ee('CP/Filter')->make('filter_by_frequency', 'filter_frequency', [
			'frequency_always'	=> lang('email_notification_frequency_always'),
			'frequency_once'		=> lang('email_notification_frequency_once')
		]);
		$frequencys->disableCustomValue();
		
		$filters = ee('CP/Filter')
			->add($frequencys);
		
		// ee()->view->filters = $filters->render($this->_base_url);
		$this->_filters = $filters->render($this->_filters_base_url);
	}
	
	/**
	 * Setup query parameters for email notifications list
	 * @return void
	 */
	private function email_notification_query_setup()
	{
		// Get parameters
		if (ee()->input->get('sort_col') != null) {
			if (ee()->input->get('sort_dir') != null && ee()->input->get('sort_dir') == 'desc') {
				ee()->db->order_by(ee()->input->get('sort_col'), 'DESC');
			} else {
				ee()->db->order_by(ee()->input->get('sort_col', 'ASC'));
			}
		}

		$search_phrase = null;
		// We verify POST first, because it's what is sent by the search form
		if (ee()->input->post('search') != null && ee()->input->post('search') != '') {
			$search_phrase = ee()->input->post('search');
		} else if	(ee()->input->get('search') != null && ee()->input->get('search') != '') {
			$search_phrase = ee()->input->get('search');
		}
		
		if ($search_phrase) {
			$this->_keywords = $search_phrase;
			$sql_filter_where = "(`email_address` LIKE '%" . ee()->db->escape_like_str($search_phrase) . "%' OR `url_to_match` LIKE '%" . ee()->db->escape_like_str($search_phrase) . "%' )";
			ee()->db->where($sql_filter_where, null, true);
		}
		
		if (ee()->input->get('filter_by_frequency')) {
			$frequency = ee()->input->get('filter_by_frequency');
			if ($frequency == 'frequency_always') {
				ee()->db->where('frequency', 'always');
			} else if ($frequency == 'frequency_once') {
				ee()->db->where('frequency', 'once');
			}
		}
	}

	/**
	 * Receive and process POST data from email list page
	 **/
	public function modify_emails()
	{
		$emails_to_modify = ee()->input->post('emails');

		$count = 0;
		foreach ($emails_to_modify as $email_id) {
			if (ee()->input->post('bulk_action') == 'delete') {
				ee()->db->delete('hop_404_reporter_emails', ['email_id' => $email_id]);
				$count++;
			} else if (ee()->input->post('bulk_action') == 'reset') {
				// ee()->db->update('hop_404_reporter_emails', array('parameter' => ''), array('email_id' => $email_id));
				// We have to remove the email from all URLs where a notification have been sent
				$query = ee()->db->select(['url_id', 'notification_to'])
					->from('hop_404_reporter_urls')
					->like('notification_to', 'i:'.$email_id.'')
					->get();

				$results_urls = $query->result_array();

				foreach ($results_urls as $result_url) {
					$serialized_notification_to = $result_url['notification_to'];
					$notification_to = unserialize($serialized_notification_to);
					if ($notification_to && array_key_exists($email_id, $notification_to)) {
						unset($notification_to[$email_id]);
						ee()->db->update('hop_404_reporter_urls', 
							['notification_to' => serialize($notification_to)], 
							['url_id' => $result_url['url_id']]
						);
					}
				}
				$count++;
			}
		}

		if (ee()->input->post('bulk_action') == 'delete') {
			ee('CP/Alert')
				->makeInline('email_deleted_success')
				->asSuccess()
				->withTitle(lang('email_deleted_success'))
				->addToBody(sprintf(lang('email_deleted_message'), $count))
				->defer();
		} else if (ee()->input->post('bulk_action') == 'reset') {
			ee('CP/Alert')
				->makeInline('email_reseted_success')
				->asSuccess()
				->withTitle(lang('email_reseted_success'))
				->addToBody(sprintf(lang('email_reset_message'), $count))
				->defer();
		}

		ee()->functions->redirect(ee('CP/URL')->make('addons/settings/hop_404_reporter/notifications'));
	}

	/**
	 * Create an email notification
	 * @return Page cotent or page redirection
	 */
	public function add_email()
	{
		$this->_buildNav();
		$header = [
			'title' 	=> lang('hop_404_reporter_module_name'),
		];
		ee()->view->header = $header;
		
		$vars = [
			'cp_page_title' => lang('add_new_notification'),
			'base_url' => ee('CP/URL', 'addons/settings/hop_404_reporter/add_email')->compile(),
			'save_btn_text' => lang('settings_save'),
			'save_btn_text_working' => lang('settings_save_working'),
		];
		
		// Using shard form to create config form
		$vars['sections'] = [
			[
				[
					'title' => 'email_address',
					'desc' => 'email_address_desc',
					'fields' => [
						'email_address' => ['type' => 'text', 'required' => 'true', 'value' => '']
					]
				],
				[
					'title' => 'email_notification_url_filter_label',
					'desc' => 'email_notification_url_filter_desc',
					'fields' => [
						'url_to_match' => ['type' => 'text', 'value' => '']
					]
				],
				[
					'title' => 'email_notification_referrer_label',
					'desc' => 'email_notification_referrer_desc',
					'fields' => [
						'referrer' => ['type' => 'yes_no', 'value' => true]
					]
				],
				[
					'title' => 'email_notification_frequency_label',
					'desc' => 'email_notification_frequency_desc',
					'fields' => [
						'frequency' => ['type' => 'select', 'choices' => ['always' => lang('email_notification_frequency_always'), 'once' => lang('email_notification_frequency_once')]]
					]
				],
				[
					'title' => '',
					'fields' => [
						'action' => ['type' => 'hidden', 'value' => 'add_email']
					]
				]
			]
		];
		
		//If we have POST data, try to save the new email notification
		if (ee()->input->post('action') == 'add_email') {
			// Validation
			$validator = ee('Validation')->make();
			
			$validator->defineRule('notif_frequency', function($key, $value, $parameters) {
				if (!in_array($value, Hop_404_reporter_helper::get_email_notification_globals())) {
					return lang('email_notificaiton_frequency_invalid');
				}
				return true;
			});
			
			$validator->setRules([
				'email_address' => 'required|email',
				'frequency' => 'required|notif_frequency'
			]);
			$result = $validator->validate($_POST);
			
			if ($result->isValid()) {
				// Get back all values, store them in array and save them
				$fields = [];
				foreach ($vars['sections'] as $settings) {
					foreach ($settings as $setting) {
						foreach ($setting['fields'] as $field_name => $field) {
							$fields[$field_name] = ee()->input->post($field_name);
						}
					}
				}

				// We don't want to save that field, it's not a setting
				unset($fields['action']);
				
				ee()->db->insert('hop_404_reporter_emails', $fields);
				ee('CP/Alert')
					->makeInline('shared-form')
					->asSuccess()
					->withTitle(lang('emaill_notification_add_success'))
					->addToBody(lang('emaill_notification_add_success_desc'))
					->defer();

				ee()->functions->redirect(ee('CP/URL', 'addons/settings/hop_404_reporter/notifications')->compile());
			} else {
				$vars['errors'] = $result;
				ee('CP/Alert')
					->makeInline('shared-form')
					->asIssue()
					->withTitle(lang('settings_save_error'))
					->addToBody(lang('settings_save_error_desc'))
					->now();
			}
		}

		// return ee()->load->view('add_email', $vars, true);
		return [
			'heading'		=> lang('add_new_notification'),
			'body'			=> ee('View')->make('hop_404_reporter:add_email')->render($vars),
			'breadcrumb'	=> [
				ee('CP/URL', 'addons/settings/hop_404_reporter')->compile() => lang('hop_404_reporter_module_name')
			]
		];
	}
	
	//--------------------------------------------------------------------------
	//
	//		  DISPLAY & SAVE SETTINGS PAGE
	//
	//--------------------------------------------------------------------------

	/**
	 * Displays configuration panel
	 */
	public function settings()
	{
		$this->_buildNav();
		$header = [
			'title' => lang('hop_404_reporter_module_name'),
		];
		ee()->view->header = $header;
		
		$settings = Hop_404_reporter_helper::get_settings();
		
		$vars = [
			'cp_page_title' => lang('settings'),
			'base_url' => ee('CP/URL', 'addons/settings/hop_404_reporter/settings')->compile(),
			'save_btn_text' => lang('settings_save'),
			'save_btn_text_working' => lang('settings_save_working'),
		];
		
		// Using EE3 API to create config form
		$vars['sections'] = [
			[
				[
					'title' => 'set_enabled',
					'desc' => 'set_enabled_desc',
					'fields' => [
						'enabled' => ['type' => 'yes_no', 'value' => $settings['enabled']]
					]
				],
				[
					'title' => 'set_send_email_notifications',
					'desc' => 'set_send_email_notifications_desc',
					'fields' => [
						'send_email_notifications' => ['type' => 'yes_no', 'value' => $settings['send_email_notifications']]
					]
				],
				[
					'title' => 'set_referrer_tracking',
					'desc' => 'set_referrer_tracking_desc',
					'fields' => [
						'referrer_tracking' => ['type' => 'yes_no', 'value' => $settings['referrer_tracking']]
					]
				],
				[
					'title' => 'set_email_address_sender',
					'desc' => 'set_email_address_sender_desc',
					'fields' => [
						'email_address_sender' => ['type' => 'text', 'value' => $settings['email_address_sender']]
					]
				],
				[
					'title' => 'set_email_notification_subject',
					'desc' => 'set_email_notification_subject_desc',
					'fields' => [
						'email_notification_subject' => ['type' => 'text', 'value' => $settings['email_notification_subject'], 'required' => true]
					]
				],
				[
					'title' => 'set_404_email_template',
					'desc' => 'set_404_email_template_desc',
					'fields' => [
						'email_template' => [
							'type' => 'html',
							'content' => '<textarea rows="12" name="email_template" required>' . $settings['email_template'] . '</textarea>'
						]
					]
				],
				[
					'title' => '',
					'fields' => [
						'action' => ['type' => 'hidden', 'value' => 'save_settings']
					]
				]
			]
		];
		

		if (ee()->input->post('action') == 'save_settings') {
			$settings = [];
			$form_is_valid = true;
			
			// Validation
			$validator = ee('Validation')->make();
			
			$validator->setRules([
				'enabled' => 'enum[y,n]',
				'send_email_notifications' => 'enum[y,n]',
				'referrer_tracking' => 'enum[y,n]',
				'email_address_sender' => 'email',
				'email_notification_subject' => 'required',
				'email_template' => 'required'
			]);
			$result = $validator->validate($_POST);
			
			if ($result->isValid()) {
				// Get back all values, store them in array and save them
				$fields = [];
				foreach ($vars['sections'] as $settings) {
					foreach ($settings as $setting) {
						foreach ($setting['fields'] as $field_name => $field) {
							$fields[$field_name] = ee()->input->post($field_name);
						}
					}
				}
				// We don't want to save that field, it's not a setting
				unset($fields['action']);
				
				Hop_404_reporter_helper::save_settings($fields);
				ee('CP/Alert')->makeInline('shared-form')
					->asSuccess()
					->withTitle(lang('preferences_updated'))
					->addToBody(lang('preferences_updated_desc'))
					->defer();

				ee()->functions->redirect(ee('CP/URL', 'addons/settings/hop_404_reporter/settings')->compile());
			} else {
				$vars['errors'] = $result;
				ee('CP/Alert')->makeInline('shared-form')
					->asIssue()
					->withTitle(lang('settings_save_error'))
					->addToBody(lang('settings_save_error_desc'))
					->now();
				$vars['settings'] = $settings;
			}

		} // ENDIF action = save_settings

		return [
			'heading'			=> lang('settings'),
			'body'				=> ee('View')->make('hop_404_reporter:settings')->render($vars),
			'breadcrumb'	=> [
				ee('CP/URL', 'addons/settings/hop_404_reporter')->compile() => lang('hop_404_reporter_module_name')
			]
		];
	}
	
	//--------------------------------------------------------------------------
	//		  GLOBAL METHODS
	//--------------------------------------------------------------------------
	
	/**
	 * This is building a base url including already existing parameters.
	 * @param  array	$parameters	Array of names of parameters to keep in the url
	 * @return string			 [description]
	 */
	protected function _create_base_url_with_existing_parameters($parameters, $post_parameters = [])
	{
		$base_url_with_parameters = clone $this->_base_url;
		if (!is_array($parameters)) {
			return $base_url_with_parameters;
		}
		
		// We prioritize POST data
		if ($post_parameters != null && count($post_parameters) != 0) {
			foreach ($post_parameters as $parameter) {
				if (ee()->input->post($parameter)) {
					$base_url_with_parameters->setQueryStringVariable($parameter, ee()->input->post($parameter));
					
					// Remove it from GET data
					unset($parameters[$parameter]);
				}
			}
		}
		
		foreach ($parameters as $parameter) {
			if (ee()->input->get($parameter)) {
				$base_url_with_parameters->setQueryStringVariable($parameter, ee()->input->get($parameter));
			}
		}
		
		return $base_url_with_parameters;
	}

	/*
	 * License setting page
	 */
	public function license()
	{
		$this->_buildNav();

		$vars = [];
		$vars['action_url'] = ee('CP/URL', 'addons/settings/' . $this->short_name . '/save_license');

		$license_setting = ee('Model')->get($this->short_name . ':Config')->filter('setting_name', 'license')->first();
		$vars['license_key'] = $license_setting->value != 'n/a' ? $license_setting->value : '';
		$vars['license_setting_id'] = !empty($license_setting->setting_id) ? $license_setting->setting_id : '';

		// Check if license is saved as valid
		$vars['license_valid'] = $this->_checkLicenseValid();

		$vars['license_agreement'] = 'https://www.hopstudios.com/software/' . $this->short_name . '/license';

		return [
			'heading'		=> lang('license'),
			'body'			=> ee('View')->make('' . $this->short_name . ':license')->render($vars),
			'breadcrumb'	=> [
				ee('CP/URL', 'addons/settings/' . $this->short_name . '')->compile() => $this->module_name
			]
		];
	}

	/*
	 * Save license action
	 */
	public function save_license()
	{
		$license_key = ee()->input->post('license_key');
		$license_setting_id = ee()->input->post('license_setting_id');

		if ($license_setting_id) {
			$license_setting = ee('Model')->get($this->short_name . ':Config')->filter('setting_id', $license_setting_id)->first();
		} else {
			$license_setting = ee('Model')->make($this->short_name . ':Config');
		}

		$license_setting->setting_name = 'license';
		$license_setting->value = $license_key;
		$license_setting->save();

		// Check if license is valid from hop license
		$is_valid = $this->_checkLicense($license_key);

		if ($is_valid == 'valid') {
			$license_valid = ee('Model')->get($this->short_name . ':Config')->filter('setting_name', 'license_valid')->first();
			if (empty($license_valid)) {
				$license_valid = ee('Model')->make($this->short_name . ':Config');
				$license_valid->setting_name = 'license_valid';
				$license_valid->value = 'valid license';
				$license_valid->save();
			}
		} else {
			$license_valid = ee('Model')->get($this->short_name . ':Config')->filter('setting_name', 'license_valid')->first();
			if ( ! empty($license_valid)) {
				$license_valid->delete();
			}
		}

		ee()->functions->redirect(ee('CP/URL')->make('addons/settings/' . $this->short_name . '/license'));
	}

	/*
	 * Check if license is valid
	 * Connect with hop studios license api
	 */
	private function _checkLicense($license_key)
	{
		$url = 'https://license.hopstudios.com/check/' . $this->short_name . '/' . $license_key;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$result = curl_exec($ch);

		// if ( ! $result) {
		// 	echo 'Curl error: ' . curl_error($ch);
		// }
		curl_close($ch);

		return $result;
	}

	/*
	 * Quick check in the db to see if license is valid
	 */
	private function _checkLicenseValid()
	{
		try {
			$license_valid = ee('Model')->get($this->short_name . ':Config')->filter('setting_name', 'license_valid')->first();
			if ( ! empty($license_valid)) {
				return $license_valid->value == 'valid license';
			}
		} catch (Exception $e) {
			// Make sure Hop License table is configured properly
			ee('CP/Alert')->makeInline('shared-form')
				->asWarning()
				->withTitle('Error')
				->addToBody('Please update ' . $this->module_name)
				->defer();
			ee()->functions->redirect(ee('CP/URL')->make('addons'));
		}
		return false;
	}
}