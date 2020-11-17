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

namespace Solspace\Addons\Calendar\Model;

use EllisLab\ExpressionEngine\Model\Channel\ChannelEntry;
use EllisLab\ExpressionEngine\Service\Model\Model;
use Solspace\Addons\Calendar\Helpers\DateTimeHelper;
use Solspace\Addons\Calendar\Library\Carbon\Carbon;
use Solspace\Addons\Calendar\Library\RRule\Frequency\Frequency;
use Solspace\Addons\Calendar\Library\RRule\RRule;
use Solspace\Addons\Calendar\Library\When\InvalidStartDate;
use Solspace\Addons\Calendar\Library\When\When;
use Solspace\Addons\Calendar\Repositories\PreferenceRepository;

/**
 * @property ChannelEntry $ChannelEntry
 * @property int          $id
 * @property int          $entry_id
 * @property int          $calendar_id
 * @property int          $field_id
 * @property string       $rrule
 * @property Carbon       $start_date
 * @property Carbon       $end_date
 * @property bool         $all_day
 * @property bool         $select_dates
 * @property string       $until_date
 */
class Event extends Model
{
    protected static $_primary_key = 'id';
    protected static $_table_name  = 'calendar_events';

    protected $id;
    protected $entry_id;
    protected $calendar_id;
    protected $field_id;
    protected $rrule;
    protected $start_date;
    protected $end_date;
    protected $all_day;
    protected $select_dates;
    protected $until_date;

    /** @var Carbon */
    private $startDateCarbon;

    /** @var Carbon */
    private $endDateCarbon;

    /** @var Carbon[] [occurrenceDate => supposedStartDate, ..] */
    private $occurrenceDatePool = array();

    /** @var Frequency */
    private $rruleData;

    protected static $_relationships = array(
        'ChannelEntry' => array(
            'type'     => 'BelongsTo',
            'model'    => 'ee:ChannelEntry',
            'from_key' => 'entry_id',
            'to_key'   => 'entry_id',
            'weak'     => true,
            'inverse'  => array(
                'name' => 'Event',
                'type' => 'belongsTo',
            ),
        ),
    );

    /**
     * @return Event
     */
    public static function create()
    {
        $preference = PreferenceRepository::getInstance()->getOrCreate();

        /** @var Event $event */
        $event = ee('Model')->make(
            'calendar:Event',
            array(
                'all_day' => $preference->all_day,
            )
        );

        return $event;
    }

    /**
     * If a start date is set returns it
     *
     * @return Carbon
     */
    public function getStartDate()
    {
        if (is_null($this->startDateCarbon)) {
            $startDateCarbon       = new Carbon($this->start_date, DateTimeHelper::TIMEZONE_UTC);
            $this->startDateCarbon = $startDateCarbon;
        }

        return $this->startDateCarbon;
    }

    /**
     * @param Carbon $offsetDate
     *
     * @return string
     */
    public function getFormattedStartDate(Carbon $offsetDate = null)
    {
        $targetDate = $this->getOccurrenceStartDate($offsetDate);

        return $this->getVerboseDateFormatString($targetDate);
    }

    /**
     * If an end date is set returns it as a Carbon value
     *
     * @return Carbon|null
     */
    public function getEndDate()
    {
        if (is_null($this->endDateCarbon)) {
            $endDateCarbon       = new Carbon($this->end_date, DateTimeHelper::TIMEZONE_UTC);
            $this->endDateCarbon = $endDateCarbon;
        }

        return $this->endDateCarbon;
    }

    /**
     * @param Carbon $offsetDate
     *
     * @return string
     */
    public function getFormattedEndDate(Carbon $offsetDate = null)
    {
        $targetDate = $this->getOccurrenceEndDate($offsetDate);

        return $this->getVerboseDateFormatString($targetDate);
    }

    /**
     * @param Carbon $occurrenceDate
     * @param Carbon $supposedStartDate
     */
    public function addOccurrenceDate(Carbon $occurrenceDate, Carbon $supposedStartDate)
    {
        $this->occurrenceDatePool[$occurrenceDate->toDateString()] = $supposedStartDate;
    }

    /**
     * Checks to see if the event occurs over many days (not recursion)
     *
     * @return boolean
     */
    public function isMultiDay()
    {
        return $this->getStartDate()->day !== $this->getEndDate()->day;
    }

    /**
     * Syntactical sugar for checking if an event repeats
     *
     * @return boolean
     */
    public function isRecurring()
    {
        if (!is_null($this->rrule) || $this->select_dates) {
            return true;
        }

        return false;
    }

    public function isSelectDates()
    {
        if ($this->select_dates) {
            return true;
        }

        return false;
    }

    public function getSelectedDates()
    {
        $selectDates = ee('Model')
            ->get('calendar:SelectDate')
            ->filter('event_id', $this->id)
            ->all();

        if ($selectDates) {
            return $selectDates;
        }

        return false;
    }


    /**
     * How many days does the event occur on?
     *
     * @return int days
     */
    public function getNumberOfDays()
    {
        return $this
            ->getEndDate()
            ->copy()
            ->setTime(0, 0, 0)
            ->diffInDays(
                $this->getStartDate()->copy()->setTime(0, 0, 0)
            );
    }

    /**
     * How many hours does the event occur on?
     *
     * @return int hours
     */
    public function getNumberOfHours()
    {
        return $this->getEndDate()->diffInHours($this->getStartDate());
    }

    /**
     * How many minutes does the event occur on?
     *
     * @return int minutes
     */
    public function getNumberOfMinutes()
    {
        return $this->getEndDate()->diffInMinutes($this->getStartDate());
    }

    /**
     * Checks whether the $hour falls
     * between the current events $start_date and $end_date
     *
     * @param int $hour
     *
     * @return bool
     */
    public function inHour($hour)
    {
        return $this->getStartDate()->hour === (int)$hour;
    }

    /**
     * Check if the date should be shown as an "all-day" event.
     * It has to if one of these conditions are met:
     *  - the event is set to "all-day"
     *  - the event start diff from $onDate in days is not zero (meaning that the start date is on another day)
     *
     * @param Carbon $onDate
     *
     * @return bool
     */
    public function showAsAllDay(Carbon $onDate)
    {
        if ($this->all_day) {
            return true;
        }

        $isSingleDayEvent = !$this->isMultiDay();
        if ($isSingleDayEvent) {
            return false;
        }

        if ($this->isRecurring()) {
            return !$this->occursOn($onDate);
        }

        $startDate = $this->getStartDate();

        $year  = $onDate->year;
        $month = $onDate->month;
        $day   = $onDate->day;

        $isCurrentYear  = $year == $startDate->year;
        $isCurrentMonth = $month == $startDate->month;
        $isCurrentDay   = $day == $startDate->day;

        return (!$isCurrentYear || !$isCurrentMonth || !$isCurrentDay);
    }

    /**
     * Gets the RRule
     *
     * @param string $submittedRRule The stored RRule
     *
     * @return string - The modified RRule
     */
    public function getRRuleAttribute($submittedRRule)
    {
        $rrule = RRule::createFromRRule($submittedRRule);
        $until = new Carbon($rrule['until'], DateTimeHelper::TIMEZONE_UTC);
        // Does timestamp conversion
        $rrule['until'] = $until->toISO8601String();

        return $rrule->getRRule();
    }

    /**
     * Sets up the RRule data for the recurrence if none exists
     *
     * @return Frequency The RRule data parsed to an array
     */
    public function getRRuleData()
    {
        if (!$this->rrule) {
            return null;
        }

        if (is_null($this->rruleData)) {
            $this->rruleData = RRule::createFromRRule($this->rrule);
        }

        return $this->rruleData;
    }

    /**
     * Returns a value for frequency ("daily", "weekly" ...)
     *
     * @return string|null
     */
    public function getFreq()
    {
        if ($this->rrule) {
            $rrule = $this->getRRuleData();

            if (!is_null($rrule)) {
                return $rrule['freq'];
            }
        }

        if ($this->select_dates) {
            return 'dates';
        }

        return null;
    }

    /**
     * @return bool
     */
    public function isDaily()
    {
        return $this->getFreq() === "daily";
    }

    /**
     * @return bool
     */
    public function isWeekly()
    {
        return $this->getFreq() === "weekly";
    }

    /**
     * @return bool
     */
    public function isMonthly()
    {
        return $this->getFreq() === "monthly";
    }

    /**
     * @return bool
     */
    public function isYearly()
    {
        return $this->getFreq() === "yearly";
    }

    /**
     * Returns an RRule in array format under the guise of a 'daily' key
     *
     * @return array The daily recursion array
     */
    public function getDaily()
    {
        return $this->handleData('daily');
    }

    /**
     * Returns an RRule in array format under the guise of a 'weekly' key
     *
     * @return array The weekly recursion array
     */
    public function getWeekly()
    {
        return $this->handleData('weekly');
    }

    /**
     * Returns an RRule in array format under the guise of a 'monthly' key
     *
     * @return array The monthly recursion array
     */
    public function getMonthly()
    {
        return $this->handleData('monthly');
    }

    /**
     * Returns an RRule in array format under the guise of a 'yearly' key
     *
     * @return array The yearly recursion array
     */
    public function getYearly()
    {
        return $this->handleData('yearly');
    }

    /**
     * Gets the Until value Carbon or null
     *
     * @return null|Carbon
     */
    public function getUntil()
    {
        if (!is_null($this->until_date)) {
            return new Carbon($this->until_date, DateTimeHelper::TIMEZONE_UTC);
        }

        return null;
    }

    /**
     * @return int
     */
    public function getInterval()
    {
        $rrule = $this->getRRuleData();
        if (isset($rrule['interval'])) {
            return $rrule['interval'];
        }

        return null;
    }

    /**
     * Takes a Carbon object and returns the recurrences in an array of Carbon
     * objects for the recursion
     *
     * @param Carbon $startDate The start date
     * @param Carbon $endDate
     * @param int    $limit
     *
     * @return array An array of Carbon objects for the recurrence
     */
    public function getOccurrences(Carbon $startDate, Carbon $endDate = null, $limit = 200)
    {
        $occurrences = array();
        if ($this->rrule) {
            $r = new When();
            try {
                $r->startDate($startDate)
                  ->rrule($this->rrule);

                if ($limit) {
                    $r->count($limit);
                }

                if ($endDate) {
                    $r->until($endDate);
                }

                $r->generateOccurrences();

                $occurrences = $r->occurrences;
            } catch (InvalidStartDate $e) {
            }
        }

        return $occurrences;
    }

    /**
     * Find out if an event occurs on a specific date
     *
     * @param Carbon $date
     *
     * @return bool
     */
    public function occursOn(Carbon $date)
    {
        if ($this->rrule) {
            $r = new When();
            $r->rrule($this->rrule)
              ->startDate($this->getStartDate())
              ->until($this->getUntil());

            return $r->occursOn($date);
        }

        return false;
    }

    /**
     * Helps the frequency attribute accessors get their data from an RRUle
     *
     * @param  string $freq daily, weekly, monthly or yearly
     *
     * @return array An array of data or an empty string
     */
    private function handleData($freq)
    {
        if (!$this->rrule) {
            return null;
        }

        $rrule = $this->getRRuleData();
        $data  = array();
        if ($rrule['freq'] === $freq) {
            $data = $rrule->getData();
        }

        if (empty($data)) {
            return null;
        }

        return (object)$data;
    }

    /**
     * Makes a "Monday, January 11, 2016 at 8:00am" from a Carbon date object
     * Does not append hours if the current event is set to be "All day" long
     *
     * @param Carbon $date
     *
     * @return string
     */
    private function getVerboseDateFormatString(Carbon $date)
    {
        $dateString = $date->format('l, F j, Y');

        if (!$this->all_day) {
            $dateString .= lang('calendar_time_from_date_separator') . $date->format('g:ia');
        }

        return $dateString;
    }

    /**
     * @param Carbon $offsetDate
     *
     * @return Carbon
     */
    public function getOccurrenceStartDate(Carbon $offsetDate)
    {
        $targetDate = $this->getStartDate();

        if (isset($this->occurrenceDatePool[$offsetDate->toDateString()])) {
            $startDate = $this->getStartDate();

            $targetDate = clone $this->occurrenceDatePool[$offsetDate->toDateString()];
            $targetDate->setTime($startDate->hour, $startDate->minute, $startDate->second);

            return $targetDate;
        }

        return $targetDate;
    }

    /**
     * @param Carbon $offsetDate
     *
     * @return Carbon
     */
    public function getOccurrenceEndDate(Carbon $offsetDate)
    {
        $targetDate = $this->getEndDate();

        if (isset($this->occurrenceDatePool[$offsetDate->toDateString()])) {
            $endDate = $this->getEndDate();

            $targetDate = clone $this->occurrenceDatePool[$offsetDate->toDateString()];
            $targetDate->day += $this->getNumberOfDays();
            $targetDate->setTime($endDate->hour, $endDate->minute, $endDate->second);

            return $targetDate;
        }

        return $targetDate;
    }
}

