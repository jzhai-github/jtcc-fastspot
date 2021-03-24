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

namespace Solspace\Addons\Calendar\Library\Schedule;

use Solspace\Addons\Calendar\Library\Carbon\Carbon as Carbon;

/**
 * A schedule is a mechanism for returning arrays of dates or a single day. It
 * is a nice abstraction for rendering calendar stuff
 *
 * Schedules require child classes called Types which handle the specifics of
 * its data
 *
 */
abstract class Schedule
{
    protected $current;
    protected $startDateTime;
    protected $endDateTime;
    protected $startDate;
    protected $endDate;

    /**
     * Takes a startTime to build a Schedule object and an optional endTime
     * depending on the Schedule Type
     *
     * @param Carbon $startDateTime The start datetime of the schedule
     * @param Carbon $endDateTime   The end datetime of the schedule
     */
    public function __construct(Carbon $startDateTime, Carbon $endDateTime)
    {
        if (isset($endDateTime) && $startDateTime->gt($endDateTime)) {
            $endDateTime = $startDateTime->copy();
        }

        $this->startDateTime = $startDateTime;
        $this->endDateTime   = $endDateTime;
        $this->current       = $startDateTime->copy();
        $this->startDate     = $startDateTime->toDateString();
        if (isset($this->endDateTime)) {
            $this->endDate = $endDateTime->toDateString();
        }
    }

    /**
     * Returns the start datetime Carbon object
     *
     * @return Carbon The start datetime
     */
    public function getStartDateTime()
    {
        return $this->startDateTime->copy();
    }

    /**
     * Returns the end datetime Carbon object
     *
     * @return Carbon The end datetime
     */
    public function getEndDateTime()
    {
        return $this->endDateTime->copy();
    }

    /**
     * Returns the start date in format (YYYY-MM-DD)
     *
     * @return string The start date
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Returns the end date in format (YYYY-MM-DD)
     *
     * @return string The end date
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Implemented for RecursiveIterators
     *
     * @return mixed The currenty loop data
     */
    public function getChildren()
    {
        return $this->current();
    }

    /**
     * @return boolean [description]
     */
    public function hasChildren()
    {
        return (
            is_array($this->current) || $this->current() instanceof Traversable
        );
    }

    /**
     * This static method handles conversions for weeks of a month or just a
     * week based on the start date provided to it
     *
     * @param  int    $startDay The starting day of the week (mon, tues)
     * @param  Carbon $time     The Carbon object for the date to use for formatting
     *
     * @return array Two Carbon objects, a start date and an end date
     */
    public static function getSectionByTime($startDay, Carbon $time)
    {
        $dayOfWeek = $time->dayOfWeek;

        if ($dayOfWeek < $startDay) {
            $difference = abs($dayOfWeek - $startDay);
            $endDate    = $time->copy()->addDays($difference - 1);
            $startDate  = $endDate->copy()->subDays(6);
        } elseif ($dayOfWeek > $startDay) {
            $difference = $dayOfWeek - $startDay;
            $startDate  = $time->copy()->subDays($difference);
            $endDate    = $startDate->copy()->addDays(6);
        } else {
            $startDate = $time;
            $endDate   = $time->copy()->addDays(6);
        }

        return array($startDate, $endDate);
    }
}
