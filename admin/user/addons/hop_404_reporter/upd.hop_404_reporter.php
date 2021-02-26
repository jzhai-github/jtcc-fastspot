<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once PATH_THIRD . 'hop_404_reporter/config.php';
require_once PATH_THIRD . 'hop_404_reporter/helper.php';
require_once PATH_THIRD . 'hop_404_reporter/Helper/HopInstallerHelper.php';

class Hop_404_reporter_upd extends HopInstallerHelper
{
    public $name        = HOP_404_REPORTER_NAME;
    public $version     = HOP_404_REPORTER_VERSION;
    public $short_name  = HOP_404_REPORTER_SHORT_NAME;
    public $class_name  = HOP_404_REPORTER_CLASS_NAME;

    public $has_cp_backend = 'y';
    public $has_publish_fields = 'n';

    public function __construct()
    {
        parent::__construct();

        $this->_dontInvertMe();
        $this->_checkLicense();
    }

    public function install()
    {
        parent::__construct();

        $this->_initialDbScripts();

        $data = [
            'module_name' => $this->class_name,
            'module_version' => $this->version,
            'has_cp_backend' => 'y',
            'has_publish_fields' => 'n'
        ];

        ee()->db->insert('modules', $data);

        return true;
    }
    
    public function update($current = '')
    {
        $this->_initialDbScripts($current);

        if (version_compare($current, '2.0.4', '<')) {
            // Add new field to hop_404_reporter_urls
            ee()->load->dbforge();
            ee()->dbforge->add_column('hop_404_reporter_urls', [
                'notification_to' => ['type' => 'MEDIUMTEXT']
            ]);
        }

        if (version_compare($current, '3.0.0', '<')) {
            // Add new field to hop_404_reporter_urls
            ee()->load->dbforge();
            ee()->dbforge->add_column('hop_404_reporter_emails', [
                'referrer' => ['type' => 'VARCHAR(1)']
            ]);
            ee()->dbforge->modify_column('hop_404_reporter_emails', [
                'interval' => [
                    'name' => 'frequency',
                    'type' => 'VARCHAR(255)',
                ]
            ]);

            $data = [
                // Add this add on to the menu manger
                'class'     => $this->class_name . '_ext',
                'method'    => 'addToMenu',
                'hook'      => 'cp_custom_menu',
                'settings'  => '',
                'priority'  => 10,
                'version'   => $this->version,
                'enabled'   => 'y'
            ];
            ee()->db->insert('extensions', $data);
        }
        
        return true;
    }
    
    public function uninstall()
    {
        parent::__construct();

        $this->_hopUninstall(['hop_404_reporter_urls', 'hop_404_reporter_emails', 'hop_404_reporter_settings']);
        
        return true;
    }

    private function _setupHop404ReporterTable()
    {
        ee()->load->dbforge();

        if (!ee()->db->table_exists('hop_404_reporter_urls')) {
            $fields = [
                'url_id'            => ['type' => 'INT', 'constraint' => '10', 'unsigned' => true, 'auto_increment' => true],
                'url'               => ['type' => 'VARCHAR', 'constraint' => '255'],
                'count'             => ['type' => 'INT', 'constraint' => '8', 'default' => 0],
                'referrer_url'      => ['type' => 'VARCHAR', 'constraint' => '255', 'null' => true],
                'last_occurred'     => ['type' => 'DATETIME'],
                'notification_to'   => ['type' => 'MEDIUMTEXT']
            ];

            ee()->dbforge->add_field($fields);
            ee()->dbforge->add_key('url_id', true);
            ee()->dbforge->add_key(['url', 'referrer_url']); //add a key on those two as they'll be unique

            ee()->dbforge->create_table('hop_404_reporter_urls');

            unset($fields);
        }

        // Make sure emoji support don't report this as an issue
        $table_name = ee()->db->dbprefix . 'hop_404_reporter_urls';
        $status = ee()->db->query('SHOW INDEX FROM `' . $table_name . '` WHERE Key_name = "url_referrer_url"')->result_array();

        if ($status[0]['Sub_part'] > '191') {
            ee()->db->query('DROP INDEX `url_referrer_url` ON `' . $table_name . '`');
            ee()->db->query('CREATE INDEX `url_referrer_url` ON `' . $table_name . '` (`url`(191), `referrer_url`(191));');
        }
        
        //emails table
        if (!ee()->db->table_exists('hop_404_reporter_emails')) {
            $fields = [
                'email_id'      => ['type' => 'INT', 'constraint' => '10', 'unsigned' => true, 'auto_increment' => true],
                'email_address' => ['type' => 'VARCHAR', 'constraint' => '255'],
                'url_to_match'  => ['type' => 'VARCHAR', 'constraint' => '255'],
                'referrer'      => ['type' => 'VARCHAR', 'constraint' => '1'],
                'frequency'      => ['type' => 'VARCHAR', 'constraint' => '255'],
                'parameter'     => ['type' => 'MEDIUMTEXT']
            ];

            ee()->dbforge->add_field($fields);
            ee()->dbforge->add_key('email_id', true);

            ee()->dbforge->create_table('hop_404_reporter_emails');

            unset($fields);
        }

        //Save settings (the default ones will be stored)
        Hop_404_reporter_helper::save_settings();
    }

    // ----------------------------------------
    //  Check if the initial settings record is there, and insert it if not
    // ----------------------------------------
    private function _initialDbScripts($current = null)
    {
        ee()->load->dbforge();

        if (!empty($current) && version_compare($current, '3.0.1', '<')) {

            $old_table = $this->_resetOldTable($this->short_name . '_settings');

            $settings = ee()->db->select(['setting_name', 'value'])->get($old_table)->result_array();

            if (ee()->dbforge->drop_table($old_table)) {
                ee()->dbforge->_reset();
            }
        }

        $this->_setupLicenseSettings();

        $this->_setupHop404ReporterTable();

        if (!empty($settings)) {
            ee()->db->insert_batch($this->short_name . '_settings', $settings);
        }
    }

    private function _resetOldTable($table)
    {
        ee()->load->dbforge();

        if (ee()->db->table_exists($table)) {
            // Rename the table
            ee()->dbforge->rename_table($table, $table . '_old');

            return $table . '_old';
        }
    }

    private function _dontInvertMe()
    {
        // ;)
        ee()->cp->add_to_head('<style type="text/css" media="screen">div[data-addon="' . $this->short_name . '"] .add-on-card__image img { filter: none; }</style>');
    }

    private function _hopUninstall($tables_to_be_removed = [])
    {
        ee()->db->select('module_id');
        $query = ee()->db->get_where('modules', ['module_name' => $this->class_name]);
        $module_id = $query->row('module_id');

        // Remove from allowed member groups/roles
        if (version_compare(APP_VER, '6', '<')) {
            ee()->db->delete('module_member_groups', ['module_id' => $module_id]);
        } else {
            ee()->db->delete('module_member_roles', ['module_id' => $module_id]);
        }

        // Remove from menu items
        ee()->db->delete('menu_items', ['name' => $this->name]);

        // Remove from actions
        ee()->db->delete('actions', ['class' => $this->class_name]);

        // Remove from plugins
        ee()->db->delete('plugins', ['plugin_package' => $this->short_name]);

        // Remove from fieldtypes
        ee()->db->delete('fieldtypes', ['name' => $this->short_name]);

        ee()->load->dbforge();
        // Drop the settings table
        $table_name = $this->short_name . '_settings';
        ee()->dbforge->drop_table($table_name);
        // Delete other tables
        foreach ($tables_to_be_removed as $remove_table) {
            ee()->dbforge->drop_table($remove_table);
        }

        // Remove our module :(
        ee()->db->delete('modules', ['module_name' => $this->class_name]);
    }

    private function _setupLicenseSettings()
    {
        ee()->load->dbforge();

        // We want to make sure we're not loading the table name from cache...
        ee()->db->data_cache['table_names'] = null;

        $table_name = $this->short_name . '_settings';
        if (!ee()->db->table_exists($table_name)) {
            ee()->dbforge->add_field([
                'setting_id'    => ['type' => 'int', 'constraint' => 4, 'unsigned' => true, 'auto_increment' => true],
                'setting_name'  => ['type' => 'varchar', 'constraint' => 32],
                'value'         => ['type' => 'text']
            ]);

            ee()->dbforge->add_key('setting_id', true);
            ee()->dbforge->create_table($table_name);
        }

        $license_setting = ee('Model')->get($this->short_name . ':Config')->filter('setting_name', 'license')->first();

        if ($license_setting === null) {
            $config = ee('Model')->make($this->short_name . ':Config');
            $config->setting_name = 'license';
            $config->value = 'n/a';
            $config->save();
        }
    }

    private function _checkLicense()
    {
        $table_name = $this->short_name . '_settings';

        try {
            if (ee()->db->table_exists($table_name)) {
                $license_valid = ee('Model')->get($this->short_name . ':Config')->filter('setting_name', 'license_valid')->first();

                if ( empty($license_valid) || $license_valid->value != 'valid license') {
                    if (version_compare(APP_VER, '6', '<')) {
                        $js = '<script>$(\'.tbl-wrap td:contains("' . $this->name . '")\').closest(\'tr\').find(\'.toolbar-wrap .toolbar\').append(\'<li class="txt-only"><a href="' . ee('CP/URL', 'addons/settings/' . $this->short_name . '/license') . '" class="no">Unlicensed</a>\');</script>';
                    } else {
                        $js = '<script>$(\'.add-on-card__title:contains("' . $this->name . '")\').closest(\'.add-on-card__text\').append(\'<a href="' . ee('CP/URL', 'addons/settings/' . $this->short_name . '/license') . '" class="st-closed">Unlicensed</a>\');</script>';
                    }
                    ee()->cp->add_to_foot($js);
                }
            }
        } catch (Exception $e) {
            // Make sure Hop License table is configured properly
            $js = '<script>$(\'.tbl-wrap td:contains("' . $this->name . '")\').closest(\'tr\').find(\'.toolbar-wrap .toolbar\').append(\'<li class="txt-only"><span class="no">Update needed</span>\');</script>';
            ee()->cp->add_to_foot($js);
        }
    }
}