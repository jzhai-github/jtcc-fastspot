<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include(PATH_THIRD.'/too_many_cooks/config.php');		

class Too_many_cooks_ext {
	
	public $settings = array();
	public $description = 'Alerts authors when opening an entry which is already being edited.';
	public $docs_url = '';
	public $name = 'Too Many Cooks';
	public $settings_exist = 'n';
	public $version = TOO_MANY_COOKS_VERSION;
	public $required_by = array('module');

	public function __construct($settings = '')
	{
		$this->settings = $settings;
		ee()->lang->loadfile('too_many_cooks');
	}
	
	public function activate_extension()
	{
		return true;
	}
	
	public function publish_form_entry_data($result)
	{
		$js = '';
		$access = false;
		
		if(ee()->extensions->last_call !== FALSE)
		{
			$result = ee()->extensions->last_call;
		}
		
		ee('too_many_cooks:Sessions')->delete_expired_sessions();
		
		// Only move forward if this member can access the module
		$this_module = ee('Model')->get('Module')->filter('module_name', 'Too_many_cooks')->first();
		$modules = ee('Model')->
			get('MemberGroup', ee()->session->userdata('group_id'))
			->first()
			->AssignedModules
			->pluck('module_id');
		if(!empty($modules))
		{
			foreach($modules as $module)
			{
				if($module == $this_module->module_id)
				{
					$access = true;
				}
			}
		}
		if(ee()->session->userdata('group_id') == 1)
		{
			$access = true;
		}

		if($access == true && $result['entry_id'] > 0)
		{
			if($existing_session = ee('too_many_cooks:Sessions')->get_session($result['entry_id']))
			{
				// It takes a lot to make a stew...
				ee()->cp->load_package_css('too_many_cooks');
				
				$js .= '
					var tmc_container = $("<div class=\"alert banner issue\" id=\"too_many_cooks\"/>");
					$("body").prepend(tmc_container);
					$(tmc_container).append(\''.sprintf(lang('tmc_unsafe'), $existing_session['screen_name']).'\');
					$(tmc_container).on("click", "a", function(e)
					{
						e.preventDefault();
						location.reload(true);	
					});
					
					$("button[type=\'submit\']").remove();
					
					setInterval(
						function()
						{
							$.get("'.ee('too_many_cooks:Sessions')->get_action_url('check_session').'", { entry_id:  '.$result['entry_id'].'}, function(data)
							{
								if(data == "FREE" && $(tmc_container).hasClass("issue"))
								{
									$(tmc_container).slideUp(300, function()
									{
										$(this).empty().
										removeClass("issue").
										addClass("success").
										append(\''.lang('tmc_safe').'\').
										slideDown(300)
									});
								}
								else if(data != "FREE" && $(tmc_container).hasClass("success"))
								{
									var session = $.parseJSON(data);
									$(tmc_container).slideUp(300, function()
									{
										$(this).empty().
										removeClass("success").
										addClass("issue").
										append(\''
										.sprintf(
											lang('tmc_unsafe'),
											'\' + session.screen_name + \'').
										'\').
										slideDown(300)
									});
								}
							})
						}, '.(ee('too_many_cooks:Sessions')->seconds * 1000).'
					);
				';
			}
			else
			{
				// ... a pinch of salt and laughter too!
				ee('too_many_cooks:Sessions')->insert_session($result['entry_id']);
				$js .= 'setInterval(
					function()
					{
						$.get("'.ee('too_many_cooks:Sessions')->get_action_url('maintain_session').'", { entry_id:  '.$result['entry_id'].'});
					}, '.(ee('too_many_cooks:Sessions')->seconds * 1000).'
				);';
			}
			
			ee()->cp->add_to_foot('
				<!-- Too Many Cooks! -->
				<script>
				$(document).ready(function()
				{
					'.$js.'
				});
				</script>
			');
		}
		
		return $result;
	}

	public function entry_submission_end($entry_id, $meta, $data)
	{
		ee('too_many_cooks:Sessions')->delete_session($entry_id);
	}
	
	function disable_extension()
	{
		return true;
	}

	function update_extension($current = '')
	{
		if ($current == '' OR $current == $this->version)
		{
			return FALSE;
		}
	}	
}
