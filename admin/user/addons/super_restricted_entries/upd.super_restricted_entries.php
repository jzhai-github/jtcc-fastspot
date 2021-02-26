<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* The software is provided "as is", without warranty of any
* kind, express or implied, including but not limited to the
* warranties of merchantability, fitness for a particular
* purpose and noninfringement. in no event shall the authors
* or copyright holders be liable for any claim, damages or
* other liability, whether in an action of contract, tort or
* otherwise, arising from, out of or in connection with the
* software or the use or other dealings in the software.
* -----------------------------------------------------------
* Amici Infotech - Super Restricted Entries
*
* @package      SuperRestrictedEntries
* @author       Mufi
* @copyright    Copyright (c) 2019, Amici Infotech.
* @link         http://expressionengine.amiciinfotech.com/super-restricted-entries
* @filesource   ./system/user/addons/super_restricted_entries/upd.super_restricted_entries.php
*/

require_once PATH_THIRD . 'super_restricted_entries/config.php';

class Super_restricted_entries_upd
{

    public $version         = SUPER_RESTRICTED_ENTRIES_VER;
    private $module_name    = SUPER_RESTRICTED_ENTRIES_MOD;

    function __construct()
    {
        ee()->load->dbforge();
        ee()->load->library('layout');
    }

    function install()
    {

        $settings = array();

        /*Create a "settings" field in modules table if there is not one.*/
        if (ee()->db->field_exists('settings', 'modules') == FALSE)
        {
            ee()->dbforge->add_column('modules', array('settings' => array('type' => 'TEXT')));
        }

        /* Register module in EE (install) */
        $data = array(
            'module_name'           => $this->module_name,
            'module_version'        => $this->version,
            'has_cp_backend'        => 'y',
            'has_publish_fields'    => 'y',
            'settings'              => serialize($settings)
        );
        ee()->db->insert('modules', $data);

        $this->_addTables();

        /* Register Main Layout in entry page (Under "Super Restricted Entries" Tab*/
        $tabs['super_restricted_entries'] = array(
            'group_access' => array(
                'visible'       => true,
                'collapse'      => false,
                'htmlbuttons'   => false,
                'width'         => '100%'
            ),
            'member_access' => array(
                'visible'       => true,
                'collapse'      => false,
                'htmlbuttons'   => false,
                'width'         => '100%'
            )
        );
        ee()->layout->add_layout_tabs($tabs, 'super_restricted_entries');

        return TRUE;

    }

    function uninstall()
    {

        ee()->db->select('module_id');
        $query = ee()->db->get_where('modules', array('module_name' => $this->module_name));

        ee()->db->where('module_id', $query->row('module_id'));
        ee()->db->delete('module_member_groups');

        ee()->db->where('module_name', $this->module_name);
        ee()->db->delete('modules');

        ee()->db->where('class', $this->module_name);
        ee()->db->delete('actions');

        ee()->dbforge->drop_table('super_restricted_entries_data');
        ee()->dbforge->drop_table('super_restricted_entries_settings');

        $tabs['super_restricted_entries'] = array(
            'group_access'  => array(),
            'member_access' => array()
        );
        ee()->layout->delete_layout_tabs($tabs);

        return TRUE;

    }

    function update($current = '')
    {

        if ($current == '' OR $current == $this->version)
        {
            return FALSE;
        }

        return TRUE;

    }

    function _addTables()
    {

        if ( ! ee()->db->table_exists('super_restricted_entries_settings') )
        {

            $fields = array(
                'id' => array(
                    'type'          => 'int',
                    'constraint'    => '10',
                    'unsigned'      => TRUE,
                    'null'          => FALSE,
                    'auto_increment'=> TRUE
                    ),
                'site_id' => array(
                    'type'          => 'int',
                    'constraint'    => '10',
                    'unsigned'      => TRUE,
                    'null'          => FALSE,
                    ),
                'group_access' => array(
                    'type' => 'text',
                    'null' => TRUE
                    ),
                'member_access' => array(
                    'type' => 'mediumtext',
                    'null' => TRUE
                    ),
                'assign_access_to_author' => array(
                    'type'       => 'varchar',
                    'constraint' => '1',
                    'null'       => TRUE
                    ),
                );

            ee()->dbforge->add_field($fields);
            ee()->dbforge->add_key('id', TRUE);
            ee()->dbforge->create_table('super_restricted_entries_settings');

        }

        if ( ! ee()->db->table_exists('super_restricted_entries_data') )
        {

            $fields = array(
                'id' => array(
                    'type'          => 'int',
                    'constraint'    => '10',
                    'unsigned'      => TRUE,
                    'null'          => FALSE,
                    'auto_increment'=> TRUE
                    ),
                'site_id' => array(
                    'type'          => 'int',
                    'constraint'    => '10',
                    'unsigned'      => TRUE,
                    'null'          => FALSE,
                    ),
                'entry_id' => array(
                    'type'          => 'int',
                    'constraint'    => '10',
                    'unsigned'      => TRUE,
                    'null'          => FALSE,
                    ),
                'channel_id' => array(
                    'type'          => 'int',
                    'constraint'    => '10',
                    'unsigned'      => TRUE,
                    'null'          => FALSE,
                    ),
                'group_access' => array(
                    'type' => 'text',
                    'null' => TRUE
                    ),
                'member_access' => array(
                    'type' => 'mediumtext',
                    'null' => TRUE
                    ),
                );

            ee()->dbforge->add_field($fields);
            ee()->dbforge->add_key('id', TRUE);
            ee()->dbforge->create_table('super_restricted_entries_data');

        }

    }

}