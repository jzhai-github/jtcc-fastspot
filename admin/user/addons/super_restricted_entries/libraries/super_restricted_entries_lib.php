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
* @filesource   ./system/user/addons/super_restricted_entries/libraries/super_restricted_entries_lib.php
*/

class Super_restricted_entries_lib
{

    public $settings = false;
	public function __construct()
    {
    	ee()->load->model('super_restricted_entries_model', 'sreModel');
    }

    function generalSettings($vars)
    {

        $member         = ee()->sreModel->getMembers();
        $memberGroups   = ee()->sreModel->getMemberGroups();
        $defaultData    = ee()->sreModel->getGeneralSettings();

        $vars['sections'] = array(
        	array(
        		 array(
        			'title' => lang('group_access'),
        			'desc'  => lang('group_access_settings_instructions'),
        			'fields' => array(
        				'group_access' => array(
        					'type'       => 'checkbox',
        					'choices'    => $memberGroups,
        					'value'      => (isset($_POST['group_access']) ? $_POST['group_access'] : (isset($defaultData['group_access']) ? $defaultData['group_access'] : "")),
                            'no_results' => ['text' => sprintf(lang('no_found'), "Member Groups")],
        				)
        			)
        		),

                array(
        			'title' => lang('member_access'),
        			'desc'  => lang('member_access_settings_instructions'),
        			'fields' => array(
        				'member_access' => array(
        					'type'       => 'checkbox',
        					'choices'    => $member,
        					'value'      => (isset($_POST['member_access']) ? $_POST['member_access'] : (isset($defaultData['member_access']) ? $defaultData['member_access'] : "")),
                            'no_results' => ['text' => sprintf(lang('no_found'), "Members")]
        				)
        			)
        		),

                array(
                    'title' => lang('assign_access_to_author_title'),
                    'desc'  => lang('assign_access_to_author_desc'),
                    'fields' => array(
                        'assign_access_to_author' => array(
                            'type'  => ((version_compare(APP_VER, '4.0.0', '<')) ? 'inline_radio' : 'toggle'),
                            'value' => (isset($_POST['assign_access_to_author']) ? $_POST['assign_access_to_author'] : (isset($defaultData['assign_access_to_author']) ? $defaultData['assign_access_to_author'] : "")),
                            'choices' => [
                                '0' => 'No',
                                '1' => 'Yes'
                            ]
                        )
                    )
                )
            )
        );

        ee()->cp->add_js_script(array(
          'file' => array('cp/form_group'),
        ));
        $vars += array(
            'base_url'              => ee('CP/URL', 'addons/settings/super_restricted_entries/'),
            'cp_page_title'         => lang('general_settings'),
            'save_btn_text'         => lang('save'),
            'save_btn_text_working' => lang('saving')
        );

        return $vars;

    }

    function saveGeneralSettings()
    {

        ee()->sreModel->saveGeneralSettings($_POST);

        ee('CP/Alert')->makeInline('shared-form')
            ->asSuccess()
            ->withTitle(lang('settings_saved'))
            ->addToBody(sprintf(lang('settings_saved_desc'), lang('super_restricted_entries')))
            ->defer();

        return true;

    }

    public function checkStatus($superRestrictedEntriesData)
    {

        $group_id   = ee()->session->userdata('group_id');
        $member_id  = ee()->session->userdata('member_id');
        $access     = false;

        if($this->settings === false)
        {
            $this->settings = ee()->sreModel->getGeneralSettings();
        }

        if(isset($this->settings['group_access']) && is_array($this->settings['group_access']) && in_array($group_id, $this->settings['group_access']))
        {
            $access = true;
        }
        elseif(isset($this->settings['member_access']) && is_array($this->settings['member_access']) && in_array($member_id, $this->settings['member_access']))
        {
            $access = true;
        }
        else
        {

            /*if ($group_id == 1 || $superRestrictedEntriesData === false || empty($superRestrictedEntriesData))
            {
                $access = true;
            }*/
            if($group_id == 1)
            {
                $access = true;
            }
            elseif(($superRestrictedEntriesData === false || empty($superRestrictedEntriesData)))
            {
                if(ee()->config->item('super_restricted_entries_allowed_empty') == "no")
                {
                    $access = false;
                }
                else
                {
                    $access = true;
                }
            }
            elseif(empty($superRestrictedEntriesData['group_access']) && empty($superRestrictedEntriesData['member_access']))
            {
                $access = true;
            }
            elseif( ! empty($superRestrictedEntriesData['group_access']) && in_array($group_id, $superRestrictedEntriesData['group_access']))
            {
                $access = true;
            }
            elseif( ! empty($superRestrictedEntriesData['member_access']) && in_array($member_id, $superRestrictedEntriesData['member_access']))
            {
                $access = true;
            }

        }

        if($access === false && isset($this->settings) && is_array($this->settings) && isset($this->settings['assign_access_to_author']) && $this->settings['assign_access_to_author'] == 1)
        {

            $authorEntries = ee()->db->select('entry_id')->get_where('channel_titles', ['entry_id' => $superRestrictedEntriesData['entry_id'], 'author_id' => $member_id])->num_rows();
            if($authorEntries > 0)
            {
                $access = true;
            }

        }

        return $access;

    }

    public function createURL($functionName = "index", $params = array())
    {

        $temp = "";
        if(count($params) > 0)
        {

            $temp = "/";
            foreach ($params as $key => $value)
            {
                $temp .= $value . "/";
            }
            rtrim($temp, "/");

        }

        return ee('CP/URL')->make('addons/settings/super_restricted_entries/' . $functionName . $temp);

    }

}