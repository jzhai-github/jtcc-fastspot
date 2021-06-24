<?php

require_once 'vendor/autoload.php';

class Ferret_ext
{
    public $name = 'Ferret';
    public $version = '1.0.0';
    public $description = 'Handles indexing of ExpressionEngine entries';
    public $settings_exist = 'n';
    public $docs_url = '';
    public $settings = [];
    public $hooksAndMethods = [
        'core_boot' => 'core_boot',
        'after_channel_entry_save' => 'after_channel_entry_save',
        'before_channel_entry_delete' => 'before_channel_entry_delete',
        'cp_custom_menu' => 'cp_custom_menu',
    ];

    protected $indexer = null;
    protected $config = null;
    protected $specificIndex = false;

    public function __construct($settings = '')
    {
        $this->settings = $settings;

        if (isset($this->settings['engine']) && !empty($this->settings['engine'])) {
            $engine = 'fostermade\ferret\engines\\' . $this->settings['engine'];
            $this->indexer = new $engine($this->settings);
        }

        $this->config = include __DIR__ . '/config/config.php';
    }

    public function activate_extension()
    {
        $this->settings = [];

        foreach ($this->hooksAndMethods as $hook => $method) {
            $data = [
                'class' => __CLASS__,
                'method' => $method,
                'hook' => $hook,
                'settings' => serialize($this->settings),
                'priority' => 10,
                'version' => $this->version,
                'enabled' => 'y',
            ];

            ee()->db->insert('extensions', $data);
        }
    }

    public function update_extension($current = '')
    {
        if ($current == '' or $current == $this->version) {
            return false;
        }

        if ($current < '1.0.0') {
            // Update to version 1.0.0
        }

        ee()->db->where('class', __CLASS__);
        ee()->db->update(
            'extensions',
            ['version' => $this->version]
        );
    }

    public function disable_extension()
    {
        ee()->db->where('class', __CLASS__);
        ee()->db->delete('extensions');
    }

    /**
     * Sets app id, search only api key, and index name as
     * ExpressionEngine global variables
     */
    public function core_boot()
    {
        $credentials = isset($this->settings['credentials']['algolia']) ? $this->settings['credentials']['algolia'] : null;

        ee()->config->_global_vars['ferret_app_id'] = $credentials ? $credentials['app_id'] : null;
        ee()->config->_global_vars['ferret_api_key'] = $credentials ? $credentials['search_key'] : null;
        ee()->config->_global_vars['ferret_env'] = defined('ENV') ? ENV . '_' : '';
    }

    /**
     * Adds a single entry to all mapped indexes
     *
     * @param $entry
     * @param array $values
     * @return mixed
     */
    public function after_channel_entry_save($entry, $values = [])
    {
        ee()->load->library('FerretIndexer', ['settings' => $this->settings]);
        ee()->ferretindexer->delete($entry->entry_id);

        ee()->load->library('FerretBuilder', [
            'settings' => $this->settings,
            'config' => $this->config,
        ]);

        $success = ee()->ferretbuilder->entry($entry);

        if (!$success) {
            $this->setAlert('Failed building entry: ' . $entry->getId());
        }

        $success = ee()->ferretindexer->index();

        return $success;
    }

    /**
     * Deletes a single entry from all mapped indexes
     *
     * @param $entry
     * @param array $values
     * @return bool
     */
    public function before_channel_entry_delete($entry, $values = [])
    {
        ee()->load->library('FerretIndexer', ['settings' => $this->settings]);
        ee()->ferretindexer->delete($entry->entry_id);
    }

    /**
     * Easily add Ferret to CP menu
     *
     * @param $menu
     * @return bool
     */
    public function cp_custom_menu($menu)
    {
        if (REQ != 'CP') {
            return true;
        }

        $menu->additem('Ferret', ee('CP/URL')->make('addons/settings/ferret'));
    }

    /**
     * Sets a control panel alert for failed actions
     *
     * @param $success
     * @return bool
     */
    protected function setAlert($success)
    {
        if ($success !== true) {
            ee('CP/Alert')
                ->makeInline('Error')
                ->asIssue()
                ->addToBody('Ferret: ' . $success)
                ->defer();

            return false;
        }
    }
}
