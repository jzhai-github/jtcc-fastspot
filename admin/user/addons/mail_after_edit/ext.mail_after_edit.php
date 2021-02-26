<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once __DIR__ . '/vendor/autoload.php';

use TNS\MailAfterEdit\EntryService;
use TNS\MailAfterEdit\SettingsService;

class Mail_after_edit_ext {

	public $settings = [];

	public $version = '2.1.0';

	public $entryService;

	public function __construct($settings = '')
	{
		$this->settings = $settings;
		$this->entryService = new EntryService;
	}

	public function activate_extension()
	{
		$this->settings = $this->getSettingsFromFile();

		$data = [
			[
				'class'     => __CLASS__,
				'method'    => 'after_channel_entry_insert',
				'hook'      => 'after_channel_entry_insert',
				'settings'  => serialize($this->settings),
				'priority'  => 10,
				'version'   => $this->version,
				'enabled'   => 'y'
			],
			[
				'class'     => __CLASS__,
				'method'    => 'after_channel_entry_update',
				'hook'      => 'after_channel_entry_update',
				'settings'  => serialize($this->settings),
				'priority'  => 10,
				'version'   => $this->version,
				'enabled'   => 'y'
			],
		];

		foreach ($data as $hook) {
			ee()->db->insert('extensions', $hook);
		}

	}

	public function disable_extension()
	{
		ee()->db->where('class', __CLASS__);
		ee()->db->delete('extensions');
	}

	public function update_extension($current = '')
	{
		// Version 2
		if(version_compare($current, '2.0', '<')) {

			ee()->db->where('class', __CLASS__);
			ee()->db->delete('extensions');

			$settings = $this->getSettingsFromFile();

			$data = [
				array(
					'class'     => __CLASS__,
					'method'    => 'after_channel_entry_insert',
					'hook'      => 'after_channel_entry_insert',
					'settings'  => serialize($settings),
					'priority'  => 10,
					'version'   => $this->version,
					'enabled'   => 'y'
				),
				array(
					'class'     => __CLASS__,
					'method'    => 'after_channel_entry_update',
					'hook'      => 'after_channel_entry_update',
					'settings'  => serialize($settings),
					'priority'  => 10,
					'version'   => $this->version,
					'enabled'   => 'y'
				)
			];

			foreach ($data as $hook) {

				ee()->db->insert('extensions', $hook);

			}

		} elseif(version_compare($current, '2.0.2', '<')) {

			// Version 2.02
			$settings = SettingsService::getSettings();

			$settings['message_config']['force_bcc'] = false;
			$settings['message_config']['send_individually'] = false;

			$data = [
				'settings'	=> serialize($settings),
			];

			ee()->db->update('extensions', $data, ['class' => 'Mail_after_edit_ext']);

		} elseif(version_compare($current, '2.1.0', '<')) {

			// Version 2.1.0
			$settings = SettingsService::getSettings();

			foreach ($settings['channel_config'] as $key => $channelConfig) {
				$settings['channel_config'][$key]['author'] = false;
			}

			$data = [
				'settings'	=> serialize($settings),
			];

			ee()->db->update('extensions', $data, ['class' => 'Mail_after_edit_ext']);

		}

		// UPDATE HOOKS
		return true;

	}

	public function settings_form()
	{
		return SettingsService::form();
	}

	public function save_settings()
	{
	    if (empty($_POST)) show_error(lang('unauthorized_access'));

	    return SettingsService::save($_POST);
	}

	// The Magic
	public function after_channel_entry_update($entry, $values, $modified)
	{
		$this->entryService->update($entry, $values, $modified);
	}

	public function after_channel_entry_insert($entry, $entryData)
	{
		$this->entryService->insert($entry, $entryData);
	}

	// Settings move
	private function initializeSettings()
	{

		// Set up app settings
		$settingData = [
			'channel_config'	=> [],
			'message_config'	=> [
				'start'		=> 'An entry has been updated! See below for information.',
				'end'		=> 'Sent by Mail After Edit',
				'domain'	=> 'http://example.com',
				'from'		=> 'from@example.com',
				'force_bcc' => false,
			],
			'skip_fields'		=> [
				'entry_date',
				'submit'
			],
		];

		return serialize($settingData);

	}

	private function getSettingsFromFile()
	{
		if( ! file_exists(PATH_THIRD . 'mail_after_edit/config.php')) {
			return $this->initializeSettings();
		}

		require PATH_THIRD . 'mail_after_edit/config.php';

		$newChannelData = [];

		foreach ($channel_config as $chKey => $chVal) {
			$item = [
				'channel'	=> $chKey,
				'type'		=> $chVal['type'],
				'from'		=> isset($chVal['from']) ? $chVal['from'] : '',
			];

			if($chVal['type'] == 'email') {
				$item['data'] = is_array($chVal['email'])
								? implode('|', $chVal['email'])
								: $chVal['email'];
			}

			if($chVal['type'] == 'member_group') {
				$item['data'] = is_array($chVal['group_id'])
								? $chVal['group_id']
								: explode('|', $chVal['group_id']);
			}

			$newChannelData[] = $item;
		}

		$settingData = [
			'channel_config'	=> $newChannelData,
			'message_config'	=> $message_info,
			'skip_fields'		=> $skip_fields,
		];

		return $settingData;
	}
}