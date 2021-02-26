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
* @filesource   ./system/user/addons/super_restricted_entries/mod.super_restricted_entries.php
*/

class Super_restricted_entries
{

    var $settings 	 = array();

    function __construct()
    {
    	ee()->load->library('Super_restricted_entries_lib', null, 'superRestrictedEntries');
    }

    function check()
    {

    	$entryId = ee()->TMPL->fetch_param('entry_id');
    	$prefix  = ee()->TMPL->fetch_param('prefix');
    	$tagdata = ee()->TMPL->tagdata;
    	$prefix  = ($prefix == "") ? "super_restricted_entries" : $prefix;

    	if($entryId == "")
    	{
    		$status = true;
    	}
    	else
    	{
            $where = array(
                'entry_id' => $entryId,
                'site_id'  => ee()->config->item('site_id')
                );
            $superRestrictedEntriesData = ee()->sreModel->getTabData($where);
	    	$status                     = ee()->superRestrictedEntries->checkStatus($superRestrictedEntriesData);
    	}

    	$ret = array(
    		$prefix . ':granted' => $status
    	);

    	return ee()->TMPL->parse_variables_row($tagdata, $ret);

    }

}