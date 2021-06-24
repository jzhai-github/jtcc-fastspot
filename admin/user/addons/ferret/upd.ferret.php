<?php

class Ferret_upd
{
    public $version = '1.0.0';

    /**
     * ExpressionEngine Add-On Install Method (Required)
     * @return bool
     */
    public function install()
    {
        $data = [
            'module_name' => 'Ferret',
            'module_version' => $this->version,
            'has_cp_backend' => 'y',
            'has_publish_fields' => 'n',
        ];

        ee()->db->insert('modules', $data);

        $this->createTables();

        return true;
    }

    /**
     * ExpressionEngine Add-On Update Method (Required)
     * @param string $current
     * @return bool
     */
    public function update($current = '')
    {
        return true;
    }

    /**
     * ExpressionEngine Add-On Uninstall Method (Required)
     * @return bool
     */
    public function uninstall()
    {
        ee()->db->where('module_name', 'Ferret');
        ee()->db->delete('modules');

        ee()->load->dbforge();
        ee()->dbforge->drop_table('ferret_indexes');
        ee()->dbforge->drop_table('ferret_objects');
        ee()->dbforge->drop_table('ferret_records');

        return true;
    }

    /**
     * Create Ferret tables
     */
    private function createTables()
    {
        ee()->load->dbforge();

        // Indexes
        $fields = [
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 128,
            ],
            'fields' => [
                'type' => 'TEXT',
            ],
            'categories' => [
                'type' => 'TEXT',
            ],
            'paths' => [
                'type' => 'TEXT',
            ],
        ];

        ee()->dbforge->add_field('id');
        ee()->dbforge->add_field($fields);
        ee()->dbforge->add_key('name');
        ee()->dbforge->create_table('ferret_indexes');

        // Objects
        $fields = [
            'index' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
            ],
            'entry_id' => [
                'type' => 'INT',
                'constraint' => '5',
                'unsigned' => true,
            ],
            'mapping' => [
                'type' => 'LONGTEXT',
            ],
        ];

        ee()->dbforge->add_field('id');
        ee()->dbforge->add_field($fields);
        ee()->dbforge->create_table('ferret_objects');

        // Records
        $fields = [
            'objectID' => [
                'type' => 'INT',
                'constraint' => '5',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'index' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
            ],
            'entry_id' => [
                'type' => 'INT',
                'constraint' => '5',
                'unsigned' => true,
            ],
            'order' => [
                'type' => 'INT',
                'constraint' => '5',
                'unsigned' => true,
            ],
        ];

        ee()->dbforge->add_field($fields);
        ee()->dbforge->add_key('objectId', true);
        ee()->dbforge->create_table('ferret_records');
    }
}
