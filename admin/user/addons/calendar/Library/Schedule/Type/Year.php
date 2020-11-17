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

use Iterator;
use Solspace\Addons\Calendar\Library\Schedule\Schedule as Schedule;

/**
 * Year returns a bunch of month iterators for a set time period
 */
class Year extends Schedule implements Iterator
{
	/**
	 * Returns the current endMonth by year
	 * @return array The current year by month
	 */
	public function current()
	{
		$startMonth = $this->current->month;
		$endDateTime = $this->getEndDateTime();
		$endMonth = 12;
		if ($endDateTime->year == $this->current->year) {
			$endMonth = $endDateTime->month;
		}

		return array($this->key()
			=> $this->createDataArray($startMonth, $endMonth));
	}

	/**
	 * The next year
	 * @return null
	 */
	public function next()
	{
		$this->current->addYear();
		$this->current->month = 1;
	}

	/**
	 * Goes back to the given start_date
	 * @return null
	 */
	public function rewind()
	{
		$this->current = $this->getStartDateTime();
	}

	/**
	 * Gets the current year for the iteration
	 * @return string The Year in the iteration
	 */
	public function key()
	{
		return $this->current->year;
	}

	/**
	 * Creates an array of months for each year
	 * @param  int $startMonth The month to start with in 2 digit format
	 * @param  int $endMonth   The month to end with in 2 digit format
	 * @return array An array of Month objects
	 */
	public function createDataArray($startMonth, $endMonth)
	{
		$data = array();
		for ($i = $startMonth; $i <= $endMonth; $i++) {
			$start = $this->startDateTime->createFromDate($this->current->year, $i);
			$end = $start->copy();
			$end->day = $start->daysInMonth;
			$data[$i] = new Month($start, $end);
			unset($start, $end);
		}
		return $data;
	}

	/**
	 * Checks to see if the year in the iteration is in the loop
	 * @return bool Is this year in the loop?
	 */
	public function valid()
	{
		return $this->key() <= $this->getEndDateTime()->year;
	}
}
