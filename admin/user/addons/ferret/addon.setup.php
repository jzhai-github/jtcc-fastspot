<?php

define('FERRET_NAME', 'Ferret');
define('FERRET_VERSION', '1.0.0');

require 'vendor/autoload.php';

return [
    'author' => 'Foster Made',
    'author_url' => 'https://fostermade.co',
    'name' => FERRET_NAME,
    'description' => 'Integration support for Algolia',
    'version' => FERRET_VERSION,
    'namespace' => 'fostermade\ferret',
    'settings_exist' => true,
    'models' => [
        'FerretIndex' => 'Model\FerretIndex',
        'FerretObject' => 'Model\FerretObject',
        'FerretRecord' => 'Model\FerretRecord',
    ],
];
