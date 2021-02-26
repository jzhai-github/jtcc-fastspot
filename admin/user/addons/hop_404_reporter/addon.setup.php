<?php

require_once PATH_THIRD . 'hop_404_reporter/config.php';

return [
    'author'        => 'Hop Studios',
    'author_url'    => 'https://hopstudios.com',
    'name'          => HOP_404_REPORTER_NAME,
    'version'       => HOP_404_REPORTER_VERSION,
	'description'	=> 'Alerts you to 404 errors happening on your website -- which will help your site look good in search engines and satisfy your site visitors.',
	'docs_url'		=> 'https://hopstudios.com/software/hop_404_reporter/docs',
	'namespace'		=> 'HopStudios\Hop404Reporter',
    'models'        => [
        'Config' => 'Model\Config'
    ],
	'settings_exist' => true
];