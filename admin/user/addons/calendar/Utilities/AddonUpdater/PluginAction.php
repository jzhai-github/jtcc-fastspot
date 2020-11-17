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

namespace Solspace\Addons\Calendar\Utilities\AddonUpdater;

class PluginAction
{
    /** @var string */
    private $methodName;

    /** @var string */
    private $className;

    /** @var bool */
    private $csrfExempt;

    /**
     * PluginAction constructor.
     *
     * @param string $methodName
     * @param string $className
     * @param bool   $isCsrfExempt
     */
    public function __construct($methodName, $className, $isCsrfExempt = false)
    {
        $this->methodName = $methodName;
        $this->className  = $className;
        $this->csrfExempt = $isCsrfExempt;
    }

    /**
     * @return string
     */
    public function getMethodName()
    {
        return $this->methodName;
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * @return bool
     */
    public function isCsrfExempt()
    {
        return $this->csrfExempt;
    }
}
