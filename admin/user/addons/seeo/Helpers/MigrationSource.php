<?php

namespace EEHarbor\Seeo\Helpers;

use EEHarbor\Seeo\FluxCapacitor\FluxCapacitor;
use EllisLab\ExpressionEngine\Library\Data\Collection;

class MigrationSource
{
    public $shortname;
    public $name;
    public $module_name;
    public $migrationPaths = array();
    public $data;
    public $remove_data = array();

    public function __construct($shortname)
    {
        $this->shortname = $shortname;
        $this->setData();
        $this->flux = new FluxCapacitor;

        $this->name                    = $this->data['name'];
        $this->channel_based_migration = $this->data['channel_based_migration'];
        $this->setKnownMigrationPaths();
        $this->setRemoveData();
    }

    public static function getAllSources()
    {
        $sources = new Collection();

        // SEO Lite
        if (ee()->db->table_exists('seolite_content')) {
            $sources[] = new MigrationSource('seo_lite');
        }

        // Better Meta
        if (ee()->db->table_exists('nsm_better_meta')) {
            $sources[] = new MigrationSource('better_meta');
        }

        // ZC Meta
        if (ee()->db->table_exists('zc_meta')) {
            $sources[] = new MigrationSource('zc_meta');
        }

        return $sources;
    }

    public function setMigrationData($migration_data)
    {
        if ($this->channel_based_migration) {
            $this->migration_data = array();

            foreach ($migration_data as $channel_id => $channel_migration_meta_data) {
                if (!isset($channel_migration_meta_data['seeo_migrate'])) {
                    unset($migration_data[$channel_id]);
                } else {
                    $channel = ee()->db->select('channel_title')
                        ->where('channel_id', $channel_id)
                        ->from('channels')
                        ->get()
                        ->row();

                    $this->migration_data[$channel_id]['channel_id'] = $channel_id;
                    $this->migration_data[$channel_id]['name']       = $channel->channel_title;
                    $this->migration_data[$channel_id]['active']     = true;
                    $this->migration_data[$channel_id]['settings']   = $channel_migration_meta_data;
                }
            }

            if (empty($migration_data)) {
                $this->flux->flashData('message_error', 'No channels selected.');
                ee()->functions->redirect($this->flux->moduleURL('setup_migrate'));
            }
        } else {
            if (empty($migration_data)) {
                $this->flux->flashData('message_error', 'Select something to migrate.');
                ee()->functions->redirect($this->flux->moduleURL('setup_migrate'));
            }

            $this->migration_data['meta_data']['name']      = 'Meta Data';
            $this->migration_data['meta_data']['shortname'] = 'meta_data';
            $this->migration_data['meta_data']['active']    = isset($migration_data['meta_data']);

            $this->migration_data['template_pages']['name']      = 'Template Page Meta Data';
            $this->migration_data['template_pages']['shortname'] = 'template_pages';
            $this->migration_data['template_pages']['active']    = isset($migration_data['template_pages']);

            $this->migration_data['settings']['name']      = 'Settings';
            $this->migration_data['settings']['shortname'] = 'settings';
            $this->migration_data['settings']['active']    = isset($migration_data['settings']);
        }
    }

    public function doMigration($migration_data)
    {
        $migrate_function = 'migrate_' . $this->shortname;

        $this->$migrate_function($migration_data);

        // Create a migration object and log it to the DB.
        $migration = ee('Model')->make('seeo:Migration');

        // Set all the migration data and save it
        $migration->shortname = $this->shortname;
        $migration->date      = date("Y-m-d H:i:s");
        $migration->settings  = json_encode($migration_data);
        $migration->save();

        return true;
    }

    public function doInitialMigration()
    {
        $migrate_function = 'migrate_initial_' . $this->shortname;

        $this->$migrate_function();

        // Create a migration object and log it to the DB.
        $migration = ee('Model')->make('seeo:Migration');

        // Set all the migration data and save it
        $migration->shortname = $this->shortname;
        $migration->date      = date("Y-m-d H:i:s");
        $migration->settings  = json_encode(array("Initial Migration for $this->shortname"));
        $migration->save();

        return true;
    }

    private function migrate_seo_lite($migration_data)
    {
        foreach ($migration_data as $channel_id => $data) {
            // set the settings
            $channel_settings = $data['settings'];

            $updated_entries = 0;
            $new_entries     = 0;

            // --------------------------------------
            // Look up all the SEO Lite Records
            // --------------------------------------
            $seo_lite_records = ee()->db->from('seolite_content')->get()->result();

            foreach ($seo_lite_records as $seo_lite_record) {
                // --------------------------------------
                // Find the entries in the selected channel
                // --------------------------------------
                if (!$entry = ee()->db->from('channel_titles')
                    ->where('entry_id', $seo_lite_record->entry_id)
                    ->where('channel_id', $channel_id)
                    ->get()
                    ->row()
                ) {
                    continue;
                } else {
                    $to_copy = array();

                    if (isset($channel_settings['title'])) {
                        // Migrate the title
                        $to_copy['title'] = $seo_lite_record->title;
                    }

                    if (isset($channel_settings['keywords'])) {
                        // Migrate the keywords
                        $to_copy['keywords'] = $seo_lite_record->keywords;
                    }

                    if (isset($channel_settings['description'])) {
                        // Migrate the description
                        $to_copy['description'] = $seo_lite_record->description;
                    }

                    if (isset($channel_settings['title_to_og_title'])) {
                        // Copy title to OG title
                        $to_copy['og_title'] = $seo_lite_record->title;
                    }

                    if (isset($channel_settings['title_to_twitter_title'])) {
                        // Copy title to Twitter Title
                        $to_copy['twitter_title'] = $seo_lite_record->title;
                    }

                    if (isset($channel_settings['description_to_og_description'])) {
                        // Copy description to OG description
                        $to_copy['og_description'] = $seo_lite_record->description;
                    }

                    if (isset($channel_settings['description_to_twitter_description'])) {
                        // Copy description to Twitter description
                        $to_copy['twitter_description'] = $seo_lite_record->description;
                    }

                    $where = array(
                        'entry_id' => $seo_lite_record->entry_id,
                    );

                    $query = ee()->db->get_where('seeo', $where);

                    if ($query->num_rows()) {
                        $to_copy['entry_id'] = $seo_lite_record->entry_id;
                        $to_copy['site_id']  = $seo_lite_record->site_id;

                        ee()->db->where($where);
                        ee()->db->update('seeo', $to_copy);
                        $updated_entries++;
                    } else {
                        $to_copy['entry_id']   = $seo_lite_record->entry_id;
                        $to_copy['site_id']    = $seo_lite_record->site_id;
                        $to_copy['channel_id'] = $channel_id;

                        ee()->db->insert('seeo', $to_copy);

                        $new_entries++;
                    }
                }
            }
        }
    }

    private function migrate_initial_better_meta()
    {
        $better_meta_settings = ee()->db->from('exp_nsm_addon_settings')
            ->where('site_id', ee()->config->item('site_id'))
            ->where('addon_id', 'nsm_better_meta')
            ->limit(1)
            ->get();

        $better_meta_settings = $this->object_to_array(json_decode($better_meta_settings->row('settings')));

        $default_robots = '';
        $default_robots .= ($better_meta_settings["default_site_meta"]['robots_index'] === 'n') ? 'NOINDEX, ' : 'INDEX, ';
        $default_robots .= ($better_meta_settings["default_site_meta"]['robots_follow'] === 'n') ? 'NOFOLLOW' : 'FOLLOW';

        $seeo_settings = ee()->seeo_settings->get();
        $seeo_settings['title'] = $better_meta_settings['default_site_meta']['site_title'];
        $seeo_settings['description'] = $better_meta_settings['default_site_meta']['description'];
        $seeo_settings['keywords'] = $better_meta_settings['default_site_meta']['keywords'];
        $seeo_settings['author'] = $better_meta_settings['default_site_meta']['author'];
        $seeo_settings['template'] = $better_meta_settings['meta_template'];

        $channels = array();
        foreach ($better_meta_settings['channels'] as $channel_id => $settings) {
            $channel_exists = (bool) ee()->db->select('channel_name')
                ->from('channels')
                ->where('channel_id', $channel_id)
                ->where('site_id', ee()->config->item('site_id'))
                ->get()->num_rows;

            // Sometimes theres
            if (! $channel_exists) {
                continue;
            }

            $settingData = array(
                'site_id'                  => ee()->config->item('site_id'),
                'channel_id'               => $channel_id,
                'file_manager'             => 'default',
                'divider'                  => '-',
                'enabled'                  => 1, // We change this, as well as add the hide fields below
                'robots'                   => $default_robots,
                'sitemap_priority'         => $settings['sitemap_priority'],
                'sitemap_change_frequency' => strtolower($settings['sitemap_change_frequency']),
                'sitemap_include'          => $settings['sitemap_include'],
                'template'                 => $seeo_settings['template'],
            );

            // Create or update channel settings
            $this->createOrUpdateSettings($settingData, $channel_id);
        }

        // Update the default settings
        $this->createOrUpdateSettings($seeo_settings, 0);

        // ee()->db->where('class', 'Seeo_ext');
        // ee()->db->update('extensions', array('settings' => serialize($seeo_settings)));

        return true;
    }

    private function createOrUpdateSettings($settings, $channel_id=0)
    {
        // Check if we already have these settings saved.
        $existing_settings = ee()->db->select('id')->from('seeo_default_settings')
            ->where(
                array(
                'site_id' => ee()->config->item('site_id'),
                'channel_id' => $channel_id)
            )->get()->row();

        if (!empty($existing_settings)) {
            $setting_id = $existing_settings->id;
            ee()->db->where('id', $setting_id);
            ee()->db->update('seeo_default_settings', $settings);
        } else {
            ee()->db->insert('seeo_default_settings', $settings);
        }

        return true;
    }

    private function migrate_better_meta($migration_data)
    {
        foreach ($migration_data as $channel_id => $data) {
            // set the settings
            $channel_settings = $data['settings'];

            $updated_entries = 0;
            $new_entries     = 0;

            // --------------------------------------
            // Look up all the Better Meta Records for this channel
            // --------------------------------------
            $better_meta_records = ee()->db->where('channel_id', $channel_id)
                ->from('nsm_better_meta')
                ->get()
                ->result();

            foreach ($better_meta_records as $better_meta_record) {
                // --------------------------------------
                // Find the entries in the selected channel
                // --------------------------------------
                $entry = ee()->db->from('channel_titles')
                    ->where('entry_id', $better_meta_record->entry_id)
                    ->where('channel_id', $channel_id)
                    ->get()
                    ->row();

                if (! $entry) {
                    // no entry exists. Bail
                    continue;
                } else {
                    $to_copy = array();

                    if (isset($channel_settings['title'])) {
                        // Migrate the title
                        $to_copy['title'] = $better_meta_record->title;
                    }

                    if (isset($channel_settings['keywords'])) {
                        // Migrate the keywords
                        $to_copy['keywords'] = $better_meta_record->keywords;
                    }

                    if (isset($channel_settings['description'])) {
                        // Migrate the description
                        $to_copy['description'] = $better_meta_record->description;
                    }

                    if (isset($channel_settings['author'])) {
                        // Migrate the author
                        $to_copy['author'] = $better_meta_record->author;
                    }

                    if (isset($channel_settings['canonical_url'])) {
                        // Migrate the canonical url
                        $to_copy['canonical_url'] = $better_meta_record->canonical_url;
                    }

                    if (isset($channel_settings['title_to_og_title'])) {
                        // Copy title to OG title
                        $to_copy['og_title'] = $better_meta_record->title;
                    }

                    if (isset($channel_settings['title_to_twitter_title'])) {
                        // Copy title to Twitter Title
                        $to_copy['twitter_title'] = $better_meta_record->title;
                    }

                    if (isset($channel_settings['description_to_og_description'])) {
                        // Copy description to OG description
                        $to_copy['og_description'] = $better_meta_record->description;
                    }

                    if (isset($channel_settings['description_to_twitter_description'])) {
                        // Copy description to Twitter description
                        $to_copy['twitter_description'] = $better_meta_record->description;
                    }

                    if (isset($channel_settings['sitemap_include'])) {
                        // Migrate sitemap preference
                        $to_copy['sitemap_include'] = $better_meta_record->sitemap_include;
                    }

                    if (isset($channel_settings['sitemap_priority'])) {
                        // Migrate sitemap priority
                        $to_copy['sitemap_priority'] = $better_meta_record->sitemap_priority;
                    }

                    if (isset($channel_settings['sitemap_change_frequency'])) {
                        // Migrate sitemap change frequency
                        $to_copy['sitemap_change_frequency'] = strtolower($better_meta_record->sitemap_change_frequency);
                    }

                    if (isset($channel_settings['robots'])) {
                        // Migrate robot settings
                        $robots_index  = $better_meta_record->robots_index == 'n' ? 'NOINDEX' : 'INDEX';
                        $robots_follow = $better_meta_record->robots_follow == 'n' ? 'NOFOLLOW' : 'FOLLOW';

                        $to_copy['robots'] = $robots_index . ', ' . $robots_follow;
                    }

                    $where = array(
                        'entry_id' => $better_meta_record->entry_id,
                    );

                    $query = ee()->db->get_where('seeo', $where);

                    if ($query->num_rows()) {
                        $to_copy['entry_id'] = $better_meta_record->entry_id;
                        $to_copy['site_id']  = $better_meta_record->site_id;

                        ee()->db->where($where);
                        ee()->db->update('seeo', $to_copy);
                        $updated_entries++;
                    } else {
                        $to_copy['entry_id']   = $better_meta_record->entry_id;
                        $to_copy['site_id']    = $better_meta_record->site_id;
                        $to_copy['channel_id'] = $channel_id;

                        ee()->db->insert('seeo', $to_copy);

                        $new_entries++;
                    }
                }
            }
        }
    }

    private function migrate_zc_meta($migration_data)
    {
        // Migrate the zc meta listings
        if (isset($migration_data['listings']) && $migration_data['listings']['active']) {
            $zc_listings = ee('Model')->get('zc_meta:Listing')->all();

            // Get all the listings and loop through making new ones
            foreach ($zc_listings as $zc_listing) {
                // Find or create a listing object
                $seeo_template_page = ee('Model')->get('seeo:TemplatePage')->filter('path', '==', $zc_listing->path)->first();
                if (empty($seeo_template_page) || count($seeo_template_page) === 0) {
                    $seeo_template_page = ee('Model')->make('seeo:TemplatePage');
                }

                // Find or create a meta object
                $seeo_meta = $seeo_template_page->Meta;
                if (empty($seeo_meta) || count($seeo_meta) === 0) {
                    $seeo_meta = ee('Model')->make('seeo:Meta');
                }

                // get the zc_meta data and unset the id
                $zc_meta = $zc_listing->Meta;
                if (empty($zc_meta) || count($zc_meta) === 0) {
                    $zc_meta = ee('Model')->make('zc_meta:Meta');
                }
                $zc_meta = $zc_meta->toArray();
                unset($zc_meta['id']);

                // Set the new meta data and save it
                $seeo_meta->set($zc_meta);
                $seeo_meta->save();

                // Add the meta_id, set the info, then save it
                $zc_listing_data            = $zc_listing->toArray();
                $zc_listing_data['meta_id'] = $seeo_meta->id;

                $seeo_template_page->set($zc_listing_data);
                $seeo_template_page->save();
            }

            // exit;
        }

        // Migrate the zc meta data
        if (isset($migration_data['meta_data']) && $migration_data['meta_data']['active']) {
            $zc_meta_entries = ee('Model')->get('zc_meta:Meta')->filter('entry_id', '!=', 0)->all();

            // Loop through all zc meta entries that have an entry_id (not a template page) and migrate them to seeo
            foreach ($zc_meta_entries as $zc_meta_entry) {
                // Get all the zc data and remove the id
                $zc_data = $zc_meta_entry->toArray();
                unset($zc_data['id']);

                // Get a SEEO object, from existing data, or a new one
                $seeo_meta = ee('Model')->get('seeo:Meta')->filter('entry_id', '==', $zc_meta_entry->entry_id)->first();

                if (empty($seeo_meta) || count($seeo_meta) === 0) {
                    $seeo_meta = ee('Model')->make('seeo:Meta');
                }

                // Set the data from zc_meta
                $seeo_meta->set($zc_data);
                $seeo_meta->save();
            }
        }

        // Migrate the zc meta settings
        if (isset($migration_data['settings']) && $migration_data['settings']['active']) {
            $settings = array();

            // Get the settings from zc_meta
            $zc_meta_query = ee()->db->select('settings')
                ->from('extensions')
                ->where('class', 'Zc_meta_ext')
                ->limit(1)
                ->get();

            // Unserialize the settings array and build a new array with seeo in the key
            $zc_settings = (array) @unserialize($zc_meta_query->row('settings'));
            foreach ($zc_settings as $key => $setting) {
                $settings[str_replace('zc_meta', 'seeo', $key)] = $setting;
            }

            ee()->db->where('class', 'Seeo_ext');
            ee()->db->update('extensions', array('settings' => serialize($settings)));
        }
    }

    private function setKnownMigrationPaths()
    {
        foreach ($this->data['migrations'] as $migration_name => $migration_data) {
            $migrationPath          = new \stdClass();
            $migrationPath->name    = $migration_name;
            $migrationPath->from    = $migration_data['from_text'];
            $migrationPath->to      = $migration_data['to_text'];
            $migrationPath->options = json_decode(json_encode($migration_data['options']), false);

            $this->migrationPaths[] = $migrationPath;
        }
    }

    private function setRemoveData()
    {
        $module_member_groups_table = version_compare(APP_VER, '6.0', '>=') ? 'module_member_roles' : 'module_member_groups';
        $this->remove_data['tabs'] = array();
        $this->remove_data['tables'] = array();
        $this->remove_data['rows'] = array();
        $this->remove_data['rows']['actions'] = array();
        $this->remove_data['rows']['extensions'] = array();
        $this->remove_data['rows']['modules'] = array();
        $this->remove_data['rows'][$module_member_groups_table] = array();

        switch ($this->shortname) {
            case 'seo_lite':
                $this->module_name = 'Seo_lite';
                $this->remove_data['tabs'][] = 'SEO Lite';
                $this->remove_data['tables'][] = 'seolite_content';
                $this->remove_data['tables'][] = 'seolite_config';
                $this->remove_data['tables'][] = 'publisher_seolite_content';
                break;

            case 'better_meta':
                $this->module_name = 'Nsm_better_meta';
                $this->remove_data['tabs'][] = 'NSM Better Meta';
                $this->remove_data['tables'][] = 'nsm_addon_settings';
                $this->remove_data['tables'][] = 'nsm_better_meta';
                $this->remove_data['rows']['extensions'][] = array('field' => 'class', 'value' => 'Nsm_better_meta_ext');
                $this->remove_data['rows']['extensions'][] = array('field' => 'class', 'value' => 'Nsm_better_meta_ext');

                ee()->db->select('field_id, legacy_field_data');
                $query = ee()->db->get_where('channel_fields', array('field_type' => 'nsm_better_meta'));

                foreach ($query->result_array() as $row) {
                    $field_id = $row['field_id'];

                    if (!empty($row['legacy_field_data']) && $row['legacy_field_data'] === 'n') {
                        $this->remove_data['tables'][] = 'channel_data_field_' . $field_id;
                    } else {
                        // @TODO: Drop field columns
                    }

                    $this->remove_data['rows']['channel_field_groups_fields'][] = array('field' => 'field_id', 'value' => $field_id);
                    $this->remove_data['rows']['channels_channel_fields'][] = array('field' => 'field_id', 'value' => $field_id);
                    $this->remove_data['rows']['channel_fields'][] = array('field' => 'field_id', 'value' => $field_id);
                }
                break;

            case 'zc_meta':
                break;
        }

        ee()->db->select('module_id');
        $query = ee()->db->get_where('modules', array('module_name' => $this->module_name));

        // Add in the stuff that's generic for any source like extensions, fieldtypes, etc.
        $this->remove_data['rows']['modules'][] = array('field' => 'module_name', 'value' => $this->module_name);
        $this->remove_data['rows'][$module_member_groups_table][] = array('field' => 'module_id', 'value' => $query->row('module_id'));
        $this->remove_data['rows']['actions'][] = array('field' => 'class', 'value' => $this->module_name);
        $this->remove_data['rows']['actions'][] = array('field' => 'class', 'value' => $this->module_name . '_mcp');
    }

    public function doRemoveSource()
    {
        ee()->load->dbforge();
        ee()->load->library('layout');

        ee()->db->select('module_id');
        $query = ee()->db->get_where('modules', array('module_name' => $this->module_name));

        $module_member_groups_table = version_compare(APP_VER, '6.0', '>=') ? 'module_member_roles' : 'module_member_groups';
        ee()->db->where('module_id', $query->row('module_id'));
        ee()->db->delete($module_member_groups_table);

        ee()->db->where('module_name', $this->module_name);
        ee()->db->delete('modules');

        ee()->db->where('class', $this->module_name);
        ee()->db->delete('actions');

        ee()->db->where('class', $this->module_name.'_mcp');
        ee()->db->delete('actions');

        ee()->db->where('class', $this->module_name.'_ext');
        ee()->db->delete('extensions');

        ee()->db->where('name', $this->module_name);
        ee()->db->delete('fieldtypes');

        ee()->db->select('field_id, legacy_field_data');
        $query = ee()->db->get_where('channel_fields', array('field_type' => 'nsm_better_meta'));

        foreach ($query->result_array() as $row) {
            $field_id = $row['field_id'];

            if (!empty($row['legacy_field_data']) && $row['legacy_field_data'] === 'n') {
                ee()->dbforge->drop_table('channel_data_field_' . $field_id);
            } else {
                // @TODO: Drop field columns
            }

            ee()->db->where('field_id', $field_id);
            ee()->db->delete('channel_field_groups_fields');

            ee()->db->where('field_id', $field_id);
            ee()->db->delete('channels_channel_fields');

            ee()->db->where('field_id', $field_id);
            ee()->db->delete('channel_fields');
        }

        ee()->db->where('field_type', $this->module_name);
        ee()->db->delete('channel_fields');

        switch ($this->module_name) {
            case 'Nsm_better_meta':
                $tabs = array(
                    'meta' => array(
                        'visible'     => 'true',
                        'collapse'    => 'false',
                        'htmlbuttons' => 'false',
                        'width'       => '100%'
                    )
                );

                ee()->dbforge->drop_table('nsm_addon_settings');
                ee()->dbforge->drop_table('nsm_better_meta');
                ee()->layout->delete_layout_tabs($tabs, 'nsm_better_meta');
                break;

            case 'Seo_lite':
                $tabs = array(
                    'seo_lite_title' => array(
                        'visible'     => 'true',
                        'collapse'    => 'false',
                        'htmlbuttons' => 'false',
                        'width'       => '100%'
                    ),
                    'seo_lite_keywords' => array(
                        'visible'     => 'true',
                        'collapse'    => 'false',
                        'htmlbuttons' => 'false',
                        'width'       => '100%'
                    ),
                    'seo_lite_description' => array(
                        'visible'     => 'true',
                        'collapse'    => 'false',
                        'htmlbuttons' => 'false',
                        'width'       => '100%',
                    )
                );

                ee()->dbforge->drop_table('seolite_content');
                ee()->dbforge->drop_table('seolite_config');
                ee()->layout->delete_layout_tabs($tabs, 'seo_lite');
                break;
        }

        return true;
    }

    private function setData()
    {
        $data = array(
            'seo_lite'    => array(
                'name'                    => 'SEO Lite',
                'channel_based_migration' => true,
                'migrations'              => array(
                    'title'       => array(
                        'from_text' => 'Title',
                        'to_text'   => 'Title',
                        'options'   => array(
                            array(
                                'name' => 'title_to_og_title',
                                'text' => 'Copy to Open Graph Title?',
                            ),
                            array(
                                'name' => 'title_to_twitter_title',
                                'text' => 'Copy to Twitter Title?',
                            ),
                        ),
                    ),
                    'keywords'    => array(
                        'from_text' => 'Keywords',
                        'to_text'   => 'Keywords',
                        'options'   => array(),
                    ),
                    'description' => array(
                        'from_text' => 'Description',
                        'to_text'   => 'Description',
                        'options'   => array(
                            array(
                                'name' => 'description_to_og_description',
                                'text' => 'Copy to Open Graph Description?',
                            ),
                            array(
                                'name' => 'description_to_twitter_description',
                                'text' => 'Copy to Twitter Description?',
                            ),
                        ),
                    ),
                ),
            ),
            'better_meta' => array(
                'name'                    => 'Better Meta',
                'channel_based_migration' => true,
                'migrations'              => array(
                    'title'                    => array(
                        'from_text' => 'Title',
                        'to_text'   => 'Title',
                        'options'   => array(
                            array(
                                'name' => 'title_to_og_title',
                                'text' => 'Copy to Open Graph Title?',
                            ),
                            array(
                                'name' => 'title_to_twitter_title',
                                'text' => 'Copy to Twitter Title?',
                            ),
                        ),
                    ),
                    'keywords'                 => array(
                        'from_text' => 'Keywords',
                        'to_text'   => 'Keywords',
                        'options'   => array(),
                    ),
                    'description'              => array(
                        'from_text' => 'Description',
                        'to_text'   => 'Description',
                        'options'   => array(
                            array(
                                'name' => 'description_to_og_description',
                                'text' => 'Copy to Open Graph Description?',
                            ),
                            array(
                                'name' => 'description_to_twitter_description',
                                'text' => 'Copy to Twitter Description?',
                            ),
                        ),
                    ),
                    'author'                   => array(
                        'from_text' => 'Author',
                        'to_text'   => 'Author',
                        'options'   => array(),
                    ),
                    'canonical_url'            => array(
                        'from_text' => 'Canonical URL',
                        'to_text'   => 'Canonical URL',
                        'options'   => array(),
                    ),
                    'robots'                   => array(
                        'from_text' => 'Robots Metadata',
                        'to_text'   => 'Robots',
                        'options'   => array(),
                    ),
                    'sitemap_include'          => array(
                        'from_text' => 'Include in Sitemap',
                        'to_text'   => 'Include in Sitemap',
                        'options'   => array(),
                    ),
                    'sitemap_priority'         => array(
                        'from_text' => 'Sitemap',
                        'to_text'   => 'Sitemap Priority',
                        'options'   => array(),
                    ),
                    'sitemap_change_frequency' => array(
                        'from_text' => 'Change Frequency',
                        'to_text'   => 'Change Frequency',
                        'options'   => array(),
                    ),
                ),
            ),
            'zc_meta'     => array(
                'name'                    => 'ZC Meta',
                'channel_based_migration' => false,
                'migrations'              => array(),
            ),
        );

        $this->data = $data[$this->shortname];
    }

    public function object_to_array($obj)
    {
        if (is_object($obj)) {
            $obj = (array) $obj;
        }
        if (is_array($obj)) {
            $new = array();
            foreach ($obj as $key => $val) {
                $new[$key] = $this->object_to_array($val);
            }
        } else {
            $new = $obj;
        }
        return $new;
    }
}
