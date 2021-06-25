<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Ferret_mcp
{
    protected $config;
    protected $settings;

    /**
     * Ferret_mcp constructor.
     */
    public function __construct()
    {
        $this->config = include 'config/config.php';
        $this->settings = FerretHelper::getSettings();

        ee()->load->library('FerretViews', ['settings' => $this->settings, 'config' => $this->config]);
        ee()->load->library('FerretSettings', ['settings' => $this->settings, 'config' => $this->config]);
    }

    /**
     * Core settings view for selecting engine and inputting credentials
     *
     * @return string
     */
    public function index()
    {
        ee()->ferretsettings->index();

        $vars = [
            'base_url' => ee('CP/URL', 'addons/settings/ferret'),
            'cp_page_title' => lang('Ferret_module_name'),
            'save_btn_text' => 'Save Settings',
            'save_btn_text_working' => 'Saving',
            'sections' => ee()->ferretviews->index(),
        ];

        $this->buildSidebar();

        ee()->cp->add_js_script([
            'file' => ['cp/form_group'],
        ]);

        return ee('View')->make('ferret:index')->render($vars);
    }

    /**
     * Settings page to add / delete indexes
     *
     * @return string
     */
    public function add_remove()
    {
        ee()->ferretsettings->addRemove();

        $vars = [
            'base_url' => ee('CP/URL', 'addons/settings/ferret/add_remove'),
            'cp_page_title' => lang('Ferret_module_name'),
            'save_btn_text' => 'Update Indexes',
            'save_btn_text_working' => 'Updating',
            'sections' => ee()->ferretviews->addRemove(),
        ];

        $this->buildSidebar();

        return ee('View')->make('ferret:index')->render($vars);
    }

    /**
     * @return string
     */
    public function indexes()
    {
        $baseUrl = ee('CP/URL', 'addons/settings/ferret/indexes/' . ee()->uri->segment(6) . '/' . ee()->uri->segment(7));
        $segment = ee()->uri->segment(7);

        ee()->ferretsettings->$segment();

        $vars = [
            'base_url' => $baseUrl,
            'cp_page_title' => lang($segment),
            'save_btn_text' => $segment . '_save',
            'save_btn_text_working' => $segment . '_working',
            'sections' => ee()->ferretviews->$segment(),
        ];

        $this->buildSidebar();

        return ee('View')->make('ferret:index')->render($vars);
    }

    /**
     * Creates the sidebar
     */
    protected function buildSidebar()
    {
        $indexes = ee('Model')->get('ferret:FerretIndex')->all();
        $sidebar = ee('CP/Sidebar')->make();
        $header = $sidebar->addHeader('Ferret', ee('CP/URL', 'addons/settings/ferret'));

        if (isset($this->settings['credentials']) && $this->settings['credentials']) {
            $header = $sidebar->addHeader('Add / Remove Indexes', ee('CP/URL', 'addons/settings/ferret/add_remove'));
        }

        foreach ($indexes as $index) {
            $header = $sidebar->addHeader('Index: ' . $index->name);

            if ($index->fields) {
                $header->withButton('Build Index', ee('CP/URL', 'addons/settings/ferret/indexes/' . $index->name . '/build'));
            }

            $basicList = $header->addBasicList();
            $basicList->addItem('Fields', ee('CP/URL', 'addons/settings/ferret/indexes/' . $index->name . '/fields'));

            if ($index->fields) {
                $basicList->addItem('Categories', ee('CP/URL', 'addons/settings/ferret/indexes/' . $index->name . '/categories'));
                $basicList->addItem('Mapping', ee('CP/URL', 'addons/settings/ferret/indexes/' . $index->name . '/mapping'));
                $basicList->addItem('Paths', ee('CP/URL', 'addons/settings/ferret/indexes/' . $index->name . '/paths'));
                $basicList->addItem('Clear Index', ee('CP/URL', 'addons/settings/ferret/indexes/' . $index->name . '/clear'));
            }
        }
    }
}
