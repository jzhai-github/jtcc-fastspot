<?php

require_once 'Conduit/Backup.php';
require_once 'ext.safeharbor.php';

use EEHarbor\Safeharbor\Conduit\Backup;
use EEHarbor\Safeharbor\FluxCapacitor\Base\Upd;

class Safeharbor_upd extends Upd
{
    public $has_cp_backend = 'y';
    public $has_publish_fields = 'n';

    public function __construct()
    {
        parent::__construct();

        $this->backupHelper = new Backup;
        $this->backups_directory =  PATH_THIRD.'safeharbor/backups';
    }

    public function install()
    {
        // create backup directory and secure with htaccess
        @mkdir($this->backups_directory);

        $htaccess = $this->backups_directory.'/.htaccess';
        @file_put_contents($htaccess, 'deny from all');

        // install module
        ee()->db->insert('modules', array(
            'module_name' => 'Safeharbor',
            'module_version' => $this->version,
            'has_cp_backend' => 'y',
            'has_publish_fields' => 'n'
        ));

        // create cron action
        ee()->db->insert('actions', array(
            'class' => 'Safeharbor',
            'method' => '_backup_cron',
        ));

        // Create tables
        $this->_createBackupsTable();
        $this->_createMessagesTable();
        $this->_createHashTable();
        $this->_createSettingsTable();
        $this->_createIndexesTable();

        $dirs_exist = $this->backupHelper->create_dir_structure('', 1);

        if (empty($dirs_exist)) {
            ee('CP/Alert')->makeBanner('box')
                    ->asIssue()
                    ->withTitle(lang('backup_path_not_writable_install'))
                    // ->addToBody('Test data gere')
                    ->defer();
        }

        return true;
    }

    public function update($current = '')
    {
        if ($current == '' or $current == $this->version) {
            return false;
        }

        ee()->load->dbforge();

        if (version_compare($current, '1.2', '<')) {
            ee()->db->query("CREATE TABLE IF NOT EXISTS `".ee()->db->dbprefix('safeharbor_indexes')."` (
                    `id` INT(7) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `backup_id` INT(6) UNSIGNED NOT NULL,
                    `file` varchar(512) NOT NULL ,
                PRIMARY KEY (`id`));");

            ee()->db->query("ALTER TABLE `".ee()->db->dbprefix('safeharbor_settings')."` ADD COLUMN amazon_s3_enabled int(1) NOT NULL DEFAULT '0';");
            ee()->db->query("ALTER TABLE `".ee()->db->dbprefix('safeharbor_settings')."` ADD COLUMN amazon_s3_access_key varchar(20) NOT NULL;");
            ee()->db->query("ALTER TABLE `".ee()->db->dbprefix('safeharbor_settings')."` ADD COLUMN amazon_s3_secret varchar(40) NOT NULL;");
            ee()->db->query("ALTER TABLE `".ee()->db->dbprefix('safeharbor_settings')."` ADD COLUMN amazon_s3_bucket varchar(256) NOT NULL;");

            ee()->db->query("ALTER TABLE `".ee()->db->dbprefix('safeharbor_backups')."` ADD COLUMN transferred_amazon_s3 int(1) NOT NULL DEFAULT '0';");

            //go ahead and remove all indexes of backups that have expired
            $results = ee()->db->query("SELECT DISTINCT(exp_safeharbor_indexes.backup_id) as backup_id FROM `exp_safeharbor_backups` JOIN exp_safeharbor_indexes ON exp_safeharbor_backups.backup_id = exp_safeharbor_indexes.backup_id WHERE exp_safeharbor_backups.status = 'Purged'");
            $results = $results->result();

            foreach ($results as $row) {
                ee()->db->delete('safeharbor_indexes', array('backup_id' => $row->backup_id));
            }
        }

        if (version_compare($current, '1.3', '<')) {
            ee()->db->query("ALTER TABLE `".ee()->db->dbprefix('safeharbor_settings')."` ADD COLUMN ftp_enabled int(1) NOT NULL DEFAULT '0';");
            ee()->db->query("ALTER TABLE `".ee()->db->dbprefix('safeharbor_settings')."` ADD COLUMN ftp_username varchar(20) NOT NULL;");
            ee()->db->query("ALTER TABLE `".ee()->db->dbprefix('safeharbor_settings')."` ADD COLUMN ftp_password varchar(256) NOT NULL;");
            ee()->db->query("ALTER TABLE `".ee()->db->dbprefix('safeharbor_settings')."` ADD COLUMN ftp_host varchar(128) NOT NULL;");
            ee()->db->query("ALTER TABLE `".ee()->db->dbprefix('safeharbor_settings')."` ADD COLUMN ftp_port int(5) NOT NULL;");
            ee()->db->query("ALTER TABLE `".ee()->db->dbprefix('safeharbor_settings')."` ADD COLUMN ftp_path varchar(256) NOT NULL;");
            ee()->db->query("ALTER TABLE `".ee()->db->dbprefix('safeharbor_backups')."` ADD COLUMN transferred_ftp int(1) NOT NULL DEFAULT '0';");

            ee()->db->query("ALTER TABLE `".ee()->db->dbprefix('safeharbor_settings')."` ADD COLUMN storage_path varchar(256) NOT NULL DEFAULT '0';");
            ee()->db->query("ALTER TABLE `".ee()->db->dbprefix('safeharbor_backups')."` ADD COLUMN note text NOT NULL;");
            ee()->db->query("ALTER TABLE `".ee()->db->dbprefix('safeharbor_settings')."` ADD COLUMN disable_remote int(1) NOT NULL DEFAULT '0';");

            ee()->db->query("ALTER TABLE `".ee()->db->dbprefix('safeharbor_settings')."` MODIFY amazon_s3_access_key varchar(256);");
            ee()->db->query("ALTER TABLE `".ee()->db->dbprefix('safeharbor_settings')."` MODIFY amazon_s3_secret varchar(256);");
            ee()->db->query("UPDATE `".ee()->db->dbprefix('actions')."` SET method='_backup_cron' WHERE class='Safeharbor' AND method='transfer_status'");
        }

        if (version_compare($current, '1.3.1', '<')) {
            if (!ee()->db->field_exists('disable_remote', 'safeharbor_settings')) {
                ee()->db->query("ALTER TABLE `".ee()->db->dbprefix('safeharbor_settings')."` ADD COLUMN disable_remote INT(1) NOT NULL DEFAULT '0';");
            }
        }

        if (version_compare($current, '1.3.3', '<')) {
            ee()->db->select('backup_id');
            ee()->db->from('safeharbor_backups');
            ee()->db->where('status', 'Purged');
            $results = ee()->db->get();
            $results = $results->result();

            foreach ($results as $row) {
                ee()->db->delete('safeharbor_backups', array('backup_id' => $row->backup_id));
                ee()->db->delete('safeharbor_indexes', array('backup_id' => $row->backup_id));
            }
        }

        if (version_compare($current, '1.3.3.1', '<')) {
            ee()->db->query("ALTER TABLE `".ee()->db->dbprefix('safeharbor_settings')."` MODIFY ftp_username varchar(128);");
        }

        if (version_compare($current, '2.1.0', '<')) {
            $fields = array(
                'backup_dbsize' => array(
                    'type'       => 'varchar',
                    'constraint' => 32,
                    'default'    => '',
                    'after'      => 'backup_size'
                )
            );

            if (!ee()->db->field_exists('backup_dbsize', 'safeharbor_backups')) {
                ee()->dbforge->add_column('safeharbor_backups', $fields);
            }

            // Make sure they have this column as it was incorrectly added into a previous update that may never have fired.
            if (!ee()->db->field_exists('amazon_s3_bucket', 'safeharbor_settings')) {
                $fields = array(
                    'amazon_s3_bucket'  => array(
                        'type'       => 'varchar',
                        'constraint' => '255',
                        'default'    => '',
                        'after'      => 'amazon_s3_secret'
                    )
                );

                ee()->dbforge->add_column('safeharbor_settings', $fields);
            }
        }

        if (version_compare($current, '2.2.0', '<')) {
            $ext = new Safeharbor_ext();
            $ext->activate_extension();
        }

        if (version_compare($current, '2.2.2', '<')) {
            if (!ee()->db->field_exists('amazon_s3_endpoint', 'safeharbor_settings')) {
                $fields = array(
                    'amazon_s3_endpoint'    => array(
                        'type'       => 'varchar',
                        'constraint' => '255',
                        'default'    => '',
                        'after'      => 'amazon_s3_bucket'
                    )
                );

                ee()->dbforge->add_column('safeharbor_settings', $fields);
            }
        }

        // if( $current === '1.3.3.7' )
        //  $you = 'L33t Haxx0r.';

        return true;
    }

    public function uninstall()
    {
        $backup_path = $this->backups_directory;

        // delete the backup path
        if (!is_null($backup_path) && $this->folder_exist($backup_path)) {
            ee()->functions->delete_directory($backup_path, true);
        }

        // Delete the cached backups
        if ($this->folder_exist(APPPATH.'cache/safeharbor_backups/')) {
            ee()->functions->delete_directory(APPPATH.'cache/safeharbor_backups/', true);
        }

        // Get the module_id for safeharbor from exp_modules
        $module = ee()->db->query("SELECT module_id FROM ".ee()->db->dbprefix('modules')." WHERE module_name='Safeharbor' LIMIT 1");
        $module_id = $module->row('module_id');

        // delete safeharbor from the member_groups and the modules table table
        ee()->db->query("DELETE FROM ".ee()->db->dbprefix('module_member_groups')." WHERE module_id=$module_id");
        ee()->db->query("DELETE FROM ".ee()->db->dbprefix('modules')." WHERE module_name = 'Safeharbor'");

        // Delete the Safeharbor actions
        ee()->db->query("DELETE FROM ".ee()->db->dbprefix('actions')." WHERE class='Safeharbor'");
        ee()->db->query("DELETE FROM ".ee()->db->dbprefix('actions')." WHERE class='Safeharbor_CP'");

        // Drop all of our custom Safeharbor tables
        ee()->db->query("DROP TABLE IF EXISTS ".ee()->db->dbprefix('safeharbor_backups'));
        ee()->db->query("DROP TABLE IF EXISTS ".ee()->db->dbprefix('safeharbor_messages'));
        ee()->db->query("DROP TABLE IF EXISTS ".ee()->db->dbprefix('safeharbor_hash'));
        ee()->db->query("DROP TABLE IF EXISTS ".ee()->db->dbprefix('safeharbor_settings'));
        ee()->db->query("DROP TABLE IF EXISTS ".ee()->db->dbprefix('safeharbor_indexes'));

        return true;
    }

    private function _createBackupsTable()
    {
        ee()->load->dbforge();

        // Create safeharbor_backups tables and keys
        $fields = array(
            'backup_id' => array('type' => 'INT(6)', 'unsigned' => true, 'null' => false, 'auto_increment' => true),
            'site_id' => array('type' => 'INT(6)', 'unsigned' => true, 'null' => false),
            'name' => array('type' => 'varchar(128)', 'null' => false),
            'backup_time' => array('type' => 'varchar(32)'),
            'start_time' => array('type' => 'varchar(32)', 'null' => false),
            'end_time' => array('type' => 'varchar(32)'),
            'status' => array('type' => 'varchar(32)'),
            'db_backup_mode' => array('type' => 'varchar(32)'),
            'backup_size' => array('type' => 'varchar(32)'),
            'backup_dbsize' => array('type' => 'varchar(32)'),
            'md5_hash' => array('type' => 'varchar(32)'),
            'local' => array('type' => 'int(1)'),
            'backup_transfer_failed' => array('type' => 'int(1)'),
            'backup_type' => array('type' => "ENUM('full','differential')", 'null' => false),
            'file_ctime' => array('type' => 'INT(11)'),
            'full_backup_id' => array('type' => 'INT(6)'),
            'transferred_amazon_s3' => array('type' => 'int(1)', 'null' => false, 'default' => '0'),
            'transferred_ftp' => array('type' => 'int(1)', 'null' => false, 'default' => '0'),
            'note' => array('type' => 'text', 'null' => false),
        );

        ee()->dbforge->add_field($fields);
        ee()->dbforge->add_key('backup_id', true);
        ee()->dbforge->add_key('backup_type');
        ee()->dbforge->create_table('safeharbor_backups');
    }

    private function _createMessagesTable()
    {
        ee()->load->dbforge();

        // Create safeharbor_messages tables and keys
        $fields = array(
            'id' => array('type' => 'INT(6)', 'unsigned' => true, 'null' => false, 'auto_increment' => true),
            'site_id' => array('type' => 'INT(6)', 'unsigned' => true, 'null' => false ),
            'message' => array('type' => 'varchar(512)', 'null' => false ),
            'date' => array('type' => 'varchar(32)', 'null' => false ),
        );

        ee()->dbforge->add_field($fields);
        ee()->dbforge->add_key('id', true);
        ee()->dbforge->create_table('safeharbor_messages');
    }

    private function _createHashTable()
    {
        ee()->load->dbforge();

        // Create safeharbor_hash tables and keys
        $fields = array(
            'id' => array( 'type' => 'INT(6)', 'unsigned' => true, 'null' => false, 'auto_increment' => true),
            'site_id' => array( 'type' => 'INT(6)', 'unsigned' => true, 'null' => false ),
            'hash' => array( 'type' => 'varchar(36)', 'null' => false ),
        );

        ee()->dbforge->add_field($fields);
        ee()->dbforge->add_key('id', true);
        ee()->dbforge->create_table('safeharbor_hash');
    }

    private function _createSettingsTable()
    {
        ee()->load->dbforge();

        // Create safeharbor_settings tables and keys
        $fields = array(
            'id' => array( 'type' => 'INT(6)', 'unsigned' => true, 'null' => false, 'auto_increment' => true),
            'site_id' => array( 'type' => 'INT(6)', 'null' => false, 'unique' => true),
            'auth_code' => array( 'type' => 'varchar(32)', 'null' => false ),
            'off_site_backup_auth_code' => array( 'type' => 'varchar(32)', 'null' => false ),
            'time_diff' => array( 'type' => 'int(2)', 'null' => false ),
            'db_backup' => array( 'type' => 'varchar(32)', 'null' => false ),
            'notify_email_address' => array( 'type' => 'varchar(128)', 'null' => false ),
            'backup_time' => array( 'type' => 'varchar(32)', 'null' => false ),
            'backup_plan' => array( 'type' => 'varchar(128)', 'null' => false ),
            'time_saved' => array( 'type' => 'varchar(32)', 'null' => false ),
            'backup_space' => array( 'type' => 'varchar(32)', 'null' => false ),
            'backup_path' => array( 'type' => 'varchar(256)', 'null' => false ),
            'storage_path' => array( 'type' => 'varchar(256)', 'null' => false ),
            'transfer_type' => array( 'type' => 'varchar(5)', 'null' => false ),
            'disable_remote' => array( 'type' => 'int(1)', 'null' => false ),
            'amazon_s3_enabled' => array( 'type' => 'int(1)', 'null' => false, 'default' => '0'),
            'amazon_s3_access_key' => array( 'type' => 'varchar(256)', 'null' => false ),
            'amazon_s3_secret' => array( 'type' => 'varchar(256)', 'null' => false ),
            'amazon_s3_bucket' => array( 'type' => 'varchar(256)', 'null' => false ),
            'amazon_s3_endpoint' => array( 'type' => 'varchar(256)', 'null' => false ),
            'ftp_enabled' => array( 'type' => 'int(1)', 'null' => false, 'default' => '0'),
            'ftp_username' => array( 'type' => 'varchar(128)', 'null' => false ),
            'ftp_password' => array( 'type' => 'varchar(256)', 'null' => false ),
            'ftp_host' => array( 'type' => 'varchar(128)', 'null' => false ),
            'ftp_port' => array( 'type' => 'int(5)', 'null' => false ),
            'ftp_path' => array( 'type' => 'varchar(256)', 'null' => false ),
        );

        ee()->dbforge->add_field($fields);
        ee()->dbforge->add_key('id', true);
        ee()->dbforge->add_key('site_id');
        ee()->dbforge->create_table('safeharbor_settings');
    }

    private function _createIndexesTable()
    {
        ee()->load->dbforge();

        // Create safeharbor_indexes tables and keys
        $fields = array(
            'id' => array( 'type' => 'INT(7)', 'unsigned' => true, 'null' => false, 'auto_increment' => true),
            'backup_id' => array( 'type' => 'INT(6)', 'unsigned' => true, 'null' => false ),
            'file' => array( 'type' => 'varchar(512)', 'null' => false ),
        );

        ee()->dbforge->add_field($fields);
        ee()->dbforge->add_key('id', true);
        ee()->dbforge->create_table('safeharbor_indexes');
    }

    /**
     * Checks if a folder exist and return canonicalized absolute pathname (long version)
     * @param string $folder the path being checked.
     * @return mixed returns the canonicalized absolute pathname on success otherwise FALSE is returned
     */
    private function folder_exist($folder)
    {
        // Get canonicalized absolute pathname
        $path = realpath($folder);

        // If it exist, check if it's a directory
        if ($path !== false and is_dir($path)) {
            // Return canonicalized absolute pathname
            return $path;
        }

        // Path/folder does not exist
        return false;
    }
}

/* End of File: upd.safeharbor.php */
