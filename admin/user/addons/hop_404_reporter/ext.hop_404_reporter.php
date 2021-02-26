<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once PATH_THIRD . 'hop_404_reporter/config.php';

class Hop_404_reporter_ext {
	public $name 			= HOP_404_REPORTER_NAME;
	public $short_name 		= HOP_404_REPORTER_SHORT_NAME;
	public $version			= HOP_404_REPORTER_VERSION;
	public $settings_exist = 'n';	
	public $settings = [];
	
	public function activate_extension() {
		$this->_setExtensions();
	}
	
	public function update_extension($current = '')
	{
		if ($current == '' OR $current == $this->version) {
			return false;
		}

		$this->_setExtensions();
	}

	public function disable_extension()
	{
		ee()->db->where('class', __CLASS__);
		ee()->db->delete('extensions');
	}

	private function _setExtensions()
	{
		$extensions = [
			[
				// Add this add on to the menu manger
				'class'		=> __CLASS__,
				'method'	=> 'addToMenu',
				'hook'		=> 'cp_custom_menu',
				'settings'	=> '',
				'priority'	=> 10,
				'version'	=> $this->version,
				'enabled'	=> 'y'
			]
		];

		foreach ($extensions as $extension) {
			// Check if set already
			if (ee()->db->select('extension_id')->from('extensions')->where(['class' => $extension['class'], 'method' => $extension['method']])->get()->num_rows() > 0) {
				// Update the extensions
				ee()->db->update(
					'extensions',
					[
						'hook'		=> $extension['hook'],
						'settings'	=> $extension['settings'],
						'priority'	=> $extension['priority'],
						'version'	=> $extension['version'],
						'enabled'	=> $extension['enabled']
					],
					[
						'class'		=> $extension['class'],
						'method'	=> $extension['method']
					]
				);
			} else {
				ee()->db->insert('extensions', $extension);
			}
		}
	}

	public function addToMenu($menu)
	{
		ee()->lang->loadfile($this->short_name);

		$sub = $menu->addSubmenu($this->name);
		$sub->addItem(
			lang('404_urls'),
			ee('CP/URL')->make('addons/settings/hop_404_reporter')
		);
		$sub->addItem(
			lang('email_notifications'),
			ee('CP/URL')->make('addons/settings/hop_404_reporter/display_emails')
		);
		$sub->addItem(
			lang('settings'),
			ee('CP/URL')->make('addons/settings/hop_404_reporter/settings')
		);

		$addon_icon_act = ee()->cp->fetch_action_id('File', 'addonIcon');

		// EE6
		if (!empty($addon_icon_act)) {
			$site_url = site_url();
			$addon_icon_url = $site_url . '?ACT=' . $addon_icon_act . '&addon=' . $this->short_name . '&file=icon.svg';

			$javascript_addon_name = 'addon_' . $this->short_name;

			// Replace with our own icon :D
			ee()->cp->add_to_foot('<script>
				if (typeof ' . $javascript_addon_name . ' === \'undefined\') {
					let ' . $javascript_addon_name . ' = $(\'.ee-sidebar__item[title="' . $this->name . '"] .ee-sidebar__item-custom-icon\');' .
					$javascript_addon_name . '.html(\'<img src="' . $addon_icon_url . '" style="display: inline-block; vertical-align: middle;">\');' .
					$javascript_addon_name . '.css(\'background\', \'none\');
				}
			</script>');
		}
	}
}