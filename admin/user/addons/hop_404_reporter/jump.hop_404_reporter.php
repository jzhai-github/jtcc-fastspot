<?php

use ExpressionEngine\Service\JumpMenu\AbstractJumpMenu;

class Hop_404_Reporter_jump extends AbstractJumpMenu
{
	protected static $items = [
		'license' => [
			'icon' => 'fa-lock',
			'command' => 'license',
			'command_title' => '<b>License</b>',
			'dynamic' => false,
			'requires_keyword' => false,
			'target' => 'license'
		],
		'index' => [
			'icon' => 'fa-list',
			'command' => 'index',
			'command_title' => '<b>Hop 404 URLs List</b>',
			'dynamic' => false,
			'requires_keyword' => false,
			'target' => 'index'
		],
		'display_emails' => [
			'icon' => 'fa-envelope-open-text',
			'command' => 'notifications list',
			'command_title' => '<b>Notifications List</b>',
			'dynamic' => false,
			'requires_keyword' => false,
			'target' => 'display_emails'
		],
		'create_notifications' => [
			'icon' => 'fa-plus',
			'command' => 'create 404 email notification',
			'command_title' => 'Create <b>Hop 404 Reporter Notifications</b>',
			'dynamic' => false,
			'requires_keyword' => false,
			'target' => 'add_email'
		],
		'settings' => [
			'icon' => 'fa-gear',
			'command' => 'settings',
			'command_title' => '<b>Settings</b>',
			'dynamic' => false,
			'requires_keyword' => false,
			'target' => 'settings'
		]
	];
}