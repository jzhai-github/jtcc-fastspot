<?php
/**
 * Calendar for ExpressionEngine
 *
 * @package       Solspace:Calendar
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2010-2020, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/calendar/
 * @license       https://docs.solspace.com/license-agreement/
 */

namespace Solspace\Addons\Calendar\Utilities;

use Solspace\Addons\Calendar\Utilities\Extension\Hook;

abstract class Extension
{
    /**
     * @return Hook[]
     */
    abstract public function getHooks();

    /**
     * Installs all hooks
     */
    public final function activate_extension()
    {
        foreach ($this->getHooks() as $hook) {
            ee()->db
                ->insert(
                    'extensions',
                    array(
                        'class'    => $hook->getClass(),
                        'method'   => $hook->getMethod(),
                        'hook'     => $hook->getHook() ?: $hook->getMethod(),
                        'settings' => $hook->getSettings() ? json_encode($hook->getSettings()) : '',
                        'priority' => $hook->getPriority(),
                        'version'  => $hook->getVersion(),
                        'enabled'  => $hook->isEnabled() ? 'y' : 'n',
                    )
                );
        }
    }

    /**
     * Removes all hooks
     */
    public final function disable_extension()
    {
        foreach ($this->getHooks() as $hook) {
            ee()->db
                ->where('class', $hook->getClass())
                ->where('method', $hook->getMethod())
                ->where('hook', $hook->getHook() ?: $hook->getMethod())
                ->delete('exp_extensions');
        }
    }
}
