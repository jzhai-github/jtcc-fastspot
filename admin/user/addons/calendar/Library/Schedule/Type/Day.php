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

namespace Solspace\Addons\Calendar\Library\Schedule\Type;

use Solspace\Addons\Calendar\Library\Carbon\Carbon as Carbon;

/**
 * A day doesn't iterate. It just has a time and does an equality check
 */
class Day
{
	private $datetime;

	/**
	 * The constructor, silly
	 * @param Carbon $datetime The day to set
	 */
	public function __construct(Carbon $datetime)
	{
		$this->datetime = $datetime;
	}

	/**
	 * Returns the date as a Carbon object
	 * @return Carbon The datetime object
	 */
	public function getDateTime()
	{
		return $this->datetime;
	}

	/**
	 * Checks to see if another datetime is equal to this day
	 * @param  Carbon  $datetime The datetime to compare to
	 * @return boolean Is it the same day?
	 */
	public function isEqual(Carbon $datetime)
	{
		if ($datetime->year === $this->datetime->year &&
			$datetime->month === $this->datetime->month &&
			$datetime->day === $this->datetime->day) {
			return true;
		}

		return false;
	}
}
