<?php

use ExpressionEngine\Service\JumpMenu\AbstractJumpMenu;

class rocket_jump extends AbstractJumpMenu
{
    protected static $items = [
        [
            'commandTitle' => 'settings',
            'icon' => 'fa-rocket',
            'command' => 'rocket settings',
            'command_title' => 'Settings',
            'target' => '',
        ],
        [
            'commandTitle' => 'clearCache',
            'icon' => 'fa-rocket',
            'command' => 'clear rocket cache',
            'command_title' => 'Clear <b>cache</b>',
            'target' => 'action_purge_cache',
        ],
    ];
}
