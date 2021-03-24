<?php

/**
 * Extension for Safe Harbor
 *
 * @package             Safe Harbor
 * @author              Tom Jaeger (tom@eeharbor.com)
 * @copyright           Copyright (c) 2017 EEHarbor
 */

use EEHarbor\Safeharbor\FluxCapacitor\Base\Ext;

class Safeharbor_ext extends Ext
{
    public $name           = 'Safeharbor';
    public $description    = 'Backups!';
    public $settings_exist = 'n';
    public $settings       = array();
    public $docs_url       = 'http://eeharbor.com/safeharbor';
    public $hooks          = array('cp_custom_menu');

    public function __construct($settings = '')
    {
        parent::__construct();
    }

    /**
     * Activate Extension
     * @return void
     */
    public function activateExtension()
    {
        parent::activateExtension();
    }

    /**
     * Update Extension
     * @return  mixed   void on update / false if none
     */
    public function updateExtension($current = false)
    {
        if (! $current || $current == $this->version) {
            return false;
        }

        return $this->updateVersion();
    }
}
