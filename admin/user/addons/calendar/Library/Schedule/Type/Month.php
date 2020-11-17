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
use Solspace\Addons\Calendar\Helpers\DateTimeHelper;
use Solspace\Addons\Calendar\Library\Carbon\Carbon as Carbon;
use Solspace\Addons\Calendar\Library\Schedule\Schedule as Schedule;

/**
 * Month Type
 *
 * The Month Schedule returns an array of weeks in the form of a month. It will
 * always return the full week of the start date in terms of a starting day.
 *
 * So, for example if you pass it a datetime object with July 2013 as the start
 * date, the first object in the array will be a Week type with a start date
 * of Sunday, June 30th because the default date is Sunday.
 *
 * If you want to change the default week start date, this Type has the
 * setFirstDayOfWeek method. You must pass in a Carbon constant.
 *
 */
class Month extends Schedule implements RecursiveIterator
{
    protected $firstDayOfWeek = Carbon::SUNDAY;
    private   $counter        = 0;

    /**
     * Gets the Week object
     *
     * @return array An array of Week objects for the month
     */
    public function current()
    {
        return $this->createDataArray($this->current);
    }

    /**
     * Lets you set when a Week "begins"
     *
     * @param  int $day The numerical day of the week zero-indexed from Sunday
     *
     * @return null
     */
    public function setfirstDayOfWeek($day = Carbon::SUNDAY)
    {
        $this->firstDayOfWeek = $day;
    }

    /**
     * Returns the first day of the week
     *
     * @return int First day of the week in int form, zero-indexed from Sunday
     */
    public function getFirstDayOfWeek()
    {
        return $this->firstDayOfWeek;
    }

    /**
     * Sets the month back to the starting month
     */
    public function rewind()
    {
        $this->current->month = $this->getStartDateTime()->month;
    }

    /**
     * Adds the next month to stop the iteration
     */
    public function next()
    {
        $this->current->addMonth();
    }

    /**
     * The numerical month
     *
     * @return int month
     */
    public function key()
    {
        return $this->current->month;
    }

    /**
     * Checks that the current loop does not go beyond the end date month
     *
     * @return bool Is the next iteration valid
     */
    public function valid()
    {
        return $this->key() == $this->getStartDateTime()->month;
    }

    /**
     * Creates an array of week objects based on when a week begins.
     *
     * Weeks contain seven days, so if the first day of the month is a Tuesday
     * and the first day of week is a Sunday, this will return the 3 days before
     * the start of the month
     *
     * @param Carbon $currentMonth A Carbon object with the month to create
     *
     * @return array An array of Week types for a given month
     */
    public function createDataArray(Carbon $currentMonth)
    {
        $firstDayOfWeek = $this->getFirstDayOfWeek();
        // Get the first day
        $firstDayOfMonth = Carbon::createFromDate(
            $currentMonth->year,
            $currentMonth->month,
            1,
            DateTimeHelper::getUserTimezone()
        );

        list($startDate, $endDate) =
            $this->getSectionByTime($firstDayOfWeek, $firstDayOfMonth);

        $data    = array();
        $counter = 0;
        while ($endDate->month == $currentMonth->month ||
            $startDate->month == $currentMonth->month) {
            $data[$counter] = new Week($startDate, $endDate);
            $startDate      = $endDate->copy()->addDay();
            $endDate        = $startDate->copy()->addDays(6);
            $counter += 1;
        }


        return $data;
    }
}
