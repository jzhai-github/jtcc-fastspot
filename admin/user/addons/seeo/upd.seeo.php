<?php

include_once 'addon.setup.php';
use EEHarbor\Seeo\FluxCapacitor\Base\Upd;

/**
 * Class SEEO Update
 *
 * @package         SEEO
 * @author          Tom Jaeger <Tom@EEHarbor.com>
 * @link            https://eeharbor.com
 * @copyright       Copyright (c) 2018, Tom Jaeger/EEHarbor
 */
class Seeo_upd extends Upd
{

    //----------------------------------------------------------------------------
    // PROPERTIES
    //----------------------------------------------------------------------------

    public $version = SEEO_VERSION;
    public $has_cp_backend = 'y';
    public $has_publish_fields = 'y';

    private $actions = array(
        // array('seeo', 'action_name')
    );

    // --------------------------------------
    // In order to install an extension we
    // need to register at least one hook
    // --------------------------------------
    private $hooks = array(
        'cp_custom_menu',
    );

    //----------------------------------------------------------------------------
    // METHODS
    //----------------------------------------------------------------------------

    public function __construct()
    {
        parent::__construct();

        // -------------------------------------
        //  Load helper, libraries and models
        // -------------------------------------
        ee()->load->add_package_path(PATH_THIRD . 'seeo');
        ee()->load->library(array('seeo_settings'));
        ee()->lang->loadfile('seeo');
        ee()->load->dbforge();
    }

    public function install()
    {
        parent::install();

        // --------------------------------------
        // Meta Info table
        // --------------------------------------
        $this->create_seeo_table();
        $this->create_template_page_meta_table();
        $this->create_migration_table();
        $this->create_settings_table();

        // --------------------------------------
        // Add rows to extensions table
        // --------------------------------------

        foreach ($this->hooks as $hook) {
            ee()->db->insert('extensions', array(
                'class'    => 'Seeo_ext',
                'method'   => $hook,
                'hook'     => $hook,
                'priority' => 5,
                'version'  => $this->version,
                'enabled'  => 'y',
                'settings' => ''
            ));
        }

        // --------------------------------------
        // Add Tabs to CP
        // --------------------------------------

        ee()->load->library('layout');
        ee()->layout->add_layout_tabs($this->tabs(), 'seeo');

        return true;
    }

    //----------------------------------------------------------------------------

    /**
     * Include the Tabs
     *
     * @access public
     * @return array
     */

    public function tabs()
    {
        return array('seeo' => array(
            'title' => array(
                'visible' => true,
                'collapsed' => false,
                'htmlbuttons' => true,
                'width' => '100%',
                ),
            'description' => array(
                'visible' => true,
                'collapsed' => false,
                'htmlbuttons' => true,
                'width' => '100%',
                ),
            'keywords' => array(
                'visible' => true,
                'collapsed' => false,
                'htmlbuttons' => true,
                'width' => '100%',
                ),
            'author' => array(
                'visible' => true,
                'collapsed' => false,
                'htmlbuttons' => true,
                'width' => '100%',
                ),
            'canonical_url' => array(
                'visible' => true,
                'collapsed' => false,
                'htmlbuttons' => true,
                'width' => '100%',
                ),
            'robots' => array(
                'visible' => true,
                'collapsed' => false,
                'htmlbuttons' => true,
                'width' => '100%',
                ),
            'og_title' => array(
                'visible' => true,
                'collapsed' => false,
                'htmlbuttons' => true,
                'width' => '100%',
                ),
            'og_description' => array(
                'visible' => true,
                'collapsed' => false,
                'htmlbuttons' => true,
                'width' => '100%',
                ),
            'og_type' => array(
                'visible' => true,
                'collapsed' => false,
                'htmlbuttons' => true,
                'width' => '100%',
                ),
            'og_url' => array(
                'visible' => true,
                'collapsed' => false,
                'htmlbuttons' => true,
                'width' => '100%',
                ),
            'og_image' => array(
                'visible' => true,
                'collapsed' => false,
                'htmlbuttons' => true,
                'width' => '100%',
                ),
            'twitter_title' => array(
                'visible' => true,
                'collapsed' => false,
                'htmlbuttons' => true,
                'width' => '100%',
                ),
            'twitter_description' => array(
                'visible' => true,
                'collapsed' => false,
                'htmlbuttons' => true,
                'width' => '100%',
                ),
            'twitter_content_type' => array(
                'visible' => true,
                'collapsed' => false,
                'htmlbuttons' => true,
                'width' => '100%',
                ),
            'twitter_image' => array(
                'visible' => true,
                'collapsed' => false,
                'htmlbuttons' => true,
                'width' => '100%',
                ),
            'sitemap_priority' => array(
                'visible' => true,
                'collapsed' => false,
                'htmlbuttons' => true,
                'width' => '100%',
                ),
            'sitemap_change_frequency' => array(
                'visible' => true,
                'collapsed' => false,
                'htmlbuttons' => true,
                'width' => '100%',
                ),
            'sitemap_include' => array(
                'visible' => true,
                'collapsed' => false,
                'htmlbuttons' => true,
                'width' => '100%',
                )
            )
        );
    }

    //----------------------------------------------------------------------------

    /**
     * Uninstallation Routine
     *
     * @access public
     * @return bool
     */

    public function uninstall()
    {
        // Get the foundation to take care of all the normal uninstall stuff
        parent::uninstall();

        // --------------------------------------
        // Uninstall the tables
        // --------------------------------------

        ee()->dbforge->drop_table('seeo');
        ee()->dbforge->drop_table('seeo_template_page');
        ee()->dbforge->drop_table('seeo_migration');
        ee()->dbforge->drop_table('seeo_config');
        ee()->dbforge->drop_table('seeo_default_settings');

        // --------------------------------------
        // Remove Publish Tabs
        // --------------------------------------

        ee()->load->library('layout');
        ee()->layout->delete_layout_tabs($this->tabs(), 'seeo');

        return true;
    }
    //-------------------------------------------------------------------

    /**
     * Update the module
     *
     * @access public
     * @param string $current. Current is the version currently INSTALLED
     * @return bool
     */
    public function update($current = '')
    {
        // -------------------------------------
        // Bail out if we don't need an upgrade
        // -------------------------------------

        if ($current == $this->version) {
            return false;
        }

        // --------------------------------------
        // Adding Low Variable Compatibility
        // --------------------------------------
        if (version_compare($current, '0.5.0', '<')) {
            ee()->db->query('ALTER TABLE exp_seeo MODIFY COLUMN entry_id INTEGER (10) NULL');
            ee()->db->query('ALTER TABLE exp_seeo MODIFY COLUMN channel_id INTEGER (10) NULL');
            ee()->db->query('ALTER Table exp_seeo ADD var_id INTEGER(10) NULL');
        }

        // --------------------------------------
        // Adding Template Page Meta
        // --------------------------------------
        if (version_compare($current, '0.6.1', '<')) {
            $this->create_template_page_meta_table();
        }

        // --------------------------------------
        // Adding title prefix an suffix to meta
        // --------------------------------------
        if (version_compare($current, '0.6.5', '<')) {
            ee()->db->query('ALTER Table exp_seeo ADD title_prefix VARCHAR(255) NULL');
            ee()->db->query('ALTER Table exp_seeo ADD title_suffix VARCHAR(255) NULL');
        }

        // --------------------------------------
        // Adding Migration table
        // --------------------------------------
        if (version_compare($current, '0.6.7', '<')) {
            $this->create_migration_table();
        }

        // --------------------------------------
        // Add a datetime column for the
        // --------------------------------------
        if (version_compare($current, '0.7.1', '<')) {
            $fields = array('channel_id' => array('type' => 'INTEGER', 'default' => 0));
            ee()->dbforge->add_column('seeo_template_page', $fields);
        }

        // --------------------------------------
        // Rename the listing table to template_page
        // --------------------------------------
        if (version_compare($current, '1.1.0')) {
            if (ee()->db->table_exists('seeo_listing') && !ee()->db->table_exists('seeo_template_page')) {
                ee()->dbforge->rename_table('seeo_listing', 'seeo_template_page');
            }

            if (! ee()->db->table_exists('seeo_default_settings')) {
                $this->create_settings_table();
            }

            // Convert the priority on both the exp_seeo and exp_seeo_default_settings tables
            // to a varchar as '0.0' equates to '0' (empty) when it's a 'DOUBLE'.
            $field = array(
                'sitemap_priority' => array(
                    'name' => 'sitemap_priority',
                    'type' => 'varchar',
                    'constraint' => 3,
                    'default' => '0.5'
                )
            );

            ee()->dbforge->modify_column('seeo', $field);
            ee()->dbforge->modify_column('seeo_default_settings', $field);

            // Migrate the existing settings that are stored in the extension
            // to the new exp_seeo_default_settings table format.
            $ext_settings = ee()->db->select('settings')
                ->from('extensions')
                ->where('class', 'Seeo_ext')
                ->limit(1)
                ->get()->row();

            if (!empty($ext_settings) && !empty($ext_settings->settings)) {
                $ext_settings = unserialize($ext_settings->settings);

                // Check if we already have default settings. We /shouldn't/ but
                // users do some strange stuff with partial DB or DataGrab imports.
                $existing_default_settings = ee()->db->select('id')->from('seeo_default_settings')->where(array('site_id' => ee()->config->item('site_id'), 'channel_id' => 0))->get()->row();

                if (empty($existing_default_settings)) {
                    $settingData = array();

                    $settingData['site_id'] = ee()->config->item('site_id');
                    $settingData['channel_id'] = 0;
                    $settingData['enabled'] = $ext_settings['enabled'];
                    $settingData['divider'] = $ext_settings['seeo_divider'];
                    $settingData['file_manager'] = $ext_settings['seeo_file_manager'];
                    $settingData['title'] = $ext_settings['seeo_default_site_title'];
                    $settingData['description'] = $ext_settings['seeo_default_description'];
                    $settingData['keywords'] = $ext_settings['seeo_default_keywords'];
                    $settingData['author'] = $ext_settings['seeo_default_author'];
                    $settingData['title_prefix'] = $ext_settings['seeo_default_title_prefix'];
                    $settingData['title_suffix'] = $ext_settings['seeo_default_title_suffix'];
                    $settingData['canonical_url'] = $ext_settings['seeo_default_canonical_url'];
                    $settingData['robots'] = (!empty($ext_settings['default_robots']) ? $ext_settings['default_robots'] : 'INDEX, FOLLOW');
                    $settingData['sitemap_priority'] = (!empty($ext_settings['default_sitemap_priority']) ? $ext_settings['default_sitemap_priority'] : '0.5');
                    $settingData['sitemap_change_frequency'] = (!empty($ext_settings['default_sitemap_change_frequency']) ? $ext_settings['default_sitemap_change_frequency'] : 'weekly');
                    $settingData['sitemap_include'] = (!empty($ext_settings['sitemap_include']) ? $ext_settings['sitemap_include'] : 'n');
                    $settingData['og_title'] = $ext_settings['seeo_default_og_title'];
                    $settingData['og_description'] = $ext_settings['seeo_default_og_description'];
                    $settingData['og_type'] = $ext_settings['seeo_default_og_type'];
                    $settingData['og_url'] = $ext_settings['seeo_default_og_url'];
                    $settingData['og_image'] = $ext_settings['seeo_default_og_image'];
                    $settingData['twitter_title'] = $ext_settings['seeo_default_twitter_title'];
                    $settingData['twitter_description'] = $ext_settings['seeo_default_twitter_description'];
                    $settingData['twitter_content_type'] = $ext_settings['seeo_default_twitter_type'];
                    $settingData['twitter_image'] = $ext_settings['seeo_default_twitter_image'];
                    $settingData['template'] = $ext_settings['seeo_default_template'];

                    ee()->db->insert('seeo_default_settings', $settingData);
                }

                // If we have per-channel settings to migrate, let's do that!
                if (!empty($ext_settings['seeo_channels']) && is_array($ext_settings['seeo_channels'])) {
                    // Loop through the `seeo_channels` and remap the data into our new format.
                    foreach ($ext_settings['seeo_channels'] as $channel_id => $settings) {
                        $settings = $settings['settings'];
                        $settingData = array();

                        $settingData['site_id'] = $settings['site_id'];
                        $settingData['channel_id'] = $channel_id;
                        $settingData['enabled'] = (!empty($settings['enabled']) && $settings['enabled'] === 'y' ? 1 : 0);
                        $settingData['file_manager'] = 'default';
                        $settingData['divider'] = '-';
                        $settingData['robots'] = (!empty($settings['default_robots']) ? $settings['default_robots'] : 'INDEX, FOLLOW');
                        $settingData['sitemap_include'] = (!empty($settings['sitemap_include']) ? $settings['sitemap_include'] : 'n');
                        $settingData['sitemap_priority'] = (!empty($settings['default_sitemap_priority']) ? $settings['default_sitemap_priority'] : '0.5');
                        $settingData['sitemap_change_frequency'] = (!empty($settings['default_sitemap_change_frequency']) ? $settings['default_sitemap_change_frequency'] : 'weekly');
                        $settingData['template'] = $ext_settings['seeo_default_template'];

                        // Make sure we don't already have settings for this site/channel. We /shouldn't/ but
                        // users do some strange stuff with partial DB or DataGrab imports.
                        $existing_settings = ee()->db->select('id')->from('seeo_default_settings')->where(array('site_id' => $settingData['site_id'], 'channel_id' => $settingData['channel_id']))->get()->row();

                        // Only insert the data if it didn't exist as we don't want to overwrite what may be here already.
                        if (empty($existing_settings)) {
                            ee()->db->insert('seeo_default_settings', $settingData);
                        }
                    }
                }
            }
        }

        // --------------------------------------
        // Uninstall the fieldtype
        // --------------------------------------
        if (version_compare($current, '1.2.3', '<')) {
            ee()->db->where('name', 'seeo');
            ee()->db->delete('fieldtypes');

            ee()->db->where('field_name', 'seeo');
            ee()->db->delete('channel_fields');
        }

        // -------------------------------------
        // Update to version X
        // -------------------------------------

        return true;
    }

    private function create_seeo_table()
    {
        ee()->dbforge->add_field(array(
            'id'                       => array(
                'type'           => 'int',
                'constraint'     => '10',
                'unsigned'       => true,
                'auto_increment' => true,
            ),
            'entry_id'                 => array(
                'type'       => 'int',
                'constraint' => '10',
                'unsigned'   => true,
                'null'       => false,
            ),
            'site_id'                  => array(
                'type'       => 'int',
                'constraint' => '10',
                'unsigned'   => true,
                'null'       => false,
            ),
            'channel_id'               => array(
                'type'       => 'int',
                'constraint' => '10',
                'unsigned'   => true,
                'null'       => false,
            ),
            'title'                    => array('type' => 'varchar', 'constraint' => '255'),
            'description'              => array('type' => 'varchar', 'constraint' => '255'),
            'keywords'                 => array('type' => 'varchar', 'constraint' => '255'),
            'author'                   => array('type' => 'varchar', 'constraint' => '255'),
            'title_prefix'             => array('type' => 'varchar', 'constraint' => '255'),
            'title_suffix'             => array('type' => 'varchar', 'constraint' => '255'),
            'canonical_url'            => array('type' => 'varchar', 'constraint' => '255'),
            'robots'                   => array('type' => 'varchar', 'constraint' => '20'),

            // Sitemap
            'sitemap_priority'         => array('type' => 'varchar', 'constraint' => '3', 'default' => '0.5'),
            'sitemap_change_frequency' => array('type' => 'varchar', 'constraint' => '7'),
            'sitemap_include'          => array('type' => 'varchar', 'constraint' => '1'),

            // Open Graph
            'og_title'                 => array('type' => 'varchar', 'constraint' => '255'),
            'og_description'           => array('type' => 'varchar', 'constraint' => '255'),
            'og_type'                  => array('type' => 'varchar', 'constraint' => '255'),
            'og_url'                   => array('type' => 'varchar', 'constraint' => '255'),
            'og_image'                 => array('type' => 'varchar', 'constraint' => '255'),

            // Twitter Cards
            'twitter_title'            => array('type' => 'varchar', 'constraint' => '255'),
            'twitter_description'      => array('type' => 'varchar', 'constraint' => '255'),
            'twitter_content_type'     => array('type' => 'varchar', 'constraint' => '255'),
            'twitter_image'            => array('type' => 'varchar', 'constraint' => '255'),
        ));

        ee()->dbforge->add_key('id', true);

        ee()->dbforge->create_table('seeo');
    }

    private function create_template_page_meta_table()
    {
        // --------------------------------------
        // Meta template_page table
        // --------------------------------------

        ee()->dbforge->add_field(array(
            'id'      => array(
                'type'           => 'int',
                'constraint'     => '10',
                'unsigned'       => true,
                'auto_increment' => true,
            ),
            'meta_id' => array(
                'type'       => 'int',
                'constraint' => '10',
                'unsigned'   => true,
                'null'       => false,
            ),
            'path'          => array('type' => 'VARCHAR', 'constraint' => '255'),
            'channel_id'    => array('type' => 'INTEGER', 'default' => 0),
        ));

        ee()->dbforge->add_key('id', true);

        ee()->dbforge->create_table('seeo_template_page');
    }

    private function create_migration_table()
    {
        // --------------------------------------
        // Meta template_page table
        // --------------------------------------

        ee()->dbforge->add_field(array(
            'id'        => array(
                'type'           => 'INT',
                'constraint'     => '10',
                'unsigned'       => true,
                'auto_increment' => true,
            ),
            'shortname' => array('type' => 'VARCHAR', 'constraint' => '255'),
            'date'      => array('type' => 'DATETIME'),
            'settings'  => array('type' => 'TEXT'),
        ));

        ee()->dbforge->add_key('id', true);

        ee()->dbforge->create_table('seeo_migration');
    }

    private function create_settings_table()
    {
        // --------------------------------------
        // Meta template_page table
        // --------------------------------------

        ee()->dbforge->add_field(array(
            'id'                       => array('type' => 'int', 'constraint' => '10', 'unsigned' => true, 'auto_increment' => true),
            'site_id'                  => array('type' => 'int', 'constraint' => '10', 'unsigned' => true, 'null' => false),
            'channel_id'               => array('type' => 'int', 'constraint' => '10', 'unsigned' => true, 'null' => false),
            'enabled'                  => array('type' => 'tinyint', 'constraint' => '1', 'unsigned' => true, 'default' => '0'),
            'file_manager'             => array('type' => 'varchar', 'constraint' => '7'),
            'divider'                  => array('type' => 'varchar', 'constraint' => '255'),
            'title'                    => array('type' => 'varchar', 'constraint' => '255'),
            'description'              => array('type' => 'varchar', 'constraint' => '255'),
            'keywords'                 => array('type' => 'varchar', 'constraint' => '255'),
            'author'                   => array('type' => 'varchar', 'constraint' => '255'),
            'title_prefix'             => array('type' => 'varchar', 'constraint' => '255'),
            'title_suffix'             => array('type' => 'varchar', 'constraint' => '255'),
            'canonical_url'            => array('type' => 'varchar', 'constraint' => '255'),
            'robots'                   => array('type' => 'varchar', 'constraint' => '255'),

            // Sitemap
            'sitemap_priority'         => array('type' => 'varchar', 'constraint' => '3', 'default' => '0.5'),
            'sitemap_change_frequency' => array('type' => 'varchar', 'constraint' => '20'),
            'sitemap_include'          => array('type' => 'varchar', 'constraint' => '1'),

            // Open Graph
            'og_title'                 => array('type' => 'varchar', 'constraint' => '255'),
            'og_description'           => array('type' => 'varchar', 'constraint' => '255'),
            'og_type'                  => array('type' => 'varchar', 'constraint' => '255'),
            'og_url'                   => array('type' => 'varchar', 'constraint' => '255'),
            'og_image'                 => array('type' => 'varchar', 'constraint' => '255'),

            // Twitter Cards
            'twitter_title'            => array('type' => 'varchar', 'constraint' => '255'),
            'twitter_description'      => array('type' => 'varchar', 'constraint' => '255'),
            'twitter_content_type'     => array('type' => 'varchar', 'constraint' => '255'),
            'twitter_image'            => array('type' => 'varchar', 'constraint' => '255'),

            // Template
            'template'                 => array('type' => 'text'),
        ));

        ee()->dbforge->add_key('id', true);

        // 2019-03-02 - This doesn't work in EE properly. We have to
        // add indexes manually if we want them to be multi-key.
        // ee()->dbforge->add_key(array('site_id', 'channel_id'));

        ee()->dbforge->create_table('seeo_default_settings');

        // Hack to add indexes as the native CI add_key does not function properly.
        ee()->db->query("ALTER TABLE `exp_seeo_default_settings` ADD INDEX (`site_id`, `channel_id`)");
    }
} // End class
/* end of file upd.seeo.php */
