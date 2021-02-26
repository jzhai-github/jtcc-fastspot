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
* @filesource   ./system/user/addons/super_restricted_entries/ext.super_restricted_entries.php
*/

require_once PATH_THIRD . 'super_restricted_entries/config.php';

class Super_restricted_entries_ext
{

	var $name	     	= SUPER_RESTRICTED_ENTRIES_NAME;
	var $version 		= SUPER_RESTRICTED_ENTRIES_VER;
	var $description	= SUPER_RESTRICTED_ENTRIES_DESC;
	var $settings_exist	= 'y';
	var $docs_url		= '';
    var $settings 		= array();
    var $apply_super_restricted_entries;

	function __construct($settings = '')
	{

        $this->settings = $settings;
        if(! is_array($this->settings))
        {
            $this->settings = array();
        }

        if(! class_exists('super_restricted_entries_lib'))
        {
            ee()->load->library('super_restricted_entries_lib', null ,'superRestrictedEntries');
        }

        $this->apply_super_restricted_entries = false;

	}

    function channel_entries_query_result($object, $query_results)
    {

        if (ee()->extensions->last_call !== false)
        {
            $query_results = ee()->extensions->last_call;
        }

        if(isset(ee()->config->config['apply_super_restricted_entries']) && (strtolower(ee()->config->config['apply_super_restricted_entries']) == "yes" || strtolower(ee()->config->config['apply_super_restricted_entries']) == "y" || strtolower(ee()->config->config['apply_super_restricted_entries']) == true))
        {
            $this->apply_super_restricted_entries = true;
        }

        $tmp = strtolower(ee()->TMPL->fetch_param('apply_super_restricted_entries'));
        if($tmp == "yes" || $tmp == "y")
        {
            $this->apply_super_restricted_entries = true;
        }
        elseif($tmp == "no" || $tmp == "n")
        {
            $this->apply_super_restricted_entries = false;
        }

        $this->settings['logged_in_member_group_id']  = ee()->session->userdata('group_id');
        $this->settings['logged_in_member_member_id'] = ee()->session->userdata('member_id');

        if($this->apply_super_restricted_entries == false || $this->settings['logged_in_member_group_id'] == 1)
        {
            return $query_results;
        }


        if(! isset($this->settings['generalSettings']))
        {
            $this->settings['generalSettings'] = ee()->sreModel->getGeneralSettings();
        }

        if(isset($this->settings['generalSettings']['group_access']) && is_array($this->settings['generalSettings']['group_access']) && in_array($this->settings['logged_in_member_group_id'], $this->settings['generalSettings']['group_access']))
        {
            return $query_results;
        }
        elseif(isset($this->settings['generalSettings']['member_access']) && is_array($this->settings['generalSettings']['member_access']) && in_array($this->settings['logged_in_member_member_id'], $this->settings['generalSettings']['member_access']))
        {
            return $query_results;
        }

        if( empty($this->entry_sql) AND ! empty($object->sql) )
        {
            ee()->session->set_cache('super_restricted_entries', 'cache_sql', $object->sql);
        }

        $logged_queries = array_reverse(ee('Database')->getLog()->getQueries());

        $current_queries = array();
        // $logged_queries is an array of arrays we need 0 (the query) from each array
        foreach($logged_queries as $query_array)
        {
            $current_queries[] = $query_array[0];
        }

        // retrieve all entry_id's without the query limit (limit results after grabbing the translations)
        $select_entries_sql = NULL;
        $noDynamic = false;
        $dynamic = ee()->TMPL->fetch_param('dynamic');
        if (!empty($dynamic) && $dynamic === 'no') {
            $noDynamic = true;
        }

        $all_entry_ids = array();

        foreach( $current_queries as $query )
        {

            // check for relationship
            if (strpos($query, 'SELECT rel_id, rel_parent_id, rel_child_id, rel_type, rel_data') !== false)
            {
                return $query_results;
            }
            elseif (strpos($query, 'SELECT `rel_id`, `rel_parent_id`, `rel_child_id`, `rel_type`, `reverse_rel_data`') !== false)
            {
                // we have a reverse relationship
                return $query_results;
            }
            elseif (
                (strpos($query, 'SELECT t.entry_id FROM ' . ee()->db->dbprefix('channel_titles') . ' AS t') !== false)
                    or (!$noDynamic && strpos($query, 'SELECT  DISTINCT(t.entry_id),  t.entry_id, t.channel_id') !== false)
                    or (strpos($query, 'SELECT DISTINCT(t.entry_id) FROM ' . ee()->db->dbprefix('channel_titles') . ' AS t') !== false)
                    or (strpos($query, 'SELECT DISTINCT t.entry_id , t.entry_date, t.sticky FROM ' . ee()->db->dbprefix('channel_titles') . ' AS t') !== false)
                    or (strpos($query, 'SELECT DISTINCT t.entry_id , t.sticky , t.title FROM ' . ee()->db->dbprefix('channel_titles') . ' AS t') !== false)
                    or (strpos($query, 'SELECT DISTINCT t.entry_id , t.sticky FROM ' . ee()->db->dbprefix('channel_titles') . ' AS t') !== false)
                    or (strpos($query, 'SELECT DISTINCT t.entry_id, t.entry_date, t.sticky FROM ' . ee()->db->dbprefix('channel_titles') . ' AS t') !== false)
                    or (strpos($query, 'SELECT t.entry_id , exp_channels.channel_id FROM ' . ee()->db->dbprefix('channel_titles') . ' AS t') !== false)
                    or (strpos($query, 'SELECT DISTINCT t.entry_id , exp_channels.channel_id , t.sticky FROM ' . ee()->db->dbprefix('channel_titles') . ' AS t') !== false)
                )
            {
                // we have regular results
                // save LIMIT for use later
                preg_match('/LIMIT ([0-9,\s]+)$/', $query, $matches);
                if( !empty($matches) )
                {
                    $limit = $matches[1];
                    $values = explode(',', $matches[1]);
                    $limit = array(
                        'offset' => trim($values[0]),
                        'limit' => trim($values[1]),
                    );
                }

                // remove limit from query
                $select_entries_sql = preg_replace('/LIMIT [0-9,\s]+$/', '', $query);

                // save ORDER BY for later use
                preg_match('/ORDER BY (.*)$/', $select_entries_sql, $matches);
                if( !empty($matches) )
                {
                    $order_by = $matches[1];
                }

                if(!empty($select_entries_sql))
                {
                    $all_entry_ids = ee()->db->query($select_entries_sql);
                    $all_entry_ids = $all_entry_ids->result_array();
                }

                break;
            }

        }

        if( empty($all_entry_ids))
        {
            return $query_results;
        }

        $original_entry_ids = array();
        foreach( $all_entry_ids as $row )
        {
            $original_entry_ids[] = $row['entry_id'];
        }

        $notCheckForAuthorEntries = $original_entry_ids;

        $tableName = ee()->db->dbprefix('super_restricted_entries_data');
        if(ee()->config->item('super_restricted_entries_allowed_empty') == "no")
        {
            $query = "SELECT `sre_data`.`entry_id` FROM (`{$tableName}` as sre_data) WHERE
                (
                    (`sre_data`.`group_access` = '{$this->settings['logged_in_member_group_id']}' OR
                    `sre_data`.`group_access` LIKE '%|{$this->settings['logged_in_member_group_id']}|%' OR
                    `sre_data`.`group_access` LIKE '%|{$this->settings['logged_in_member_group_id']}' OR
                    `sre_data`.`group_access` LIKE '{$this->settings['logged_in_member_group_id']}|%')
                    OR
                    (`sre_data`.`member_access` = '{$this->settings['logged_in_member_member_id']}' OR
                    `sre_data`.`member_access` LIKE '%|{$this->settings['logged_in_member_member_id']}|%' OR
                    `sre_data`.`member_access` LIKE '%|{$this->settings['logged_in_member_member_id']}' OR
                    `sre_data`.`member_access` LIKE '{$this->settings['logged_in_member_member_id']}|%')
                )
                AND `sre_data`.`entry_id` IN (".implode(",", $notCheckForAuthorEntries).") ";

            $filterIds = ee()->db->query($query)->result_array();
            if(count($filterIds) == 0)
            {
                return [];
            }

            $original_entry_ids_final = array();
            foreach($filterIds as $row)
            {
                $original_entry_ids_final[] = $row['entry_id'];
            }
        }
        else
        {

            if(isset($this->settings['generalSettings']) && is_array($this->settings['generalSettings']) && isset($this->settings['generalSettings']['assign_access_to_author']) && $this->settings['generalSettings']['assign_access_to_author'] == 1)
            {

                $authorEntries = ee()->db->select('entry_id')
                ->from('channel_titles')
                ->where('author_id', $this->settings['logged_in_member_member_id'])
                ->where_in('entry_id', $original_entry_ids)->get();
                if($authorEntries->num_rows() > 0)
                {

                    $notCheckForAuthorEntries = array();
                    $authorEntries            = $authorEntries->result();
                    foreach ($authorEntries as $author)
                    {
                        $notCheckForAuthorEntries[] = $author->entry_id;
                    }

                    $notCheckForAuthorEntries = array_values(array_diff($original_entry_ids, $notCheckForAuthorEntries));
                    unset($authorEntries);

                }

            }

            $query = "SELECT `sre_data`.`entry_id` FROM (`{$tableName}` as sre_data) WHERE
                (
                    (`sre_data`.`group_access` IS NULL)
                    OR
                    (`sre_data`.`group_access` != '{$this->settings['logged_in_member_group_id']}' AND
                    `sre_data`.`group_access` NOT LIKE '%|{$this->settings['logged_in_member_group_id']}|%' AND
                    `sre_data`.`group_access` NOT LIKE '%|{$this->settings['logged_in_member_group_id']}' AND
                    `sre_data`.`group_access` NOT LIKE '{$this->settings['logged_in_member_group_id']}|%')
                ) AND (
                    (`sre_data`.`member_access` IS NULL)
                    OR
                    (`sre_data`.`member_access` != '{$this->settings['logged_in_member_member_id']}' AND
                    `sre_data`.`member_access` NOT LIKE '%|{$this->settings['logged_in_member_member_id']}|%' AND
                    `sre_data`.`member_access` NOT LIKE '%|{$this->settings['logged_in_member_member_id']}' AND
                    `sre_data`.`member_access` NOT LIKE '{$this->settings['logged_in_member_member_id']}|%')
                )
                AND `sre_data`.`entry_id` IN (".implode(",", $notCheckForAuthorEntries).") ";

            $filterIds = ee()->db->query($query)->result_array();
            if(count($filterIds) == 0)
            {
                return $query_results;
            }

            foreach($filterIds as $row)
            {
                $excludeIds[] = $row['entry_id'];
            }

            $original_entry_ids_final = array_values(array_diff($original_entry_ids, $excludeIds));
            if(empty($original_entry_ids_final))
            {
                return [];
            }

        }

        $Qlimit = ee()->TMPL->fetch_param('limit') ? ee()->TMPL->fetch_param('limit') : 100;
        $entry_ids_with_pagination = array_unique($original_entry_ids_final);
        $entry_ids_str = '';
        $startString = explode('/', $object->uristr);
        // $temp = $startString;
        // $start = $object->uri == 'index' ? 0 : preg_replace("/[^0-9,.]/", "", $object->uri );

        $data               = array();
        $search_segment     = ee()->uri->query_string;
        $start              = 0;

        if(preg_match("#^P(\d+)|/P(\d+)#", $search_segment, $match))
        {
            $start = (isset($match[2])) ? $match[2] : $match[1];
        }

        $end = count($entry_ids_with_pagination) < ($start+$Qlimit) ? count($entry_ids_with_pagination) : ($start+$Qlimit) ;
        for($i = $start; $i < $end; $i++){
            $entry_ids_str .= $entry_ids_with_pagination[$i];
            if($i != $end - 1)
            {
                $entry_ids_str .= ', ';
            }
        }

        if($entry_ids_str == "")
        {
            return [];
        }

        $sql_split = preg_split('/ WHERE /', ee()->session->cache['super_restricted_entries']['cache_sql']);
        $sql_split2 = preg_split('/ ORDER BY /', $sql_split[1]);
        if(! isset($sql_split2[1]))
        {
            $sql_split2 = preg_split('/ORDER BY /', $sql_split[1]);
        }

        $sql = $sql_split[0] . ' WHERE t.entry_id IN (' . $entry_ids_str . ') ORDER BY ' . $sql_split2[1];
        //echo "<pre>";print_r($object);
        $object->sql = $sql;
        $per_page = $object->pagination->per_page;
        $current_offset = $object->pagination->offset;
        $cur_page = ee()->pagination->cur_page;
        $results = ee()->db->query($sql);
        //$object->pagination->page_links = NULL;
        //$url = ee()->functions->fetch_site_index(1);
        $object->pagination->absolute_results = $object->absolute_results = $object->pagination->total_rows = $object->pagination->total_items = count($entry_ids_with_pagination);
        if( $Qlimit != 0 && count($entry_ids_with_pagination) != 0 )
        {
            $object->pagination->per_page = $Qlimit;
            $object->pagination->total_pages = intval(ceil($object->pagination->total_items / $Qlimit));
            $clone = new ReflectionClass($object->pagination);
            $pages = $clone->getProperty('_page_array');
            $links = $clone->getProperty('_page_links');
            $next = $clone->getProperty('_page_next');
            $previous = $clone->getProperty('_page_previous');
            $pages->setAccessible(true);
            $links->setAccessible(true);
            $next->setAccessible(true);
            $previous->setAccessible(true);
            $links->setValue($object->pagination, '');
            $next->setValue($object->pagination, '');
            $previous->setValue($object->pagination, '');
            $pages->setValue($object->pagination, array());
            $object->pagination->build(count($entry_ids_with_pagination), $Qlimit);
        }
        else
        {
            $object->pagination->total_pages = 1;
        }
        $object->pagination->cur_page = $cur_page;
        $object->pagination->offset = $current_offset;
        $object->pagination->per_page = $per_page;
        // if($object->pagination->total_pages == $object->pagination->current_page){
        //     $object->pagination->page_next = '';
        // }
        if( !empty($object->enable['categories']) )
        {
            // rebuild category data.
            $object->query = $results;
            $object->fetch_categories();
        }

        $results = $results->result_array();

        return $results;

    }

    function relationships_query($field_name, $entry_ids, $depths, $sql)
    {

        ee()->db->_reset_select();
        $data = ee()->db->query($sql)->result_array();

        if(isset(ee()->config->config['apply_super_restricted_entries_relationships']) && (strtolower(ee()->config->config['apply_super_restricted_entries_relationships']) == "yes" || strtolower(ee()->config->config['apply_super_restricted_entries_relationships']) == "y" || strtolower(ee()->config->config['apply_super_restricted_entries_relationships']) == true))
        {
            $this->apply_super_restricted_entries = true;
        }

        $tmp = strtolower(ee()->TMPL->fetch_param('apply_super_restricted_entries_relationships'));
        if($tmp == "yes" || $tmp == "y")
        {
            $this->apply_super_restricted_entries = true;
        }
        elseif($tmp == "no" || $tmp == "n")
        {
            $this->apply_super_restricted_entries = false;
        }

        $this->settings['logged_in_member_group_id']  = ee()->session->userdata('group_id');
        $this->settings['logged_in_member_member_id'] = ee()->session->userdata('member_id');

        if($this->apply_super_restricted_entries == false || empty($data) || $this->settings['logged_in_member_group_id'] == 1)
        {
            return $data;
        }

        if(! isset($this->settings['generalSettings']))
        {
            $this->settings['generalSettings'] = ee()->sreModel->getGeneralSettings();
        }

        if(isset($this->settings['generalSettings']['group_access']) && is_array($this->settings['generalSettings']['group_access']) && in_array($this->settings['logged_in_member_group_id'], $this->settings['generalSettings']['group_access']))
        {
            return $data;
        }
        elseif(isset($this->settings['generalSettings']['member_access']) && is_array($this->settings['generalSettings']['member_access']) && in_array($this->settings['logged_in_member_member_id'], $this->settings['generalSettings']['member_access']))
        {
            return $data;
        }

        $original_entry_ids = array();
        foreach ($data as $key => $value)
        {
            if(isset($value['L0_id']))
            {
                $original_entry_ids[] = $value['L0_id'];
            }
        }

        $notCheckForAuthorEntries = $original_entry_ids;
        if(isset($this->settings['generalSettings']) && is_array($this->settings['generalSettings']) && isset($this->settings['generalSettings']['assign_access_to_author']) && $this->settings['generalSettings']['assign_access_to_author'] == 1)
        {

            $authorEntries = ee()->db->select('entry_id')
            ->from('channel_titles')
            ->where('author_id', $this->settings['logged_in_member_member_id'])
            ->where_in('entry_id', $original_entry_ids)->get();
            if($authorEntries->num_rows() > 0)
            {

                $notCheckForAuthorEntries = array();
                $authorEntries            = $authorEntries->result();
                foreach ($authorEntries as $author)
                {
                    $notCheckForAuthorEntries[] = $author->entry_id;
                }

                $notCheckForAuthorEntries = array_values(array_diff($original_entry_ids, $notCheckForAuthorEntries));
                unset($authorEntries);

            }

        }

        $tableName = ee()->db->dbprefix('super_restricted_entries_data');
        if(ee()->config->item('super_restricted_entries_allowed_empty') == "no")
        {
            $query = "SELECT `sre_data`.`entry_id` FROM (`{$tableName}` as sre_data) WHERE
                (
                    (`sre_data`.`group_access` = '{$this->settings['logged_in_member_group_id']}' OR
                    `sre_data`.`group_access` LIKE '%|{$this->settings['logged_in_member_group_id']}|%' OR
                    `sre_data`.`group_access` LIKE '%|{$this->settings['logged_in_member_group_id']}' OR
                    `sre_data`.`group_access` LIKE '{$this->settings['logged_in_member_group_id']}|%')
                    OR
                    (`sre_data`.`member_access` = '{$this->settings['logged_in_member_member_id']}' OR
                    `sre_data`.`member_access` LIKE '%|{$this->settings['logged_in_member_member_id']}|%' OR
                    `sre_data`.`member_access` LIKE '%|{$this->settings['logged_in_member_member_id']}' OR
                    `sre_data`.`member_access` LIKE '{$this->settings['logged_in_member_member_id']}|%')
                )
                AND `sre_data`.`entry_id` IN (".implode(",", $notCheckForAuthorEntries).") ";

            $filterIds = ee()->db->query($query)->result_array();

            if(count($filterIds) == 0)
            {
                return [];
            }

            $original_entry_ids_final = array();
            foreach($filterIds as $row)
            {
                $original_entry_ids_final[] = $row['entry_id'];
            }
        }
        else
        {

            if(isset($this->settings['generalSettings']) && is_array($this->settings['generalSettings']) && isset($this->settings['generalSettings']['assign_access_to_author']) && $this->settings['generalSettings']['assign_access_to_author'] == 1)
            {

                $authorEntries = ee()->db->select('entry_id')
                ->from('channel_titles')
                ->where('author_id', $this->settings['logged_in_member_member_id'])
                ->where_in('entry_id', $original_entry_ids)->get();
                if($authorEntries->num_rows() > 0)
                {

                    $notCheckForAuthorEntries = array();
                    $authorEntries            = $authorEntries->result();
                    foreach ($authorEntries as $author)
                    {
                        $notCheckForAuthorEntries[] = $author->entry_id;
                    }

                    $notCheckForAuthorEntries = array_values(array_diff($original_entry_ids, $notCheckForAuthorEntries));
                    unset($authorEntries);

                }

            }

            $query = "SELECT `sre_data`.`entry_id` FROM (`{$tableName}` as sre_data) WHERE
                (
                    (`sre_data`.`group_access` IS NULL)
                    OR
                    (`sre_data`.`group_access` != '{$this->settings['logged_in_member_group_id']}' AND
                    `sre_data`.`group_access` NOT LIKE '%|{$this->settings['logged_in_member_group_id']}|%' AND
                    `sre_data`.`group_access` NOT LIKE '%|{$this->settings['logged_in_member_group_id']}' AND
                    `sre_data`.`group_access` NOT LIKE '{$this->settings['logged_in_member_group_id']}|%')
                ) AND (
                    (`sre_data`.`member_access` IS NULL)
                    OR
                    (`sre_data`.`member_access` != '{$this->settings['logged_in_member_member_id']}' AND
                    `sre_data`.`member_access` NOT LIKE '%|{$this->settings['logged_in_member_member_id']}|%' AND
                    `sre_data`.`member_access` NOT LIKE '%|{$this->settings['logged_in_member_member_id']}' AND
                    `sre_data`.`member_access` NOT LIKE '{$this->settings['logged_in_member_member_id']}|%')
                )
                AND `sre_data`.`entry_id` IN (".implode(",", $notCheckForAuthorEntries).") ";

            $filterIds = ee()->db->query($query)->result_array();
            if(count($filterIds) == 0)
            {
                return $data;
            }

            foreach($filterIds as $row)
            {
                $excludeIds[] = $row['entry_id'];
            }

            $original_entry_ids_final = array_values(array_diff($original_entry_ids, $excludeIds));
            if(empty($original_entry_ids_final))
            {
                return [];
            }

        }

        $sql_split  = preg_split('/ORDER BY/', $sql);
        $finalSql   = $sql_split[0] . " AND L0.child_id IN (". implode(",", $original_entry_ids_final) .") ORDER BY " . $sql_split[1];

        return ee()->db->query($finalSql)->result_array();

    }

    function activate_extension()
    {

        $hooks = array(
    		array(
		        'hook'     => "channel_entries_query_result",
		        'method'   => "channel_entries_query_result",
		        'priority' => 3
		    ),
            array(
                'hook'     => "relationships_query",
                'method'   => "relationships_query",
                'priority' => 3
            ),
            array(
                'hook'     => "sessions_start",
                'method'   => "sessions_start",
                'priority' => 3
            )
    	);
        $this->_registerHooks($hooks);

        return true;

    }

    function update_extension($current = '')
    {
    	if ($current == '' OR $current == $this->version)
    	{
    		return FALSE;
    	}

        ee()->db->select("*");
        ee()->db->from("extensions");
        ee()->db->where('class', __CLASS__);
        ee()->db->where('hook', "sessions_start");
        $count = ee()->db->get()->num_rows();
        if($count == 0)
        {
            $hooks = array(
                array(
                    'hook'     => "sessions_start",
                    'method'   => "sessions_start",
                    'priority' => 3
                )
            );
            $this->_registerHooks($hooks);
        }
    }

    function disable_extension()
    {
    	ee()->db->where('class', __CLASS__);
    	ee()->db->delete('extensions');
    }

	function _registerHooks($hooks)
    {

        foreach ($hooks AS $hook)
        {

            $data = array(
                'class'     => __CLASS__,
                'method'    => $hook['method'],
                'hook'      => $hook['hook'],
                'settings'  => '',
                'priority'  => $hook['priority'],
                'version'   => $this->version,
                'enabled'   => 'y'
            );

            ee()->db->insert('extensions', $data);

        }

    }

    public function sessions_start($object)
    {
        if (REQ == 'CP') {
            return false;
        }

        // tell EE to start tracking the queries
        ee('Database')->getLog()->saveQueries('y');

        // start session if it hasn't been already
        if (session_id() == '') {
            session_start();
        }
    }

}