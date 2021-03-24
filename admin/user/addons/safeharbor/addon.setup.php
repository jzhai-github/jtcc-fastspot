<?php

require_once 'autoload.php';
$addonJson = json_decode(file_get_contents(__DIR__ . '/addon.json'));

return array(
    'name'              => $addonJson->name,
    'description'       => $addonJson->description,
    'version'           => $addonJson->version,
    'namespace'         => $addonJson->namespace,
    'author'            => 'EEHarbor',
    'author_url'        => 'http://eeharbor.com/safeharbor',
    'docs_url'          => 'https://eeharbor.com/safeharbor/documentation',
    'settings_exist' => true,
    'models'      => array(
        'Settings'    => 'Model\Settings',
        'Backups'     => 'Model\Backups',
    )
);
