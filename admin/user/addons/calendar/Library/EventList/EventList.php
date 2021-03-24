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

namespace Solspace\Addons\Calendar\Library\EventList;

use EllisLab\ExpressionEngine\Service\Model\Collection;
use Solspace\Addons\Calendar\Exceptions\CacheException;
use Solspace\Addons\Calendar\Helpers\DateTimeHelper;
use Solspace\Addons\Calendar\Library\Carbon\Carbon as Carbon;
use Solspace\Addons\Calendar\Model\Event;
use Solspace\Addons\Calendar\Model\Preference;
use Solspace\Addons\Calendar\Repositories\EventRepository;
use Solspace\Addons\Calendar\Repositories\PreferenceRepository;

/**
 * The EventList does the heavy listing of creating a giant array of IDs and
 * a secondary list of events. It will give you events for a given date, but
 * you must pass it an Event Collection. getEvents()
 */
class EventList
{
    const OCCURRENCE_LIMIT = 43; // 31 for month days + 6 both ways into prev and next months

    /** @var array of Carbon dates indexed by event */
    private $exclusionCache;

    /** @var Collection|Event[] */
    private $events;

    /** @var Preference */
    private $preference;

    /** @var Carbon */
    private $rangeStartDate;

    /** @var Carbon */
    private $rangeEndDate;

    /**
     * Event ID's grouped and indexed by their Y-m-d value as key
     * [start-date => [id, id, ..], ..]
     *
     * @var array $eventList
     */
    private $eventList = array();

    /**
     * EventLIst is composed of a Laravel Collection. It MUST have all its
     * exclusions, calendars and recursions pre-loaded
     *
     * @param Collection|Event[] $events
     * @param Carbon             $rangeStartDate
     * @param Carbon             $rangeEndDate
     */
    public function __construct(Collection $events, Carbon $rangeStartDate, Carbon $rangeEndDate)
    {
        if ($events->count()) {
            $this->events = $events->indexByIds();
        } else {
            $this->events = array();
        }
        $this->rangeStartDate = $rangeStartDate;
        $this->rangeEndDate   = $rangeEndDate;
        $this->preference     = PreferenceRepository::getInstance()->getOrCreate();
        $this->createEventList();
    }

    /**
     * When given a date string in ISO8601 format (YYYY-MM-DD) will give you the
     * dates for that day
     *
     * @param string $dateString A date in ISO8601 format
     *
     * @return Event[]|Collection An Array of Event Objects
     */
    public function getEvents($dateString)
    {
        $eventIds = array();
        if (isset($this->eventList[$dateString])) {
            $eventIds = $this->eventList[$dateString];
        }

        $eventList = array();
        foreach ($eventIds as $eventId) {
            if (isset($this->events[$eventId])) {
                $eventList[] = $this->events[$eventId];
            }
        }

        return $eventList;
    }

    /**
     * Takes an event collection, parses it and sorts it
     */
    private function createEventList()
    {
        if (empty($this->events)) {
            return;
        }

        $this->cacheEventExclusions($this->events);

        foreach ($this->events as $event) {
            $this->handleEvent($event);
        }
        $this->sort();
    }

    /**
     * Actually handles all the parsing of event data to create the giant array
     *
     * @param Event $event
     */
    private function handleEvent($event)
    {
        $startDate = $event->getStartDate();

        $notRecurring = !$event->isRecurring();
        $notExcluded  = !$this->isDateExcluded($startDate, $event->id);

        $dateSelect = false;

        if ($event->select_dates) {
            $dateSelect = $this->isDateSelect($event->id);
        }

        if ($dateSelect) {
            $this->handleDateSelect($event);
        } elseif ($notRecurring && $notExcluded) {
            $this->eventList[$startDate->toDateString()][] = $event->id;

            if ($event->isMultiDay()) {
                $this->setMultiDayEvent($startDate, $event);
            }
        } else {
            $this->handleRecurrences($event);
        }
    }

    /**
     * Handles all the recurrence information needed in the event list
     *
     * @param Event $event
     */
    private function handleRecurrences(Event $event)
    {
        if ($event->isRecurring()) {
            $startDate      = $event->getStartDate();

            $rangeStartDate = clone $this->rangeStartDate;
            if ($rangeStartDate->lt($startDate)) {
                $rangeStartDate = clone $startDate;
            } else {
                $rangeStartDate->day = $startDate->day;
            }

            $rangeEndDate = $this->rangeEndDate;
            if ($rangeEndDate->gt($event->getUntil())) {
                $rangeEndDate = $event->getUntil();
            }

            $occurrences = $event->getOccurrences($startDate, $rangeEndDate, null);

            /** @var Carbon $day */
            foreach ($occurrences as $day) {
                if (!$day->between($rangeStartDate, $rangeEndDate)) {
                    continue;
                }

                if ($this->isDateExcluded($day, $event->id)) {
                    continue;
                }

                $event->addOccurrenceDate($day, $day);

                $this->addEventToEventList($day, $event->id);
                if ($event->isMultiDay()) {
                    $this->setMultiDayEvent($day, $event);
                }
            }
        }
    }

    private function handleDateSelect(Event $event)
    {
        $selectDates = ee('Model')
            ->get('calendar:SelectDate')
            ->filter('event_id', $event->id)
            ->all();

        foreach ($selectDates as $selectDate) {
            $occurrences[] = new Carbon($selectDate->date, DateTimeHelper::TIMEZONE_UTC);
        }

        $occurrences[] = new Carbon($event->start_date, DateTimeHelper::TIMEZONE_UTC);

        /** @var Carbon $day */
        foreach ($occurrences as $day) {

            $event->addOccurrenceDate($day, $day);

            $this->addEventToEventList($day, $event->id);
            if ($event->isMultiDay()) {
                $this->setMultiDayEvent($day, $event);
            }
        }
    }

    /**
     * Adds an event for a given date
     *
     * @param Carbon $day The date to add the event
     * @param string $id  The event id
     */
    private function addEventToEventList(Carbon $day, $id)
    {
        $dateString = $day->toDateString();

        $dateNotSet = !isset($this->eventList[$dateString]);

        if ($dateNotSet) {
            $this->eventList[$dateString] = array();
        }

        $idNotPresent = !in_array($id, $this->eventList[$dateString]);
        if ($idNotPresent) {
            $this->eventList[$dateString][] = $id;
        }
    }

    /**
     * @param Carbon $date
     * @param int    $eventId
     *
     * @return bool
     * @throws CacheException
     */
    private function isDateExcluded(Carbon $date, $eventId)
    {
        $dateString = $date->toDateString();
        $exclusions = $this->getExclusionsForEvent($eventId);

        return in_array($dateString, $exclusions);
    }

    /**
     * @param int    $eventId
     *
     * @return bool
     * @throws CacheException
     */
    private function isDateSelect($eventId)
    {
        $selectDates = ee('Model')
            ->get('calendar:SelectDate')
            ->filter('event_id', $eventId)
            ->all();

        if ($selectDates) {
            return true;
        }

        return false;
    }

    /**
     * Makes sure that all events that are multi-day, including recurrences
     * have their IDs set
     *
     * @param Carbon $startDate
     * @param Event  $event
     */
    private function setMultiDayEvent(Carbon $startDate, $event)
    {
        $supposedStartDate = clone $startDate;

        $difference    = $event->getNumberOfDays();
        $recurrenceDay = $startDate->copy();
        while ($difference > 0) {
            $recurrenceDay->addDay();

            if (!$event->all_day && $difference === 1) {
                $overlapThreshold = $this->preference->overlap_threshold;
                $endDate          = $event->getEndDate();

                $hour   = $endDate->hour;
                $minute = $endDate->minute;
                $second = $endDate->second;

                if ($hour < $overlapThreshold || ($hour == $overlapThreshold && $minute == 0 && $second == 0)) {
                    break;
                }
            }

            $event->addOccurrenceDate($recurrenceDay, $supposedStartDate);

            $this->addEventToEventList($recurrenceDay, $event->id);
            $difference -= 1;
        }
    }

    /**
     * @param int $eventId
     *
     * @return array|Carbon[]
     * @throws CacheException
     */
    private function getExclusionsForEvent($eventId)
    {
        $cache = $this->exclusionCache;

        if (is_null($cache)) {
            throw new CacheException('Exclusion cache not warmed up.');
        }

        if (!array_key_exists($eventId, $cache)) {
            return array();
        }

        return $cache[$eventId];
    }

    /**
     * Caches exclusions for the given $events list
     *
     * @param Event[] $events
     */
    private function cacheEventExclusions(array $events)
    {
        if (!is_null($this->exclusionCache)) {
            return;
        }

        $eventRepository = EventRepository::getInstance();

        $eventIds = array();
        foreach ($events as $event) {
            $eventIds[] = $event->id;
        }

        $this->exclusionCache = $eventRepository->getExclusionDatesForEvents($eventIds);
    }

    /**
     * Sort events
     * 1) All-day events
     * 2) Events spanning multiple days
     * 3) Other events sorted by start date
     *
     * @return $this
     */
    private function sort()
    {
        $events = $this->events;
        foreach ($this->eventList as $dateString => &$eventIds) {
            usort(
                $eventIds,
                function ($a, $b) use ($dateString, $events) {
                    /** @var Event $eventA */
                    $eventA = $events[$a];
                    /** @var Event $eventB */
                    $eventB = $events[$b];

                    $occurrenceDate = new Carbon($dateString, DateTimeHelper::TIMEZONE_UTC);

                    if ($eventA->all_day && !$eventB->all_day) {
                        return -1;
                    } else if (!$eventA->all_day && $eventB->all_day) {
                        return 1;
                    }

                    if ($eventA->isMultiDay() && !$eventB->isMultiDay()) {
                        return -1;
                    } else if (!$eventA->isMultiDay() && $eventB->isMultiDay()) {
                        return 1;
                    }

                    $startDateA = $eventA->getOccurrenceStartDate($occurrenceDate);
                    $startDateB = $eventB->getOccurrenceStartDate($occurrenceDate);

                    if ($startDateA->gt($startDateB)) {
                        return 1;
                    } else if ($startDateA->lt($startDateB)) {
                        return -1;
                    }

                    return 0;
                }
            );
        }

        return $this;
    }
}
