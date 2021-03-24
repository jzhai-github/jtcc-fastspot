<?php

namespace EEHarbor\Safeharbor\Conduit;

use EEHarbor\Safeharbor\FluxCapacitor\Conduit\McpNav as FluxNav;

class McpNav extends FluxNav
{
    protected function defaultItems()
    {
        $this->setToolbarIcon();

        $default_items = array(
            'index' => lang('home'),
            'settings' => lang('settings'),
        );

        return $default_items;
    }

    protected function defaultActiveMap()
    {
        return array('safeharbor' => 'index');
    }
}
