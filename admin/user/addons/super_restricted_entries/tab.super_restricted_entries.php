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
* @filesource   ./system/user/addons/super_restricted_entries/tab.super_restricted_entries.php
*/

class Super_restricted_entries_tab
{

    public $settings;
    public $limit;

	function __construct()
	{

        ee()->lang->loadfile('super_restricted_entries');
        ee()->load->library('Super_restricted_entries_lib', null, 'superRestrictedEntries');
        $this->limit = 100;

        $this->settings = ee()->sreModel->getGeneralSettings();

	}

	function display($channelId, $entryId = '')
	{

        $defaultData    = array();
        $member         = ee()->sreModel->getMembers();
        $memberGroups   = ee()->sreModel->getMemberGroups();

        if($entryId != "")
        {

            $where = array(
                'entry_id' => $entryId,
                'site_id'  => ee()->config->item('site_id')
                );
            $defaultData  = ee()->sreModel->getTabData($where);
            if($defaultData === false)
            {
                $defaultData = array();
            }

        }
        /*else
        {
            $defaultData = $this->settings;
        }*/

        $fields = array(
            'group_access' => array(
                'field_id'              => 'group_access',
                'field_label'           => lang('group_access'),
                'field_instructions'    => lang('group_access_settings_instructions'),
                'field_required'        => 'n',
                'field_list_items'      => $memberGroups,
                'options'               => $memberGroups,
                'selected'              => (isset($defaultData['group_access']) && is_array($defaultData['group_access'])) ? $defaultData['group_access'] : array(),
                'field_data'            => (isset($defaultData['group_access']) && is_array($defaultData['group_access'])) ? $defaultData['group_access'] : array(),
                'field_fmt'             => 'n',
                'field_show_fmt'        => 'n',
                'field_pre_populate'    => 'n',
                'field_text_direction'  => 'ltr',
                'field_type'            => 'checkboxes',
                'no_results'            => ['text' => sprintf(lang('no_found'), "Member Groups")]
            ),

            'member_access' => array(
                'field_id'              => 'member_access',
                'field_label'           => lang('member_access'),
                'field_instructions'    => lang('member_access_settings_instructions'),
                'field_required'        => 'n',
                'field_list_items'      => $member,
                'options'               => $member,
                'selected'              => (isset($defaultData['member_access']) && is_array($defaultData['member_access'])) ? $defaultData['member_access'] : "",
                'field_data'            => (isset($defaultData['member_access']) && is_array($defaultData['member_access'])) ? $defaultData['member_access'] : "",
                'field_fmt'             => 'n',
                'field_show_fmt'        => 'n',
                'field_pre_populate'    => 'n',
                'field_text_direction'  => 'ltr',
                'field_type'            => 'checkboxes',
                'no_results'            => ['text' => sprintf(lang('no_found'), "Members")]
            )
        );

        return $fields;

	}

	function validate($entry, $values)
	{
        /*$validator = ee('Validation')->make(array(
            'foo_field_one' => 'required',
            'foo_field_two' => 'required|enum[y,n]',
        ));

        return $validator->validate($values);*/
        return true;
	}

	function save($entry, $values)
	{

        if(array_filter($values))
        {

            $data               = $values;
            $data["site_id"]    = $entry->site_id;
            $data["channel_id"] = $entry->channel_id;
            $data["entry_id"]   = $entry->entry_id;

            ee()->sreModel->saveTabData($data);

        }
        else
        {
            $this->delete(array($entry->entry_id));
        }

        return ;

	}

    public function delete($entryIds)
    {
        ee()->db->where_in('entry_id', $entryIds);
        ee()->db->delete('super_restricted_entries_data');
    }

}