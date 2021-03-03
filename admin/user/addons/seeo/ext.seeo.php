<?php
include_once 'addon.setup.php';

use EEHarbor\Seeo\FluxCapacitor\Base\Ext;

/**
 * class Seeo_ext
 *
 * @package         SEEO
 * @author          Tom Jaeger <Tom@EEHarbor.com>
 * @link            https://eeharbor.com
 * @copyright       Copyright (c) 2018, Tom Jaeger/EEHarbor
 */
class Seeo_ext extends Ext
{
    //----------------------------------------------------------------------------
    // PROPERTIES
    //----------------------------------------------------------------------------

    public $settings_exist = true;
    public $version = SEEO_VERSION;
    public $settings = array();
    public $required_by = array('module');

    //----------------------------------------------------------------------------
    // METHODS
    //----------------------------------------------------------------------------

    /**
     * Constructor
     *
     * @access public
     * @param array
     * @return void
     */
    public function __construct($settings = array())
    {
        parent::__construct();

        // ee()->load->add_package_path(PATH_THIRD . 'seeo');
        // ee()->load->library(array('seeo_settings'));
        // ee()->lang->loadfile('seeo');

        // Set the settings object
        // ee()->seeo_settings->set($settings);

        // Assign the settings
        // $this->settings = ee()->seeo_settings->get();
    }

    //----------------------------------------------------------------------------

    /**
     * Settings are handled in the Module
     *
     * @access public
     * @return void
     */
    public function settings()
    {
        ee()->functions->redirect($this->flux->moduleURL('index'));
    }

    public function updateExtension($current = '')
    {
        return true;
    }

    //----------------------------------------------------------------------------
    // HOOKS
    //----------------------------------------------------------------------------
}
