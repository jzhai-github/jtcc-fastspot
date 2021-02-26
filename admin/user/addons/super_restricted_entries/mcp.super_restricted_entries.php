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
* @filesource   ./system/user/addons/super_restricted_entries/mcp.super_restricted_entries.php
*/

require_once PATH_THIRD . 'super_restricted_entries/config.php';

class Super_restricted_entries_mcp
{

    var $version = SUPER_RESTRICTED_ENTRIES_VER;
    var $vars;

    function __construct()
    {
        ee()->load->library('Super_restricted_entries_lib', null, 'superRestrictedEntries');
    }

    public function index()
    {

        ee()->cp->load_package_css("super_restricted_entries");
        // ee()->cp->load_package_js("super_restricted_entries");
        $this->vars = $this->_startupForm();

        if(isset($_POST) && count($_POST))
        {
            $ret = ee()->superRestrictedEntries->saveGeneralSettings();
            if($ret)
            {
                ee()->functions->redirect(ee()->superRestrictedEntries->createURL());
            }
        }

        $this->vars = ee()->superRestrictedEntries->generalSettings($this->vars);
        return array(
            'heading'    => lang('general_settings'),
            'body'       => "<div class='" . ((version_compare(APP_VER, '4.0.0', '<')) ? 'box below_ee_4' : '') . "'>" . ee('View')->make('ee:_shared/form')->render($this->vars) . "</div>",
            'breadcrumb' => array(
                ee('CP/URL', 'addons/settings/super_restricted_entries/')->compile() => lang('super_restricted_entries')
            ),
        );
    }

    public function sync_entry_access()
    {

        if(! ee()->db->field_exists('entry_access', 'channel_titles'))
        {
            ee('CP/Alert')->makeInline('shared-form')
                ->asIssue()->canClose()
                ->withTitle(lang('error_encountered'))
                ->addToBody(lang('sync_db_not_found'))
                ->defer();
            ee()->functions->redirect(ee()->superRestrictedEntries->createURL());
        }

        ee()->db->select("entry_id, site_id, channel_id, entry_access");
        ee()->db->from('channel_titles');
        $get  = ee()->db->get();
        $sync = false;

        if($get->num_rows())
        {

            $temp = ee()->db->get('super_restricted_entries_data');
            $superRestrictedEntriesData = array();
            if($temp->num_rows() > 0)
            {
                $temp = $temp->result();
                foreach ($temp as $value)
                {
                    $superRestrictedEntriesData[$value->entry_id] = 'exists';
                }
            }

            foreach ($get->result() as $key => $value)
            {

                if($value->entry_access != NULL && ! isset($superRestrictedEntriesData[$value->entry_id]))
                {

                    $temp = unserialize($value->entry_access);
                    if(! (isset($temp['group_access']) and isset($temp['member_access'])))
                    {
                        continue;
                    }

                    if(empty($temp['group_access']) and empty($temp['member_access']))
                    {
                        continue;
                    }

                    $insert = array(
                        'entry_id'      => $value->entry_id,
                        'site_id'       => $value->site_id,
                        'channel_id'    => $value->channel_id,
                        'group_access'  => implode('|',$temp['group_access']),
                        'member_access' => implode('|',$temp['member_access']),
                    );
                    ee()->sreModel->saveTabData($insert);

                    $sync = true;

                }

            }

        }

        if($sync)
        {
            $message = lang('sync_success');
        }
        else
        {
            $message = lang('sync_already_completed');
        }

        ee('CP/Alert')->makeInline('shared-form')
            ->asSuccess()
            ->withTitle(lang('settings_saved'))
            ->addToBody($message)
            ->defer();
        ee()->functions->redirect(ee()->superRestrictedEntries->createURL());

    }

    function _startupForm()
    {

        $this->vars = array();

        /*CSRF and XID is same after EE V 2.8.0. For previous versions (Backword compatability)*/
        if(version_compare(APP_VER, '2.8.0', '<'))
        {
            $this->vars['csrf_token']   = ee()->security->get_csrf_hash();
            $this->vars['xid']          = ee()->functions->add_form_security_hash('{XID_HASH}');
        }
        else
        {
            $this->vars['csrf_token']   = XID_SECURE_HASH;
            $this->vars['xid']          = XID_SECURE_HASH;
        }

    }
}
