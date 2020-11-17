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

namespace Solspace\Addons\Calendar\Repositories;

use EllisLab\ExpressionEngine\Model\Channel\Channel;
use EllisLab\ExpressionEngine\Model\Channel\ChannelEntry;
use EllisLab\ExpressionEngine\Service\Model\Collection;
use Solspace\Addons\Calendar\Exceptions\CalendarException;
use Solspace\Addons\Calendar\Helpers\DateTimeHelper;
use Solspace\Addons\Calendar\Library\Carbon\Carbon;
use Solspace\Addons\Calendar\Library\Helpers;
use Solspace\Addons\Calendar\Library\RRule\RRule;
use Solspace\Addons\Calendar\Library\When\When;
use Solspace\Addons\Calendar\Model\CalendarModel;
use Solspace\Addons\Calendar\Model\Event;
use Solspace\Addons\Calendar\Model\Exclusion;

class MigrationRepository extends AbstractRepository
{
    const MAX_RECURRING_EXCEPTIONS = 20;

    private $legacyFieldId;

    /**
     * @return MigrationRepository
     */
    public static function getInstance()
    {
        return parent::getInstance();
    }

    /**
     * Gets a collection of all legacy calendars as ChannelEntry instances
     *
     * @return Collection|ChannelEntry[]
     */
    public function getLegacyCalendars()
    {
        $preferences = $this->getLegacyPreferences();

        if (!isset($preferences['calendar_weblog'])) {
            return new Collection();
        }

        $legacyCalendars = ee('Model')
            ->get('ChannelEntry')
            ->filter('channel_id', $preferences['calendar_weblog'])
            ->all();

        return $legacyCalendars;
    }

    /**
     * @return Channel
     */
    public function getLegacyCalendarChannel()
    {
        $preferences = $this->getLegacyPreferences();

        if (!isset($preferences['calendar_weblog'])) {
            return null;
        }

        /** @var Channel $legacyCalendarChannel */
        $legacyCalendarChannel = ee('Model')
            ->get('Channel')
            ->filter('channel_id', $preferences['calendar_weblog'])
            ->first();

        return $legacyCalendarChannel;
    }

    /**
     * Gets a collection of all legacy events as ChannelEntry instances
     *
     * @return Collection|ChannelEntry[]
     */
    public function getLegacyEvents()
    {
        $preferences = $this->getLegacyPreferences();

        $eventData = $this->getLegacyEventData();

        if (!isset($preferences['event_weblog'])) {
            return new Collection();
        }

        $legacyEvents = ee('Model')
            ->get('ChannelEntry')
            ->filter('channel_id', $preferences['event_weblog'])
            ->filter('entry_id', 'IN', array_column($eventData, 'entry_id'))
            ->all();

        return $legacyEvents;
    }

    /**
     * Return all event data indexed by the entry_id
     *
     * @return array
     */
    public function getLegacyEventData()
    {
        $suffix       = \Calendar_upd::getLegacyTableSuffix();
        $legacyEvents = ee()->db->get('calendar_events' . $suffix)->result_array();

        $indexedEvents = array();
        foreach ($legacyEvents as $event) {
            $indexedEvents[$event['entry_id']] = $event;
        }

        return $indexedEvents;
    }

    /**
     * @param int $limit
     * @param int $offset
     *
     * @return array
     */
    public function getLegacyEventBatch($limit = 10, $offset = 0)
    {
        $suffix      = \Calendar_upd::getLegacyTableSuffix();
        $preferences = $this->getLegacyPreferences();

        /** @var array $legacyEventsData */
        $legacyEventsData = ee()->db
            ->get('calendar_events' . $suffix, $limit, $offset)
            ->result_array();

        $indexedEvents = array();
        foreach ($legacyEventsData as $eventData) {
            $indexedEvents[$eventData['entry_id']] = $eventData;
        }

        /** @var ChannelEntry[] $legacyEvents */
        $legacyEvents = ee('Model')
            ->get('ChannelEntry')
            ->filter('channel_id', $preferences['event_weblog'])
            ->filter('entry_id', 'IN', array_column($indexedEvents, 'entry_id'))
            ->all();

        $returnData = array();
        foreach ($legacyEvents as $event) {
            $returnData[$event->entry_id] = array(
                'data' => $indexedEvents[$event->entry_id],
                'model' => $event,
            );
        }

        return $returnData;
    }

    /**
     * @return int
     */
    public function getLegacyEventCount()
    {
        $suffix = \Calendar_upd::getLegacyTableSuffix();

        $legacyEventCount = ee()->db
            ->select('COUNT(event_id) AS count')
            ->from('calendar_events' . $suffix)
            ->get()
            ->result_array();

        if (count($legacyEventCount)) {
            return (int) $legacyEventCount[0]['count'];
        }

        return 0;
    }

    /**
     * Returns the ID of the calendar fieldtype for Calendar: Events channel
     *
     * @return int
     * @throws CalendarException
     */
    public function getLegacyEventFieldId()
    {
        if (is_null($this->legacyFieldId)) {
            $fieldModel = ee('Model')
                ->get('ChannelField')
                ->filter('field_name', 'event_dates_and_options')
                ->filter('field_type', 'calendar')
                ->first();

            if (!$fieldModel) {
                throw new CalendarException("Could not find the calendar field for 'Calendar: Events' channel");
            }

            $this->legacyFieldId = $fieldModel->field_id;
        }

        return $this->legacyFieldId;
    }

    /**
     * @param ChannelEntry $calendar
     * @param int          $siteId
     *
     * @return CalendarModel
     */
    public function getCalendarFromLegacy(ChannelEntry $calendar, $siteId)
    {
        $calendarModel = ee('Model')
            ->get('calendar:Calendar')
            ->filter('url_title', $calendar->url_title)
            ->filter('site_id', $siteId)
            ->filter('description', $calendar->description)
            ->first();

        if (!$calendarModel) {
            $calendarModel = ee('Model')
                ->make(
                    'calendar:Calendar',
                    array(
                        'name'        => $calendar->title,
                        'url_title'   => $calendar->url_title,
                        'color'       => Helpers::randomColor(),
                        'description' => $calendar->summary,
                        'default'     => false,
                        'site_id'     => $siteId,
                    )
                );
        }

        return $calendarModel;
    }

    /**
     * @param ChannelEntry $event
     * @param int          $calendarId
     * @param array        $eventData
     *
     * @return Event
     * @throws CalendarException
     */
    public function getEventFromLegacy(ChannelEntry $event, $calendarId, array $eventData)
    {
        $entryId = $event->entry_id;
        $fieldId = $this->getLegacyEventFieldId();
        $allDay  = $eventData['all_day'] == 'y';
        $recurs  = $eventData['recurs'] == 'y';

        $startDate = Carbon::createFromDate(
            $eventData['start_year'],
            $eventData['start_month'],
            $eventData['start_day'],
            DateTimeHelper::TIMEZONE_UTC
        );

        $endDate = Carbon::createFromDate(
            $eventData['end_year'],
            $eventData['end_month'],
            $eventData['end_day'],
            DateTimeHelper::TIMEZONE_UTC
        );

        // If it's an all-day event - disregard start and end *times*
        if ($allDay) {
            $startDate->setTime(0, 0, 0);
            $endDate->setTime(23, 59, 59);
        } else {
            $hour   = $eventData['start_time'] / 100;
            $minute = substr($eventData['start_time'], -2);
            $startDate->setTime($hour, $minute);
            $hour   = $eventData['end_time'] / 100;
            $minute = substr($eventData['end_time'], -2);
            $endDate->setTime($hour, $minute);
        }

        $eventModel = ee('Model')
            ->get('calendar:Event')
            ->filter('entry_id', $entryId)
            ->filter('calendar_id', $calendarId)
            ->filter('field_id', $fieldId)
            ->first();

        if (!$eventModel) {
            /** @var Event $eventModel */
            $eventModel = ee('Model')
                ->make(
                    'calendar:Event',
                    array(
                        'entry_id'    => $entryId,
                        'calendar_id' => $calendarId,
                        'field_id'    => $fieldId,
                    )
                );
        }

        $eventModel->start_date = $startDate->toDateTimeString();
        $eventModel->end_date   = $endDate->toDateTimeString();
        $eventModel->all_day    = $allDay;

        // Some fields get populated only for recurring events
        if ($recurs) {
            // Fetch all rrules for this event
            $suffix            = \Calendar_upd::getLegacyTableSuffix();
            $legacyRecurrences = ee()->db
                ->where('entry_id', $entryId)
                ->where('rule_type', '+')
                ->limit(1)
                ->get('calendar_events_rules' . $suffix)
                ->result_array();

            $rrule      = $until = null;
            $recurrency = array_pop($legacyRecurrences);
            if ($recurrency) {
                list($until, $rrule) = $this->parseLegacyRRule($recurrency);
            }

            if ($rrule && preg_match("/FREQ=(DAILY|WEEKLY|MONTHLY|YEARLY)/i", $rrule)) {
                $eventModel->until_date = $until->toDateTimeString();
                $eventModel->rrule      = $rrule;
            }
        }

        return $eventModel;
    }

    /**
     * Finds all exceptions and their rules for a given event
     * And converts them to Cal2 exclusions
     *
     * @param Event $event
     *
     * @throws \Exception
     */
    public function handleEventExceptions(Event $event)
    {
        $suffix = \Calendar_upd::getLegacyTableSuffix();

        $existingExclusions = ee()->db
            ->where('event_id', $event->id)
            ->get('calendar_exclusions')
            ->result_array();

        $existingExclusions = array_column($existingExclusions, 'date');

        $legacyExclusions = ee()->db
            ->where('entry_id', $event->entry_id)
            ->get('calendar_events_exceptions' . $suffix)
            ->result_array();

        foreach ($legacyExclusions as $exclusion) {
            $dateCarbon = Carbon::createFromFormat('Ymd', $exclusion['start_date'], DateTimeHelper::TIMEZONE_UTC);
            $dateString = $dateCarbon->toDateString();

            if (in_array($dateString, $existingExclusions)) {
                continue;
            }

            /** @var Exclusion $exclusionModel */
            $exclusionModel = ee('Model')
                ->make('calendar:Exclusion', array('event_id' => $event->id, 'date' => $dateString));

            $exclusionModel->save();
        }

        $legacyExclusionRules = ee()->db
            ->where('entry_id', $event->entry_id)
            ->where('rule_type', '-')
            ->get('calendar_events_rules' . $suffix)
            ->result_array();

        foreach ($legacyExclusionRules as $rule) {
            list($until, $rruleAsString) = $this->parseLegacyRRule($rule);

            $ruleStartDate = Carbon::createFromFormat('Ymd', $rule['start_date'], DateTimeHelper::TIMEZONE_UTC);

            $rrule     = RRule::createFromRRule($rruleAsString);
            $rruleData = $rrule->getData();

            $when = new When();
            $when->freq($rruleData['freq']);
            $when->rrule($rruleAsString);
            $when->startDate($ruleStartDate);
            $when->until($until);
            $when->generateOccurrences();

            $occurrences = $when->occurrences;

            /** @var \DateTime $occurrence */
            foreach ($occurrences as $occurrence) {
                $dateString = $occurrence->format('Y-m-d');

                if (in_array($dateString, $existingExclusions)) {
                    continue;
                }

                /** @var Exclusion $exclusionModel */
                $exclusionModel = ee('Model')
                    ->make('calendar:Exclusion', array('event_id' => $event->id, 'date' => $dateString));

                try {
                    $exclusionModel->save();
                } catch (\Exception $e) {
                    if (strpos($e->getMessage(), 'SQLSTATE[23000]') === false) {
                        throw $e;
                    }
                }
            }
        }
    }

    /**
     * Builds an RRULE string and finds UNTIL date
     *
     * @param array $rule
     *
     * @return array [untilCarbon, rruleString]
     */
    public function parseLegacyRRule(array $rule)
    {
        $parsedRule = array();

        if ($rule['repeat_years'] > 0) {
            $parsedRule['FREQ']     = 'YEARLY';
            $parsedRule['INTERVAL'] = $rule['repeat_years'];
        } elseif ($rule['repeat_months'] > 0) {
            $parsedRule['FREQ']     = 'MONTHLY';
            $parsedRule['INTERVAL'] = $rule['repeat_months'];
        } elseif ($rule['repeat_weeks'] > 0) {
            $parsedRule['FREQ']     = 'WEEKLY';
            $parsedRule['INTERVAL'] = $rule['repeat_weeks'];
        } elseif ($rule['repeat_days'] > 0) {
            $parsedRule['FREQ']     = 'DAILY';
            $parsedRule['INTERVAL'] = $rule['repeat_days'];
        }

        if ($rule['months_of_year'] > 0) {
            //this flips keys to make 'c' => 12, etc
            $monthsOfYear = array_flip(array(1, 2, 3, 4, 5, 6, 7, 8, 9, 'A', 'B', 'C'));

            if (strlen($rule['months_of_year'] > 1)) {
                $months = str_split($rule['months_of_year']);
                foreach ($months as $month) {
                    $parsedRule['BYMONTH'][] = $monthsOfYear[$month] + 1;
                }
            } else {
                $month                 = (int) $rule['months_of_year'];
                $parsedRule['BYMONTH'] = $monthsOfYear[$month] + 1;
            }
        }

        if ($rule['days_of_month'] > '') {
            //this flips keys to make 'v' => 30, etc
            $daysOfMonth = array_flip(array_merge(range(1, 9), range('A', 'V')));

            $days = str_split($rule['days_of_month']);
            if ($days > 1) {
                foreach ($days as $day) {
                    $parsedRule['BYMONTHDAY'][] = $daysOfMonth[$day] + 1;
                }
            } else {
                $parsedRule['BYMONTHDAY'] = $daysOfMonth[$rule['days_of_month']] + 1;
            }
        }

        // Parse days of week only if BYMONTHDAY is not present
        $hasDaysOfWeek = $rule['days_of_week'] != '' || $rule['days_of_week'] > 0;
        if ($hasDaysOfWeek && !isset($parsedRule['BYMONTHDAY'])) {
            $daysOfMonth = array('SU', 'MO', 'TU', 'WE', 'TH', 'FR', 'SA');
            $dayLetter   = array('U', 'M', 'T', 'W', 'R', 'F', 'S');

            $daysOfWeek = str_split($rule['days_of_week']);

            if ($rule['relative_dow'] > 0) {
                $relativeDaysOfWeek = str_split($rule['relative_dow']);
                foreach ($daysOfWeek as $day) {
                    foreach ($relativeDaysOfWeek as $relativeDay) {
                        if ($relativeDay >= 5) {
                            $relativeDay = -1;
                        }
                        $parsedRule['BYDAY'][] = $relativeDay . $daysOfMonth[array_search($day, $dayLetter)];
                    }
                }
            } else {
                foreach ($daysOfWeek as $day) {
                    $parsedRule['BYDAY'][] = $daysOfMonth[array_search($day, $dayLetter)];
                }
            }
        }

        if ($parsedRule['FREQ'] === 'YEARLY' && !isset($parsedRule['BYMONTH'])) {
            $month                 = Carbon::createFromFormat('Ymd', $rule['start_date'], DateTimeHelper::TIMEZONE_UTC);
            $parsedRule['BYMONTH'] = $month->format('n');
        }

        if ($rule['stop_after'] > 0) {
            $parsedRule['COUNT'] = $rule['stop_after'];
            $tempRRule           = $this->rruleToString($parsedRule);
            $startDate           = Carbon::createFromFormat('Ymd', $rule['start_date'], DateTimeHelper::TIMEZONE_UTC);

            $when = new When();
            $when->freq($parsedRule['FREQ']);
            $when->rrule($tempRRule);
            $when->startDate($startDate);
            $when->count($rule['stop_after']);
            $when->generateOccurrences();
            $occurrences = $when->occurrences;

            $until               = array_pop($occurrences);
            $until               = new Carbon($until->format('Y-m-d H:i:s'), DateTimeHelper::TIMEZONE_UTC);
            $parsedRule['UNTIL'] = $until->toISO8601String();
            unset($parsedRule['COUNT']);
            $until->setTime(23, 59, 59);
        } elseif ($rule['stop_by'] > 0) {
            $until = Carbon::createFromFormat('Ymd', $rule['stop_by'], DateTimeHelper::TIMEZONE_UTC);
            $until->setTime(23, 59, 59);
            $parsedRule['UNTIL'] = $until->toISO8601String();
        } else {
            $until = new Carbon("+30 years", DateTimeHelper::TIMEZONE_UTC);
            $until->setTime(23, 59, 59);
            $parsedRule['UNTIL'] = $until->toISO8601String();
        }

        return array($until, $this->rruleToString($parsedRule));
    }

    /**
     * Gets legacy preferences for the current site_id
     *
     * @return array
     */
    private function getLegacyPreferences()
    {
        $suffix = \Calendar_upd::getLegacyTableSuffix();
        $siteId = ee()->config->item('site_id');

        $result = ee()->db
            ->where('site_id', $siteId)
            ->get("calendar_preferences$suffix")->result_array();

        $unserialized = array();
        foreach ($result as $preference) {
            $unserialized[$preference['site_id']] = unserialize($preference['preferences']);
        }

        return isset($unserialized[$siteId]) ? $unserialized[$siteId] : array();
    }

    /**
     * Converts the RRule array to RRULE string
     *
     * @param array $rrule
     *
     * @return string
     */
    private function rruleToString(array $rrule)
    {
        $ruleToString = array();
        foreach ($rrule as $key => $value) {
            if (is_array($value)) {
                $value = implode(',', $value);
            }
            $ruleToString[] = "$key=$value";
        }

        return implode(';', $ruleToString);
    }
}
