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
 * The Monthly frequency of an RRule
 */
class Monthly extends Frequency
{

	/**
	 * Returns the Monthly rrule based on certain parameters
	 * @return string The RRule string
	 */
	public function getRRule()
	{
		parent::getRRule();

		if ($this->rrule['bymonthdayorbyday'] == 'byday' ||
			isset($this->rrule['byday'])) {
			$this->setByDay();
		} else {
			$this->setWithArray('bymonthday');
		}
		return $this->rrule->getRRule();
	}
}
