<?php

include_once 'addon.setup.php';
use EEHarbor\Seeo\Helpers\AuditItem;
use EEHarbor\Seeo\Helpers\EntryAudit;
use EEHarbor\Seeo\Helpers\MetaFields;
use EEHarbor\Seeo\Helpers\PublishTabs;
use EEHarbor\Seeo\FluxCapacitor\Base\Mcp;
use EEHarbor\Seeo\Helpers\MigrationSource;
use EllisLab\ExpressionEngine\Library\Data\Collection;

/**
 * SEEO Control Panel Class
 *
 * @package         SEEO
 * @author          Tom Jaeger <Tom@EEHarbor.com>
 * @link            https://eeharbor.com
 * @copyright       Copyright (c) 2018, Tom Jaeger/EEHarbor
 */
class Seeo_mcp extends Mcp
{
    //----------------------------------------------------------------------------
    // Variables
    //----------------------------------------------------------------------------

    protected $per_page = 50;
    protected $data     = array();
    protected $package  = 'seeo';

    //----------------------------------------------------------------------------
    // METHODS
    //----------------------------------------------------------------------------

    public function __construct()
    {
        // -------------------------------------
        //  Load helper, libraries and models
        // -------------------------------------

        parent::__construct();

        ee()->load->add_package_path(PATH_THIRD . 'seeo');
        ee()->load->library(array('seeo_settings'));
        ee()->lang->loadfile('seeo');

        // -------------------------------------
        //  Class name shortcut
        // -------------------------------------

        $this->class_name = 'Seeo';

        // -------------------------------------
        //  Get site shortcut
        // -------------------------------------

        $this->site_id = (int) ee()->config->item('site_id');

        ee()->load->library('file_field');

        $this->prepViews();
    }

    //----------------------------------------------------------------------------
    // Index/settings page
    //----------------------------------------------------------------------------

    /**
     * Display an overview of meta information for all entries
     * @return string
     */
    public function index()
    {
        // Grab the current settings. This is just to make sure they're set; if not, this will instantiate the defaults.
        $settings = ee()->seeo_settings->get();

        // Get the list of all channels in this site.
        $this->data['channels'] = $this->getChannels();

        foreach ($this->data['channels'] as $channel_id => $channel_title) {
            $total = ee()->db->select('total_records')
                ->where('channel_id', $channel_id)
                ->where('site_id', $this->site_id)
                ->from('channels')
                ->get()
                ->row();

            if (empty($total)) {
                $total_records = 0;
            } else {
                $total_records = $total->total_records;
            }

            $this->data['channels'][$channel_id] = array(
                'channel_title' => $channel_title,
                'num_entries' => $total_records,
                'url' => $this->flux->moduleURL('audit_detail', array('channel_id' => $channel_id)),
                'settings_url' => $this->flux->moduleURL('meta', array('channel_id' => $channel_id)),
            );
        }
        return $this->flux->view('mcp/audit/index', $this->data, true);
    }

    /**
     * Module Index
     *
     * @access public
     * @return string
     */
    public function default_settings()
    {
        // --------------------------------------
        // Merge default & current settings
        // --------------------------------------

        $this->data = array_merge($this->data, ee()->seeo_settings->get());

        $this->data['reset_url'] = $this->flux->moduleURL('reset_entries');

        // Get the list of all channels in this site.
        $this->data['channels'] = $this->getChannels();

        foreach ($this->data['channels'] as $channel_id => $channel_title) {
            $this->data['channels'][$channel_id] = array(
                'channel_title' => $channel_title,
                'url' => $this->flux->moduleURL('meta', array('channel_id' => $channel_id))
            );
        }

        // --------------------------------------
        // Set Breadcrumbs
        // --------------------------------------

        $this->_set_cp_var('cp_page_title', lang('seeo_settings'));
        ee()->cp->set_breadcrumb($this->flux->moduleURL(), lang('seeo_page_title'));

        // --------------------------------------
        // Get the channels
        // --------------------------------------

        // ee()->load->model('channel_model');

        // $this->data['channels'] = ee()->channel_model->get_channels()->result();

        // --------------------------------------
        // Load up Assets if it exists
        // --------------------------------------

        if (ee('Addon')->get('assets') && ee('Addon')->get('assets')->isInstalled()) {
            require_once PATH_THIRD . 'assets/helper.php';
            require_once APPPATH . 'fieldtypes/EE_Fieldtype.php';
            require_once PATH_THIRD . 'assets/ft.assets.php';

            $assets_helper = new Assets_helper;
            $assets_helper->include_sheet_resources();

            // --------------------------------------
            // Set up OG Image in Assets
            // --------------------------------------
            $og_image = new Assets_ft();
            $og_image->settings = array(
                'multi' => 'n'
            );
            $og_image->field_name = 'seeo__seeo[og_image]';
            $og_image->var_id = 'seeo__seeo[og_image]';
            $this->data['assets_og_image'] = $og_image;

            // --------------------------------------
            // Set up Twitter Image in Assets
            // --------------------------------------
            $twitter_image = new Assets_ft();
            $twitter_image->settings = array(
                'multi' => 'n'
            );
            $twitter_image->field_name = 'seeo__seeo[twitter_image]';
            $twitter_image->var_id = 'seeo__seeo[twitter_image]';

            $this->data['assets_twitter_image'] = $twitter_image;
        }

        $this->_prep_image('og_image', null, 'default_og_image_button', 'default_og_image_thumbnail');
        $this->_prep_image('twitter_image', null, 'default_twitter_image_button', 'default_twitter_image_thumbnail');

        ee()->load->add_package_path(PATH_THIRD . 'seeo/');

        return $this->flux->view('mcp/settings/meta', $this->data, true);
    }

    public function meta()
    {
        // Check if a channel is specified in the url
        $channel_id = ee()->input->get('channel_id');

        $this->data = array_merge($this->data, ee()->seeo_settings->get($channel_id));

        // If not, let's load the first available one
        if (empty($channel_id)) {
            ee()->functions->redirect($this->flux->moduleURL('index'));
        }

        // Get the list of all channels in this site.
        $channels = $this->getChannels();

        // If the channel id is still empty, load the no channel view!
        if (empty($channels[$channel_id])) {
            ee()->functions->redirect($this->flux->moduleURL('index'));
        }

        // $this->data['input_prefix'] = 'seeo_channel_' . $channel_id;

        ee()->load->model('channel_model');
        $this->data['channel_id'] = $channel_id;
        $this->data['channel_title'] = $channels[$channel_id];
        $this->data['channels'] = ee()->channel_model->get_channels()->result();

        // --------------------------------------
        // Load up Assets if it exists
        // --------------------------------------

        if (ee('Addon')->get('assets') && ee('Addon')->get('assets')->isInstalled()) {
            require_once PATH_THIRD . 'assets/helper.php';
            require_once APPPATH . 'fieldtypes/EE_Fieldtype.php';
            require_once PATH_THIRD . 'assets/ft.assets.php';

            $assets_helper = new Assets_helper;
            $assets_helper->include_sheet_resources();

            // --------------------------------------
            // Set up OG Image in Assets
            // --------------------------------------
            $og_image = new Assets_ft();
            $og_image->settings = array(
                'multi' => 'n'
            );
            $og_image->field_name = 'seeo__seeo[og_image]';
            $og_image->var_id = 'seeo__seeo[og_image]';
            $this->data['assets_og_image'] = $og_image;

            // --------------------------------------
            // Set up Twitter Image in Assets
            // --------------------------------------
            $twitter_image = new Assets_ft();
            $twitter_image->settings = array(
                'multi' => 'n'
            );
            $twitter_image->field_name = 'seeo__seeo[twitter_image]';
            $twitter_image->var_id = 'seeo__seeo[twitter_image]';

            $this->data['assets_twitter_image'] = $twitter_image;
        }

        // $this->_prep_image('og_image', null, 'default_og_image_button', 'default_og_image_thumbnail');
        // $this->_prep_image('twitter_image', null, 'default_twitter_image_button', 'default_twitter_image_thumbnail');

        $this->data['reset_url'] = $this->flux->moduleURL('reset_entries');
        ee()->cp->add_to_foot('<script type="text/javascript" charset="utf-8" src="'.ee()->config->item('theme_folder_url').'user/seeo/javascript/seeo.js"></script>');

        ee()->cp->add_js_script(array(
            'file' => array(
                'fields/file/cp',
            ),
        ));

        ee()->load->add_package_path(PATH_THIRD . 'seeo/');

        return $this->flux->view('mcp/settings/meta', $this->data, true);
    }

    /**
     * Post save settings
     * @access public
     * @return string
     */
    public function save_meta_settings()
    {
        // --------------------------------------
        // Initialize a settings array
        // --------------------------------------

        $settings = array();
        $settingData = array();

        $settingData['site_id'] = (int) $this->site_id;

        // Check if this is for a specific channel or the global default.
        $settingData['channel_id'] = (int) ee()->input->post('channel_id');

        // --------------------------------------
        // Loop through default settings
        // for POST values, fallback to default
        // --------------------------------------

        foreach (ee()->seeo_settings->default_settings as $key => $val) {
            if (($settingData[$key] = ee()->input->post($key)) === false) {
                $settingData[$key] = $val;
            }
        }

        if (ee()->seeo_settings->get($settingData['channel_id'], 'file_manager') == 'assets') {
            $image_data = ee()->input->post('seeo__seeo');
            $og_image = $image_data['og_image'][0];
            $twitter_image = $image_data['twitter_image'][0];

            $settingData['og_image'] = array($og_image);
            $settingData['twitter_image'] = array($twitter_image);
        }

        // Check if we already have these settings saved.
        $existing_settings = ee()->db->select('id')->from('seeo_default_settings')->where(array('site_id' => $this->site_id, 'channel_id' => $settingData['channel_id']))->get()->row();

        // --------------------------------------
        // Save settings
        // --------------------------------------
        if (!empty($existing_settings)) {
            $setting_id = $existing_settings->id;
            ee()->db->where('site_id', $this->site_id);
            ee()->db->where('channel_id', $settingData['channel_id']);
            ee()->db->update('seeo_default_settings', $settingData);
        } else {
            ee()->db->insert('seeo_default_settings', $settingData);
        }

        // --------------------------------------
        // Set feedback message
        // --------------------------------------

        $this->flux->flashData('message_success', lang('seeo_changes_saved'));

        // --------------------------------------
        // Redirect back to overview
        // --------------------------------------

        ee()->functions->redirect($this->flux->moduleURL('index'));
    }

    public function reset_entries()
    {
        $channel_id = ee()->input->post('channel_id');
        $type = ee()->input->post('type');

        ee()->db->where('channel_id', intval($channel_id));

        if ($type === 'sitemap-include') {
            ee()->db->update('seeo', array('sitemap_include' => 'd'));
        } elseif ($type === 'sitemap-priority') {
            ee()->db->update('seeo', array('sitemap_priority' => 'd'));
        } elseif ($type === 'sitemap-change-frequency') {
            ee()->db->update('seeo', array('sitemap_change_frequency' => 'd'));
        } elseif ($type === 'robots') {
            ee()->db->update('seeo', array('robots' => 'd'));
        }

        die(json_encode(array('s' => 1)));
    }

    //----------------------------------------------------------------------------
    // Audit page
    //----------------------------------------------------------------------------

    public function audit_detail()
    {
        // Check if a channel is specified in the url
        $channel_id = ee()->input->get('channel_id');

        // If not, let's load the first available one
        if (empty($channel_id)) {
            ee()->functions->redirect($this->flux->moduleURL('index'));
        }

        // Get the list of all channels in this site.
        $channels = $this->getChannels();

        // If the channel id is still empty, load the no channel view!
        if (empty($channels[$channel_id])) {
            ee()->functions->redirect($this->flux->moduleURL('index'));
        }

        $this->data['channel']['channel_title'] = $channels[$channel_id];

        // --------------------------------------
        // Get start row
        // --------------------------------------

        $page = intval(ee()->input->get('page'));

        if (empty($page)) {
            $page = 1;
        }

        $start_num = ($page * $this->per_page) - $this->per_page;// + 1;

        // --------------------------------------
        // Grab the total entries from the channel
        // --------------------------------------

        $total = ee()->db->select('total_records')
            ->where('channel_id', $channel_id)
            ->where('site_id', $this->site_id)
            ->from('channels')
            ->get()
            ->row();

        // If no matching channel_id, total is 0
        if (empty($total)) {
            $total_records = 0;
        } else {
            $total_records = $total->total_records;
        }

        // --------------------------------------
        // Build the channel array
        // --------------------------------------
        $this->data['channel']['entries'] =
            ee()->db->select('
                ct.entry_id,
                ct.channel_id,
                ct.title,
                s.description as meta_description,
                s.title as meta_title,
                s.keywords as meta_keywords,
                s.author as meta_author,
                s.canonical_url as meta_canonical_url,
                s.robots as meta_robots,
                s.og_title as og_title,
                s.og_description as og_description,
                s.og_type as og_type,
                s.og_url as og_url,
                s.og_image as og_image,
                s.twitter_title as twitter_title,
                s.twitter_description as twitter_description,
                s.twitter_content_type as twitter_content_type,
                s.twitter_image as twitter_image
            ')
            ->from('channel_titles ct')
            ->where('ct.channel_id', $channel_id)
            ->where('ct.site_id', $this->site_id)
            ->limit($this->per_page, $start_num)
            ->join('seeo s', 'ct.entry_id = s.entry_id', 'left')
            ->order_by('ct.entry_date', 'desc')
            ->order_by('ct.entry_id', 'desc')
            ->get()
            ->result_array();

        // Loop through the entries and make audit's for them
        foreach ($this->data['channel']['entries'] as $entry) {
            // New entry audit
            $entryAudit = new EntryAudit($entry);

            $entryAudit->auditItems();

            // register a bunch of items
            foreach ($entry as $itemKey => $itemVal) {
                $entryAudit->registerItem(array('shortname' => $itemKey, 'value' => $itemVal));
            }
            $this->data['channel']['audit'][] = $entryAudit;
        }

        // --------------------------------------
        // Pagination
        // --------------------------------------

        $this->data['pagination'] = ee('CP/Pagination', $total_records)
            ->currentPage($page)
            ->perPage($this->per_page)
            ->queryStringVariable('page')
            ->displayPageLinks(5)
            ->render($this->flux->moduleURL('audit_detail', array('channel_id' => $channel_id)));

        // --------------------------------------
        // Set Breadcrumbs
        // --------------------------------------

        $this->_set_cp_var('cp_page_title', lang('seeo_entries_missing_meta'));
        ee()->cp->set_breadcrumb($this->flux->moduleURL(), lang('seeo_page_title'));

        return $this->flux->view('mcp/audit/entries', $this->data, true);
    }

    //----------------------------------------------------------------------------
    // Template Page
    //----------------------------------------------------------------------------

    /**
     * Display all of the template pages
     * @return string
     */
    public function template_page_meta()
    {
        // --------------------------------------
        // Display all the Template Pages
        // --------------------------------------
        $this->data['template_pages'] = ee('Model')->get('seeo:TemplatePage')->all();

        // if (! $this->data['template_pages']->count()) {
            // ee()->functions->redirect($this->flux->moduleURL('template_page_meta_create_edit'));
        // }

        // --------------------------------------
        // Set Breadcrumbs & Title
        // --------------------------------------
        $this->_set_cp_var('cp_page_title', lang('seeo_template_page_meta'));
        ee()->cp->set_breadcrumb($this->flux->moduleURL(), lang('seeo_page_title'));

        return $this->flux->view('mcp/template_page/template_page', $this->data, true);
    }

    /**
     * Edit Template Page
     * @return string
     */
    public function template_page_meta_create_edit()
    {
        $template_page_id = (int) ee()->input->get('template_page_id');

        $template_page_data = ee()->input->cookie('seeo_template_page_data');
        $meta_data = ee()->input->cookie('seeo_template_page_meta_data');

        if (!empty($template_page_data) || !empty($meta_data)) {
            if (!empty($template_page_data)) {
                $template_page_data = json_decode($template_page_data, true);
            }

            if (!empty($meta_data)) {
                $meta_data = json_decode($meta_data, true);
            }
        } else {
            $template_page_data = ee('Model')->get('seeo:TemplatePage', $template_page_id)->first();

            // If we don't find the template page, we are creating a new one
            if (empty($template_page_data)) {
                $template_page_data = ee('Model')->make('seeo:TemplatePage');
                $meta_data = ee('Model')->make('seeo:Meta');
                $meta_data = $meta_data->toArray();
            } else {
                $meta_data = $template_page_data->Meta->toArray();
            }

            $template_page_data = $template_page_data->toArray();
        }

        // Get an array of Channels
        $channels = $this->getChannels(true);

        ee()->load->helper('form');
        $this->data['form_open'] = form_open($this->flux->moduleURL('template_page_meta_post'));

        // --------------------------------------
        // Set Breadcrumbs & Title
        // --------------------------------------
        $this->_set_cp_var('cp_page_title', lang('seeo_template_page_meta'));
        ee()->cp->set_breadcrumb($this->flux->moduleURL(), lang('seeo_page_title'));

        $meta_fields = new MetaFields;


        // Form definition array
        $vars['sections'] = array(
            array(
                array(
                    'title' => 'seeo_template_page_path',
                    'desc' => 'seeo_template_page_path_description',
                    'fields' => array(
                        'template_page_id' => array(
                            'type' => 'hidden',
                            'value' => $template_page_id,
                            'required' => false,
                            'maxlength' => 255,
                        ),
                        'template_page_path' => array(
                            'type' => 'text',
                            'value' => $template_page_data['path'],
                            'required' => true,
                            'maxlength' => 255,
                        ),
                    )
                ),
                array(
                    'title' => 'seeo_template_page_channel',
                    'desc' => 'seeo_template_page_channel_description',
                    'fields' => array(
                        'template_page_channel' => array(
                            'type' => 'select',
                            'choices' => $channels,
                            'value' => $template_page_data['channel_id'],
                            'required' => false,
                            'maxlength' => 255,
                        )
                    )
                ),
            ),
            'seeo_standard_meta_tags' => $meta_fields->getSharedFormFields('standard', $meta_data),
            'seeo_open_graph_fields' => $meta_fields->getSharedFormFields('open_graph', $meta_data),
            'seeo_twitter_fields' => $meta_fields->getSharedFormFields('twitter', $meta_data),
            'seeo_sitemap_options' => $meta_fields->getSharedFormFields('sitemap', $meta_data),
        );

        if (!empty($template_page_id)) {
            $create_edit_page_title = lang('seeo_template_page_meta_update');
        } else {
            $create_edit_page_title = lang('seeo_template_page_meta_create');
        }

        // Final view variables we need to render the form
        $vars += array(
            'base_url' => $this->flux->moduleURL('template_page_meta_post'),
            'cp_page_title' => $create_edit_page_title,
            'save_btn_text' => 'btn_save_settings',
            'save_btn_text_working' => 'btn_saving'
        );

        ee()->cp->add_js_script(array(
            'file' => array(
                'fields/file/cp',
            ),
        ));

        return ee('View')->make('ee:_shared/form')->render($vars);

        return $this->flux->view('mcp/template_page/create_edit', $this->data, true);
    }

    /**
     * Save Template Page post
     * @return string
     */
    public function template_page_meta_post()
    {
        // Get the template page data from the post.
        $template_page_data['id'] = ee()->input->post('template_page_id');
        $template_page_data['path'] = ee()->input->post('template_page_path');
        $template_page_data['channel_id'] = ee()->input->post('template_page_channel');

        // Get the meta data from the post.
        $meta_data['channel_id'] = ee()->input->post('template_page_channel');
        $meta_data['title'] = ee()->input->post('title');
        $meta_data['description'] = ee()->input->post('description');
        $meta_data['keywords'] = ee()->input->post('keywords');
        $meta_data['author'] = ee()->input->post('author');
        $meta_data['canonical_url'] = ee()->input->post('canonical_url');
        $meta_data['robots'] = ee()->input->post('robots');
        $meta_data['og_title'] = ee()->input->post('og_title');
        $meta_data['og_description'] = ee()->input->post('og_description');
        $meta_data['og_type'] = ee()->input->post('og_type');
        $meta_data['og_url'] = ee()->input->post('og_url');
        $meta_data['og_image'] = ee()->input->post('og_image');
        $meta_data['twitter_title'] = ee()->input->post('twitter_title');
        $meta_data['twitter_description'] = ee()->input->post('twitter_description');
        $meta_data['twitter_content_type'] = ee()->input->post('twitter_content_type');
        $meta_data['twitter_image'] = ee()->input->post('twitter_image');
        $meta_data['sitemap_priority'] = ee()->input->post('sitemap_priority');
        $meta_data['sitemap_change_frequency'] = ee()->input->post('sitemap_change_frequency');
        $meta_data['sitemap_include'] = ee()->input->post('sitemap_include');

        // Save our data to the session so we can re-fill the fields if there's an error.
        // ee()->session->set_cache('seeo', 'template_page_data', $template_page_data);
        // ee()->session->set_cache('seeo', 'template_page_meta_data', $meta_data);

        ee()->input->set_cookie('seeo_template_page_data', json_encode($template_page_data), 10);
        ee()->input->set_cookie('seeo_template_page_meta_data', json_encode($meta_data), 10);

        // Check our required fields.
        if (empty($template_page_data['path'])) {
            ee('CP/Alert')->makeInline('shared-form')
                ->asIssue()
                ->withTitle(lang('seeo_template_page_path_required'))
                // ->addToBody(sprintf(lang('wygwam_config_saved_desc'), $configName))
                ->defer();

            // $this->flux->flashData('message_error', lang('seeo_template_page_path_required'));
            ee()->functions->redirect($this->flux->moduleURL('template_page_meta_create_edit'));
        }

        // Assets image
        if (ee()->seeo_settings->get(0, 'file_manager') == 'assets') {
            $meta_data['og_image'] = $meta_data['og_image'][0];
            $meta_data['twitter_image'] = $meta_data['twitter_image'][0];
        }

        // Get the Templage Page model
        $template_page = ee('Model')->get('seeo:TemplatePage', $template_page_data['id'])->first();

        // We do not want to stomp over id's on save.
        unset($template_page_data['id']);//, $meta_data['id']);

        // If we don't find the Template Page, lets make a new one.
		 if (empty($template_page)) {
            $template_page = ee('Model')->make('seeo:TemplatePage');
        }

        // same with meta. Get it and make a new one if it doesnt exist.
        $meta = $template_page->Meta;
		if (empty($meta)) {
            $meta = ee('Model')->make('seeo:Meta', $meta_data);
        }

        // Set the new meta data and save it
        $meta->set($meta_data);
        $meta->save();

        // Add the meta_id, set the info, then save it
        $template_page_data['meta_id'] = $meta->id;
        $template_page->set($template_page_data);
        $template_page->save();

        // Clear our cookies so we don't accidentally load the content on the next edit.
        ee()->input->delete_cookie('seeo_template_page_data');
        ee()->input->delete_cookie('seeo_template_page_meta_data');

        ee('CP/Alert')->makeInline('shared-form')
            ->asSuccess()
            ->withTitle(lang('seeo_changes_saved'))
            ->defer();

        // flash data
        ee()->functions->redirect($this->flux->moduleURL('template_page_meta'));
    }

    /**
     * Delete Template Page post
     * @return string
     */
    public function template_page_meta_delete()
    {
        $template_page_id = (int) ee()->input->get('template_page_id');

        $template_page = ee('Model')->get('seeo:TemplatePage', $template_page_id)->first();

        // If we don't find the template_page, lets redirect.
		if (empty($template_page)) {
            $this->flux->flashdata('message_error', "This template page meta does not exist! <a href='" . $this->flux->moduleURL('template_page_meta_create_edit') . "'>Create a Template Page?</a>");
            return ee()->functions->redirect($this->flux->moduleURL('template_page_meta'));
        }

        // We have a Template Page, delete it
        $template_page->delete();

        // Flash to screen that it was deleted
        $this->flux->flashdata('message_success', "Template Page Successfully Deleted!");

        // Redirect to the Template Page Meta page
        ee()->functions->redirect($this->flux->moduleURL('template_page_meta'));
    }

    //----------------------------------------------------------------------------
    // Migrate page
    //----------------------------------------------------------------------------

    /**
     * Gather necessary information to build migration
     * @return string
     */
    public function setup_migrate()
    {
        // --------------------------------------
        // Display all the activated channels
        // --------------------------------------

        $this->data['channels'] = $this->getChannels();

        // --------------------------------------
        // Set Breadcrumbs & Title
        // --------------------------------------

        // Get migration sources
        $this->data['migration_sources'] = MigrationSource::getAllSources();

        $this->_set_cp_var('cp_page_title', lang('seeo_migrate'));
        ee()->cp->set_breadcrumb($this->flux->moduleURL(), lang('seeo_page_title'));

        return $this->flux->view('mcp/migrate/index', $this->data, true);
    }

    /**
     * Confirm migration page
     * @return string
     */
    public function confirm_migrate()
    {
        // Get the migration type from the post vars and redirect if its not right
        $migration_source = ee()->input->get_post('source');

        if (!in_array($migration_source, array('better_meta', 'seo_lite', 'zc_meta'))) {
            $this->flux->flashData('message_error', 'Not a valid source type!');
            ee()->functions->redirect($this->flux->moduleURL('setup_migrate'));
        }

        // Create a migration source object
        $migration = new MigrationSource($migration_source);

        // Set the migration data based on the migration data type
        if (ee()->input->get_post('channel_based_migration')) {
            $migration_data = ee()->input->post('seeo_channels');
        } else {
            $migration_data = ee()->input->post('zc_meta_migrate');
        }

        // Set information about the migration on the object
        $migration->setMigrationData($migration_data);

        // --------------------------------------
        // Assign this to hidden fields for the confirmation screen
        // --------------------------------------

        $this->data['migration'] = $migration;

        // --------------------------------------
        // Set Breadcrumbs & Title
        // --------------------------------------

        $this->_set_cp_var('cp_page_title', lang('seeo_migrate'));
        ee()->cp->set_breadcrumb($this->flux->moduleURL(), lang('seeo_page_title'));

        return $this->flux->view('mcp/migrate/confirm', $this->data, true);
    }

    /**
     * Post migration route for actually doing the work
     * @return string
     */
    public function post_migrate()
    {
        // --------------------------------------
        // Gather type of migration
        // --------------------------------------
        $migration_source = ee()->input->get_post('context');

        if (!in_array($migration_source, array('better_meta', 'seo_lite', 'zc_meta'))) {
            $this->flux->flashData('message_error', 'Not a valid source type!');
            ee()->functions->redirect($this->flux->moduleURL('setup_migrate'));
        }

        // --------------------------------------
        // What do we want to migrate?
        // --------------------------------------
        $migration_data = ee()->input->post('migrate');

        foreach ($migration_data as &$data) {
            $data = unserialize($data);
        }

        // Create a migration source object
        $migration = new MigrationSource($migration_source);
        $migration->doMigration($migration_data);

        // --------------------------------------
        // Set Breadcrumbs & Title
        // --------------------------------------

        $this->_set_cp_var('cp_page_title', lang('seeo_migrate'));
        ee()->cp->set_breadcrumb($this->flux->moduleURL(), lang('seeo_page_title'));

        $this->flux->flashData('message_success', 'Data migrated!');
        ee()->functions->redirect($this->flux->moduleURL('index'));
    }

    public function migrate_default_data()
    {
        $migration_source = ee()->input->get_post('seeo_context');

        if (!in_array($migration_source, array('better_meta', 'seo_lite', 'zc_meta'))) {
            $this->flux->flashData('message_error', 'Not a valid source type!');
            ee()->functions->redirect($this->flux->moduleURL('setup_migrate'));
        }

        $migration = new MigrationSource($migration_source);
        $resp = $migration->doInitialMigration();

        $this->flux->flashData('message_success', 'Base Data migrated!');
        ee()->functions->redirect($this->flux->moduleURL('setup_migrate'));
    }

    /**
     * Confirm remove source page
     * @return string
     */
    public function confirm_remove_source()
    {
        // Get the migration type from the post vars and redirect if its not right
        $migration_source = ee()->input->get_post('source');

        if (!in_array($migration_source, array('better_meta', 'seo_lite', 'zc_meta'))) {
            $this->flux->flashData('message_error', 'Not a valid source type!');
            ee()->functions->redirect($this->flux->moduleURL('setup_migrate'));
        }

        // Create a migration source object
        $migration = new MigrationSource($migration_source);

        // --------------------------------------
        // Assign this to hidden fields for the confirmation screen
        // --------------------------------------

        $this->data['migration'] = $migration;

        // --------------------------------------
        // Set Breadcrumbs & Title
        // --------------------------------------

        $this->_set_cp_var('cp_page_title', lang('seeo_remove_source'));
        ee()->cp->set_breadcrumb($this->flux->moduleURL(), lang('seeo_page_title'));

        return $this->flux->view('mcp/migrate/confirm_remove_source', $this->data, true);
    }

    /**
     * Post remove_source route for actually doing the work
     * @return string
     */
    public function post_remove_source()
    {
        // --------------------------------------
        // Gather type of migration
        // --------------------------------------
        $migration_source = ee()->input->get_post('seeo_context');

        if (!in_array($migration_source, array('better_meta', 'seo_lite', 'zc_meta'))) {
            $this->flux->flashData('message_error', 'Not a valid source type!');
            ee()->functions->redirect($this->flux->moduleURL('setup_migrate'));
        }

        // Create a migration source object
        $migration = new MigrationSource($migration_source);
        $migration->doRemoveSource();

        $this->flux->flashData('message_success', 'Source Removed!');
        ee()->functions->redirect($this->flux->moduleURL('setup_migrate'));
    }

    //----------------------------------------------------------------------------
    // Private functions
    //----------------------------------------------------------------------------

    /**
     * Compose a view
     *
     * @access private
     * @param string
     * @return string
     */
    private function prepViews()
    {
        // Set some basic data for the views
        $this->data['base_url'] = $this->flux->moduleURL();
        // $this->data['input_prefix'] = 'seeo';
        $this->data['themes_url'] = $this->flux->getAddonThemesDir();
        $this->data['flux'] = $this->flux;
        $this->data['csrf_token_name']  = defined('CSRF_TOKEN') ? 'csrf_token' : 'XID';
        $this->data['csrf_token_value'] = defined('CSRF_TOKEN') ? CSRF_TOKEN : XID_SECURE_HASH;

        // Load CSS & JS
        // Man, this is so clever... Cred to @low
        $version = '&amp;v=' . (SEEO_DEBUG ? time() : SEEO_VERSION);

        ee()->cp->load_package_css($this->package . $version);
        ee()->cp->load_package_js($this->package . $version);
    }

    /**
     * Set cp var
     *
     * @access     private
     * @param      string
     * @param      string
     * @return     void
     */
    private function _set_cp_var($key, $val)
    {
        if (version_compare(APP_VER, '2.6.0', '<')) {
            ee()->cp->set_variable($key, $val);
        } else {
            ee()->view->$key = $val;
        }
    }

    /**
     * Returns an array of channels that are enabled in SEEO
     */
    private function getChannels($includeEmptyOption = false)
    {
        if (!empty($this->data['channels'])) {
            return $this->data['channels'];
        }

        $this->data['channels'] = array();

        // Get an array of Channels
        if ($includeEmptyOption) {
            $this->data['channels'] = [0 => 'No Channel'];
        }

        foreach (ee('Model')->get('Channel')->filter('site_id', $this->site_id)->all() as $channel) {
            $this->data['channels'][$channel->channel_id] = $channel->channel_title;
        }

        return $this->data['channels'];

        // ee()->load->model('channel_model');

        // $channel_settings = ee()->seeo_settings->get()['seeo_channels'];

        // foreach ($channel_settings as $key => $val) {
        //     if ($val['settings']['enabled'] == 'y') {
        //         $channel_details = ee()->db->select('channel_name, channel_title, total_entries')
        //             ->from('channels')
        //             ->where('channel_id', $key)
        //             ->get()
        //             ->row_array();
        //         $channel_settings[$key]['channel_name']  = $channel_details['channel_name'];
        //         $channel_settings[$key]['channel_title'] = $channel_details['channel_title'];
        //         $channel_settings[$key]['total_entries'] = $channel_details['total_entries'];
        //     } else {
        //         unset($channel_settings[$key]);
        //     }
        // }

        // return $channel_settings;
    }

    /**
     * Sets up the image with all the attributes necessary, as well as loading the filepicker
     */
    private function _prep_image($data_name, $target_name = null, $button_name = null, $thumbnail_name = null)
    {
        if (!$target_name) {
            $target_name = $data_name;
        }
        if (!$button_name) {
            $button_name = $data_name . '_button';
        }
        if (!$thumbnail_name) {
            $thumbnail_name = $data_name . '_thumbnail';
        }

        $name = is_array($this->data[$data_name]) ? $this->data[$data_name][0] : $this->data[$data_name];

        // Get the image file
        $file = $this->_parse_field($name);

        // Create an image button
        $this->data[$button_name] = ee('CP/FilePicker')->make()->getLink()
            ->withValueTarget($target_name)
            ->withImage($target_name)
            ->asThumbs()
            ->setText('Choose Image')
            ->setAttribute('class', 'btn action file-field-filepicker seeo-filepicker')
            ->setAttribute('id', $button_name);

        // Set the selected file if it exists
        if ($file) {
            $this->data[$button_name]->setSelected($file->file_id);
        }

        // Set the thumbnail
        $this->data[$thumbnail_name] = ee('Thumbnail')->get($file)->url;

        ee()->cp->add_js_script(array(
            'file' => array(
                'fields/file/cp',
            ),
        ));
    }

    /**
     * Gets a field and parses it (Mostly for images)
     */
    private function _parse_field($data)
    {
        $file = null;

        // If the file field is in the "{filedir_n}image.jpg" format
        if (preg_match('/^{filedir_(\d+)}/', $data, $matches)) {
            // Set upload directory ID and file name
            $dir_id    = $matches[1];
            $file_name = str_replace($matches[0], '', $data);

            $file = ee('Model')->get('File')
                ->filter('file_name', $file_name)
                ->filter('upload_location_id', $dir_id)
                ->filter('site_id', ee()->config->item('site_id'))
                ->first();
        } elseif (!empty($data) && is_numeric($data)) {
            // If file field is just a file ID
            $file = ee('Model')->get('File', $data)->first();
        }

        return $file;
    }
} // End Class
/* End of file mcp.seeo.php */
