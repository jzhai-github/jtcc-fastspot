<?php

// The config is deprecated, in favor of moving everything in to settings.
// This will make your life much easier. If you have an old config, leave
// it here; when you update the extension, it will automatically build
// the settings from your config file. Once you have updated, you are safe
// to delete that file or leave as is.

$channel_config = array(
	// If Email, set type to email
	1 => array(
		'type'	=> 'email',
		'email'	=> 'test@example.com',
		'from'	=> 'override@example.com', // This will override the message_info from address
	),
	// If Member Group, set type to member_group and add the ID of the member group
	2 => array(
		'type' => 'member_group',
		'group_id' => 1,
	),
	// You can send to multiple member groups
	3 => array(
		'type' => 'member_group',
		'group_id' => [
			1,
			2,
			3,
		],
	),
	// Here is an array example
	4 => array(
		'type' => 'email',
		'email' => array(
			'test@example.com',
			'test2@example.com',
			'addabunch@example.com'
		),
	)
);

$message_info = array(
	'start' => 'An entry has been updated! See below for information.',
	'end' => 'Check the revision history to see the changes that have been made.',
	'domain' => 'http://example.com',
	'from'	=> 'from@example.com'
);

$skip_fields = array(
	'entry_date',
	'submit'
);