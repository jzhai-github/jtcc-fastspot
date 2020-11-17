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
 * The Yearly RRUle Frequency
 */
class Yearly extends Frequency
{
	/**
	 * Returns the Yearly rrule based on certain parameters
	 * @return string The RRule string
	 */
	public function getRRule()
	{
		parent::getRRule();
		$this->setWithArray('bymonth');
		$this->setByDay();
		return $this->rrule->getRRule();
	}
}
