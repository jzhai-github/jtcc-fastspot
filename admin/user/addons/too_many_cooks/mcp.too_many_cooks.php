<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Too_many_cooks_mcp {
	
	public function __construct(){}
	
	public function index()
	{
		$vars = array(
			'message' => lang('tmc_no_settings')
		);
		return ee('View')->make('too_many_cooks:index')->render($vars);
	}
	
	public function check_session()
	{
		header("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0");
		header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
		header("Pragma: no-cache");
		if($entry_id = ee()->input->get_post('entry_id'))
		{
			if($session = ee('too_many_cooks:Sessions')->get_session($entry_id))
			{
				exit(json_encode($session));
			}
			else
			{
				exit('FREE');
			}
		}
	}
	
	public function maintain_session()
	{
		header("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0");
		header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
		header("Pragma: no-cache");
		if($entry_id = ee()->input->get_post('entry_id'))
		{
			ee('too_many_cooks:Sessions')->insert_session($entry_id);
		}
		return true;
	}
}