<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include(PATH_THIRD.'/too_many_cooks/config.php');		

class Too_many_cooks_upd {
	
	public $version = TOO_MANY_COOKS_VERSION;

	public function __construct()
	{
	}

	public function install()
	{
		// Module
		$mod_data = array(
			'module_name'			=> 'Too_many_cooks',
			'module_version'		=> $this->version,
			'has_cp_backend'		=> 'y',
			'has_publish_fields'	=> 'n'
		);
		ee()->db->insert('modules', $mod_data);
		
		// Table
		ee()->load->dbforge();
		ee()->dbforge->add_field(
			array(
				'id' => array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => TRUE, 'null' => FALSE),
				'entry_id' => array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'null' => FALSE),
				'session_id' => array('type' => 'varchar', 'constraint' => '40', 'null' => FALSE),
				'member_id' => array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'null' => FALSE),
				'expires' => array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'null' => FALSE)
			)
		);
		ee()->dbforge->add_key(array('id'), TRUE);
		ee()->dbforge->add_key(array('entry_id', 'session_id'));
		ee()->dbforge->create_table('too_many_cooks');
		
		// Extension
		$hooks = array(
			'publish_form_entry_data' => 'publish_form_entry_data',
			'entry_submission_end' => 'entry_submission_end',
		);

		foreach ($hooks as $hook => $method)
		{
			$data = array(
				'class'		=> 'Too_many_cooks_ext',
				'method'	=> $method,
				'hook'		=> $hook,
				'settings'	=> serialize(array()),
				'version'	=> $this->version,
				'enabled'	=> 'y'
			);

			ee()->db->insert('extensions', $data);			
		}
		
		return TRUE;
	}

	public function uninstall()
	{
		// Module
		$mod_id = ee()->db->select('module_id')
			->get_where('modules', array(
				'module_name' => 'Too_many_cooks'
			))->row('module_id');
		ee()->db->where('module_id', $mod_id)->delete('module_member_groups');
		ee()->db->where('module_name', 'Too_many_cooks')->delete('modules');
		
		// Table
		ee()->load->dbforge();
		ee()->dbforge->drop_table('too_many_cooks');
		
		// Extension
		ee()->db->where('class', 'Too_many_cooks_ext')->delete('extensions');
		
		return TRUE;
	}
	
	public function update($current = '')
	{
		return TRUE;
	}
	
}
