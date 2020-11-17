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

namespace Solspace\Addons\Calendar\Library\RRule\Frequency;

/**
 * The Weekly RRule frequency
 */
class Weekly extends Frequency
{
	/**
	 * Returns the weekly RRule based on certain parameters
	 * @return string The RRule string
	 */
	public function getRRule()
	{
		parent::getRRule();
		$this->setByDay();
		return $this->rrule->getRRule();
	}
}
