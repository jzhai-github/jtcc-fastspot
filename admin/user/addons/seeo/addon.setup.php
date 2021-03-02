<?php

include_once 'autoload.php';
$addonJson = json_decode(file_get_contents(__DIR__ . '/addon.json'));

if (!defined('SEEO_NAME')) {
    define('SEEO_NAME', $addonJson->name);
    define('SEEO_VERSION', $addonJson->version);
    define('SEEO_DOCS', 'https://eeharbor.com/seeo/documentation');
    define('SEEO_DESCRIPTION', $addonJson->description);
    define('SEEO_DEBUG', false);
}

$config['seeo_tab_title'] = SEEO_NAME;

return array(
    'name'           => $addonJson->name,
    'description'    => $addonJson->description,
    'version'        => $addonJson->version,
    'namespace'      => $addonJson->namespace,
    'author'         => 'EEHarbor',
    'author_url'     => 'https://eeharbor.com/seeo',
    'docs_url'       => 'https://eeharbor.com/seeo/documentation',
    'settings_exist' => true,
    'models'         => array(
        'Meta'      => 'Model\Meta',
        'TemplatePage'   => 'Model\TemplatePage',
        'Migration' => 'Model\Migration',
    ),
    'models.dependencies' => array(
        'Meta' => array(
            'ee:ChannelEntry',
        ),
        'TemplatePage' => array(
            'ee:Channel',
        ),
    ),
);
