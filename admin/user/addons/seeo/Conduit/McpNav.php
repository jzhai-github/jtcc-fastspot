<?php

namespace EEHarbor\Seeo\Conduit;

use EEHarbor\Seeo\Helpers\MigrationSource;
use EEHarbor\Seeo\FluxCapacitor\Conduit\McpNav as FluxNav;

class McpNav extends FluxNav
{
    protected function defaultItems()
    {
        $this->setToolbarIcon(lang('seeo_nav_settings'), 'default_settings');

        $default_items = array(
            '/' => lang('seeo_nav_audit'),
            'template_page_meta' => lang('seeo_nav_template_page_meta'),
            'default_settings' => lang('seeo_nav_settings'),
            'setup_migrate' => lang('seeo_nav_migrate'),
            'https://eeharbor.com/seeo/documentation' => lang('seeo_nav_documentation'),
            'license' => lang('seeo_nav_license'),
        );

        if (MigrationSource::getAllSources()->count()) {
            $default_items['setup_migrate'] .= '<span class="st-note" style="float:right; padding-left: 8px;">Sources Found</span>';
        }

        return $default_items;
    }


    protected function defaultButtons()
    {
        return array(
            'template_page_meta' => array('template_page_meta_create_edit' => lang('new')),
        );
    }

    protected function defaultActiveMap()
    {
        return array(
            'meta' => '/',
            'template_page_meta_create_edit' => 'template_page_meta',
            'confirm_migrate' => 'setup_migrate',
        );
    }

    public function postGenerateNav()
    {
        // Make settings a folder list
        // $settings_folder = $this->nav_items['settings']->addBasicList('settings_items');
        // $settings_folder->addItem(lang('seeo_nav_default_settings'), $this->flux->moduleUrl('default_settings'));
        // $settings_folder->addItem(lang('seeo_nav_channel_settings'), $this->flux->moduleUrl('channel_settings'));
    }
}
