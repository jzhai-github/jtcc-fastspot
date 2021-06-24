<?php

$lang = [
    'Ferret_module_name' => 'Ferret',
    'Ferret_module_description' => 'Integration support for Algolia',

    // Index
    'engine' => 'Engine',
    'engine_desc' => 'Select which indexing engine you would like to use:',

    // Algolia
    'algolia_credentials' => 'Algolia Credentials',
    'app_id' => 'Application ID',
    'app_id_desc' => '',
    'search_key' => 'Search Only Key',
    'search_key_desc' => 'Search only key for the indexing engine',
    'admin_key' => 'Admin API Key',
    'admin_key_desc' => 'Admin API key for the indexing engine',

    // Options
    'options' => 'Options',
    'strip_ee_tags' => 'Strip out ExpressionEngine tags',
    'strip_ee_tags_desc' => 'Remove all content from either Rich Text or Wygwam fields surrounded by curly braces.',
    'integrations' => 'Enabled Integrations',
    'integrations_desc' => 'Select which core integrations to enable during indexing',

    // Indexes
    'indexes' => 'Indexes',
    'create_index' => 'Create an Index',
    'create_index_desc' => 'Enter the name of the new index you would like to create (this will be forced to lowercase).',
    'delete_indexes' => 'Delete Index(es)',
    'delete_indexes_desc' => 'Select which indexes you would like to delete. (Note: This does not delete the index in the search engine.)',

    // Fields
    'fields' => 'Field Settings',
    'fields_for_indexing' => 'Fields for Indexing by channel',
    'fields_save' => 'Save',
    'fields_working' => 'Saving field settings',

    // Categories
    'categories' => 'Category Settings',
    'categories_for_indexing' => 'Foobar',
    'categories_save' => 'Save',
    'categories_working' => 'Saving category settings',

    // Mapping
    'mapping' => 'Mapping Settings',
    'mapping_save' => 'Save',
    'mapping_working' => 'Saving mapping settings',

    // Paths
    'paths' => 'Path Settings',
    'paths_desc' => 'Specify the url path for entries in this channel. You may use the EE tags {base_url} and {url_title}.<br />If left blank Ferret will attempt to use the Site Pages array (useful if you are using Structure).',
    'paths_save' => 'Save',
    'paths_working' => 'Saving paths settings',

    // Build
    'build' => 'Build Index',
    'build_save' => 'Build',
    'build_working' => 'Building out the index',
    'build_index' => 'Caution: Performing this action will clear out and completely rebuild the current index.',

    // Clear
    'clear' => 'Clear Index',
    'clear_save' => 'Clear',
    'clear_working' => 'Clearing out the index',
    'clear_index' => 'Caution: Performing this action will remove all objects from the current index.',
];
