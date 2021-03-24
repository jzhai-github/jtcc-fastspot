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

use RecursiveIterator;
use Solspace\Addons\Calendar\Library\Schedule\Schedule as Schedule;

/**
 * Week is a dumb Type. It assumes 7 days always and that the startDate
 * is the beginning. It does nothing more but count and instantiate instances
 * for each of the days.
 *
 * It will always have 7 items. Always.
 */
class Week extends Schedule implements RecursiveIterator
{
	private $counter = 0;

	/**
	 * Creates Day objects
	 * @return Day The day object
	 */
	public function current()
	{
		return new Day($this->current->copy());
	}

	/**
	 * Returns the counter number as the key of the array, zero-based
	 * @return int Array index
	 */
	public function key()
	{
		return $this->counter;
	}

	/**
	 * Adds to the counter and adds a Day to the current Carbon object
	 */
	public function next()
	{
		$this->counter += 1;
		$this->current->addDay();
	}

	/**
	 * Sets the array pointer back to the start datetime object and
	 * resets the counter to 0
	 */
	public function rewind()
	{
		$this->counter = 0;
		$this->current = $this->getStartDateTime();
	}

	/**
	 * Checks that the counter isn't greater than 6
	 * @return bool Whether the loop should continue or not
	 */
	public function valid()
	{
		return ($this->counter <= 6);
	}
}
