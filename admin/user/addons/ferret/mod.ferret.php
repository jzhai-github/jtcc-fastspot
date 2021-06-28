<?php

class Ferret
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

        ee()->load->library('FerretSettings', ['settings' => $this->settings, 'config' => $this->config]);
    }

    /**
     * Template tag makes Engine credentials available on the window object.
     * @TODO Make dynamic based on the selected engine.
     * @return string
     */
    public function credentials()
    {
        $settings = [];

        foreach (['app_id', 'search_key'] as $key) {
            if (isset($this->settings['credentials']['algolia'][$key])) {
                $settings[$key] = $this->settings['credentials']['algolia'][$key];
            }
        }

        return "<script> window.algolia=".json_encode($settings)."</script>";
    }
}
