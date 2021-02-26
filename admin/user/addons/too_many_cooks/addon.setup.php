<?php
include(PATH_THIRD.'/too_many_cooks/config.php');		
return array(
	'author' => 'Amphibian',
	'author_url' => 'https://amphibian.info',
	'description' => 'Alerts authors when opening an entry which is already being edited.',
	'docs_url' => '',
	'name' => 'Too Many Cooks',
	'namespace' => 'Amphibian\TooManyCooks',
	'services' => array(
		'Sessions' => 'Service\Sessions'
	),
    'settings_exist' => true,
	'version' => TOO_MANY_COOKS_VERSION
);