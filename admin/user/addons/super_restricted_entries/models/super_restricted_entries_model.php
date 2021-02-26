<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
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
* @filesource   ./system/user/addons/super_restricted_entries/models/super_restricted_entries_model.php
*/

class Super_restricted_entries_model extends CI_Model
{

	function __construct()
	{
        $this->site_id = ee()->config->item('site_id');
	}

	function getMembers()
	{

		return ee('Model')->get('Member')
			->fields('member_id', 'screen_name')
			->filter('group_id', '!=', '1')
			->all()
			->getDictionary('member_id', 'screen_name');

	}

	function getMemberGroups()
	{

		return ee('Model')->get('MemberGroup')
			->fields('group_id', 'group_title')
			->filter('site_id', ee()->config->item('site_id'))
			->filter('group_id', 'NOT IN', array(1,2,4))
			->order('group_title', 'asc')
			->all()
			->getDictionary('group_id', 'group_title');

	}

	function saveGeneralSettings()
    {

        $insert = array(
            'site_id'                   => ee()->config->item('site_id'),
            'group_access'              => is_array(ee()->input->post('group_access', true)) ? implode('|', ee()->input->post('group_access', true)) : "",
            'member_access'             => is_array(ee()->input->post('member_access', true)) ? implode('|', ee()->input->post('member_access', true)) : "",
            'assign_access_to_author'   => ee()->input->post('assign_access_to_author', true),
        );

        $alreadyExists = ee()->db->get_where('super_restricted_entries_settings', array('site_id' => $insert['site_id']))->num_rows();
        if($alreadyExists)
        {
            ee()->db->where('site_id', $insert['site_id']);
            ee()->db->update('super_restricted_entries_settings', $insert);
        }
        else
        {
            ee()->db->insert('super_restricted_entries_settings', $insert);
        }

    }

    function getGeneralSettings()
    {

        $site_id = ee()->config->item('site_id');
        $query = ee()->db->get_where('super_restricted_entries_settings', array('site_id' => $site_id));

        if($query->num_rows() == 0)
        {
            return array();
        }
        else
        {
            $data = $query->result_array();
            $data = $data[0];
            $data['group_access']  = ($data['group_access'] != "") ? explode('|', $data['group_access']) : array();
            $data['member_access'] = ($data['member_access'] != "") ? explode('|', $data['member_access']) : array();
            return $data;
        }

    }

    function saveTabData($data)
    {

        $where = array(
            'entry_id' => $data["entry_id"],
            'site_id' => $data["site_id"]
            );
        $query = ee()->db->get_where("super_restricted_entries_data", $where);

        if($query->num_rows())
        {
            ee()->db->where($where);
            ee()->db->update("super_restricted_entries_data", $data);
        }
        else
        {
            ee()->db->insert("super_restricted_entries_data", $data);
        }

        return true;

    }

    function getTabData($where)
    {

        $this->db->select('*');
        $this->db->limit(1);
        $this->db->from('super_restricted_entries_data');
        $this->db->where($where);
        $data = $this->db->get();

        if($data->num_rows == 0)
        {
            return false;
        }

        $defaultData = $data->result_array();
        $defaultData = $defaultData[0];
        $defaultData['group_access']  =  ($defaultData['group_access'] != "")  ? explode('|', $defaultData['group_access'])  : array();
        $defaultData['member_access'] =  ($defaultData['member_access'] != "") ? explode('|', $defaultData['member_access']) : array();

        return $defaultData;

    }

}