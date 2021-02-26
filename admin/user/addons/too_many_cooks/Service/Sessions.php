<?php
	
namespace Amphibian\TooManyCooks\Service;

class Sessions
{
	public $seconds = 10;
	
	function __construct(){}
	
	function insert_session($entry_id)
	{
		$data = array(
			'session_id' => ee()->session->userdata('session_id'),
			'member_id' => ee()->session->userdata('member_id'),
			'entry_id'=> $entry_id,
			'expires' => ee()->localize->now + $this->seconds + 5
		);
		ee()->db->insert('too_many_cooks', $data);
	}
	
	function get_session($entry_id)
	{
		$sessions = ee()->db->query("
			SELECT tmc.*, m.screen_name 
			FROM exp_too_many_cooks tmc  
			LEFT JOIN exp_members m 
			ON tmc.member_id = m.member_id
			WHERE tmc.entry_id = ".ee()->db->escape_str($entry_id)." 
			AND tmc.member_id != ".ee()->session->userdata('member_id')."
			AND tmc.expires > ".ee()->localize->now." LIMIT 1"	
		);
		if($sessions->num_rows() == 1)
		{
			return $sessions->row_array();
		}
		return false;
	}
	
	function delete_session($entry_id)
	{
		ee()->db->where('session_id', ee()->session->userdata('session_id'));
		ee()->db->where('member_id', ee()->session->userdata('member_id'));
		ee()->db->where('entry_id', $entry_id);
		ee()->db->delete('too_many_cooks');
	}
	
	function delete_expired_sessions()
	{
		ee()->db->where('expires <=', ee()->localize->now)->delete('too_many_cooks');
	}
	
	function get_action_url($action)
	{
		return ee('CP/URL', 'addons/settings/too_many_cooks/'.$action);
	}
	
}