<?php

namespace TNS\MailAfterEdit;

use TNS\MailAfterEdit\LogService;

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class SettingsService {

	public static function form()
	{
		// Setup fun
		ee()->load->library('javascript');

		$settings = self::getSettings();

		$channels = self::getChannels();

		$memberGroups = self::getMemberGroups();

		// Never mind
		$params = [
			'skip_fields'		=> $settings['skip_fields'],
			'message_config'	=> $settings['message_config'],
			'channel_config'	=> $settings['channel_config'],
			'channels'			=> $channels,
			'memberGroups'		=> $memberGroups,
			'memberPlaceholder' => (version_compare(APP_VER, '5.9.0', '<') ? 'MemberGroup' : 'Role'),
			'save_url'			=> ee('CP/URL', 'addons/settings/mail_after_edit/save'),
			'vue_url'			=> ee('CP/URL', 'addons/settings/mail_after_edit/assets/vue.js')
		];

		return ee()->load->view('settings', $params, true);
	}

	public static function save($data)
	{
		$settings = self::getSettings();

		$settingsData = [
			'skip_fields'		=> is_array($data['skip_fields']) ? $data['skip_fields'] : explode('|', $data['skip_fields']),
			'message_config'	=> $data['message_config'],
		];

		$settingsData['message_config']['force_bcc'] = isset($data['message_config']['force_bcc']) && $data['message_config']['force_bcc'] == 'true';
		$settingsData['message_config']['send_individually'] = isset($data['message_config']['send_individually']) && $data['message_config']['send_individually'] == 'true';

		$newChannelData = [];

		foreach ($data['channel_config'] as $channel) {

			$item = [
				'channel'		=> $channel['channel'],
				'type'			=> $channel['type'],
				'from'			=> isset($channel['from']) ? $channel['from'] : '',
				'mail_on'		=> isset($channel['mail_on']) ? $channel['mail_on'] : [],
				'skip_fields'	=> isset($channel['skip_fields'])
									? (is_array($channel['skip_fields'])
										? $channel['skip_fields']
										: explode('|', $channel['skip_fields']))
									: '',
				'author'		=> isset($channel['author']) && $channel['author'] == 'true'
									? true
									: false,
				'data'			=> $channel['data']
			];

			$newChannelData[] = $item;
		}

		$settingsData['channel_config'] = $newChannelData;

		$data = [
			'settings'	=> serialize($settingsData),
		];

		ee()->db->update(
			'extensions',
			$data,
			[
				'class' => 'Mail_after_edit_ext'
			]
		);

		return ee()->output->send_ajax_response($settingsData);
	}

	public static function getChannels()
	{
		$siteId = ee()->config->item('site_id');

		$channels =  ee('Model')
						->get('Channel')
						->filter('site_id', $siteId)
						->all();

		$results = [];

		$channels->each(function($c) use (&$results) {
			$results[] = [
				'id'	=> $c->channel_id,
				'name'	=> $c->channel_title
			];
		});

		return $results;
	}

	public static function getMemberGroups()
	{
		$siteId = ee()->config->item('site_id');
		$results = [];

		if( version_compare(APP_VER, '5.9.0', '<') ) {
			$groups =  ee('Model')
							->get('MemberGroup')
							->filter('site_id', $siteId)
							->all();
			$groups->each(function($c) use (&$results) {
				$results[] = [
					'id'	=> $c->group_id,
					'name'	=> $c->group_title
				];
			});

		} else {
			$groups = ee('Model')->get('Role')
							->all();

			foreach ($groups as $group) {
				$results[] = [
					'id'	=> $group->role_id,
					'name'	=> $group->name,
				];
			}
		}

		return $results;
	}

	public static function getSettings()
	{
		$query = ee()->db
					->select('settings')
					->from('extensions')
					->where([
						'class'	=> 'Mail_after_edit_ext',
						'method'=> 'after_channel_entry_insert'
					])
					->get()
					->result_array();

		$configData = empty($query) ? '' : $query[0];

		$settings = unserialize($configData['settings']);

		return $settings;
	}

}