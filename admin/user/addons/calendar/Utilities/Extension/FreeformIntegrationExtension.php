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

namespace Solspace\Addons\Calendar\Utilities\Extension;

use Solspace\Addons\Calendar\Utilities\Extension;

abstract class FreeformIntegrationExtension extends Extension
{
    const REGISTER_TYPES_METHOD      = 'registerIntegrations';
    const HOOK_REGISTER_INTEGRATIONS = 'freeform_next.registerIntegrations';

    public $version = '1.0.0';

    /**
     * @return Hook[]
     */
    public function getHooks()
    {
        return array(
            new Hook(
                get_class($this),
                self::REGISTER_TYPES_METHOD,
                self::HOOK_REGISTER_INTEGRATIONS,
                $this->version
            ),
        );
    }

    /**
     * @return array
     */
    abstract public function registerIntegrations();
}
