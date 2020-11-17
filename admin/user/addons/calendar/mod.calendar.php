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

namespace Solspace\Addons\Calendar;

use EllisLab\ExpressionEngine\Service\Model\Collection;
use Solspace\Addons\Calendar\Helpers\DateTimeHelper;
use Solspace\Addons\Calendar\Library\AddonBuilder;
use Solspace\Addons\Calendar\Library\Carbon\Carbon;
use Solspace\Addons\Calendar\Library\EventTags\EventTags;
use Solspace\Addons\Calendar\Library\Export\ExportCalendarToIcs;
use Solspace\Addons\Calendar\Library\Schedule\Type\Day;
use Solspace\Addons\Calendar\Library\Schedule\Type\Month;
use Solspace\Addons\Calendar\Library\Schedule\Type\Week;
use Solspace\Addons\Calendar\Model\CalendarModel;
use Solspace\Addons\Calendar\Model\Event;
use Solspace\Addons\Calendar\QueryParameters\CalendarQueryParameters;
use Solspace\Addons\Calendar\QueryParameters\ChannelQueryParameters;
use Solspace\Addons\Calendar\QueryParameters\EventQueryParameters;
use Solspace\Addons\Calendar\Repositories\CalendarRepository;
use Solspace\Addons\Calendar\Repositories\EventRepository;
use Solspace\Addons\Calendar\Repositories\PreferenceRepository;

class Calendar extends AddonBuilder
{
    protected $params     = array();
    protected $dateParams = array();
    protected $dateFields = array(
        'start_date',
        'end_date',
        'previous_day',
        'next_day',
        'date',
        'month',
        'day',
        'previous_month',
        'next_month',
        'recurrence_start_date',
        'recurrence_end_date',
    );

    /**
     * Common parameters are always checked
     * and data parameters are always set
     */
    public function __construct()
    {
        parent::__construct('module');

        $this->params     = $this->getCommonParameters();
        $this->dateParams = $this->getDateParameters();
    }

    /**
     * Renders the {exp:calendar:event} tag
     *
     * @return array EE tagdata
     */
    public function events()
    {
        $rangeStart = $this->fetchDateParameter('date_range_start');
        $rangeEnd   = $this->fetchDateParameter('date_range_end');
        $limit      = $this->fetchParameter('limit', null);
        $offset     = $this->fetchParameter('offset', null);
        $paginate   = $this->fetchParameter('paginate', null);

        $queryLimit  = $limit;
        $queryOffset = $offset;
        if ($paginate) {
            $queryLimit = $queryOffset = null;
        }

        $events = $this->getEventsFromParams($rangeStart, $rangeEnd, true, $queryLimit, $queryOffset);

        if (!count($events)) {
            return $this->show_no_results();
        }

        $events = EventTags::prefixTemplateVariables($events, $this->lower_name);

        $this->paginateResults($events, $limit);

        return ee()->TMPL->tagdata;
    }

    /**
     * Renders the {exp:calendar:month} tag
     *
     * @return array EE tagdata
     */
    public function month()
    {
        $dateRangeStart = $this->fetchDateParameter('date_range_start');
        if (null === $dateRangeStart) {
            $dateRangeStart = Carbon::now(DateTimeHelper::getUserTimezone());
        }

        $dateRangeStart->day = 1;

        $dateRangeEnd      = $dateRangeStart->copy();
        $dateRangeEnd->day = $dateRangeEnd->daysInMonth;
        $dateRangeEnd->setTime(23, 59, 59);

        $events       = $this->getEventsFromParams($dateRangeStart, $dateRangeEnd);
        $preference   = PreferenceRepository::getInstance()->getOrCreate();
        $eventsByDate = self::sortEventsByDate($events, $preference->overlap_threshold);

        $monthStart      = $dateRangeStart->copy();
        $monthStart->day = 1;

        $monthEnd      = $dateRangeStart->copy();
        $monthEnd->day = $dateRangeStart->daysInMonth;

        $month = new Month($monthStart, $monthEnd);
        $month->setfirstDayOfWeek($preference->start_day);
        $currentDay = Carbon::now(DateTimeHelper::getUserTimezone());

        $weeksList   = array();
        $totalEvents = 0;

        /** @var Week[] $weeks */
        foreach ($month as $weeks) {
            foreach ($weeks as $week) {
                $daysList        = array();
                $weekTotalEvents = 0;

                /** @var Day $day */
                foreach ($week as $day) {
                    $date       = $day->getDateTime();
                    $dateString = $date->toDateString();

                    if (array_key_exists($dateString, $eventsByDate)) {
                        $dayEvents = $eventsByDate[$dateString];
                    } else {
                        $dayEvents = array();
                    }

                    $dateInGivenMonth = $date->inInGivenMonth($monthStart);
                    if ($dateInGivenMonth) {
                        $weekTotalEvents += count($dayEvents);
                    }

                    // Add event_first_day and event_last_day booleans
                    foreach ($dayEvents as &$event) {
                        $isFirstDay = $date->format('Ymd') === $event['start_date_carbon']->format('Ymd');
                        $isLastDay  = $date->format('Ymd') === $event['end_date_carbon']->format('Ymd');

                        $event['event_first_day'] = $isFirstDay;
                        $event['event_last_day']  = $isLastDay;

                        if (!($isFirstDay && $isLastDay) && !$isFirstDay && $dateInGivenMonth) {
                            $weekTotalEvents--;
                        }
                    }
                    unset($event);

                    $dayEvents = $this->sortEventsForMonthDay($dayEvents);

                    $dayData = array(
                        'day_date'             => $date->format('U:e'),
                        'day_total_events'     => count($dayEvents),
                        'day_in_current_month' => $date->format('Ym') === $dateRangeStart->format('Ym'),
                        'today'                => $date->eq($currentDay),
                        'events'               => $dayEvents,
                    );
                    $dayData += $this->getDayTagData($date);

                    $daysList[] = $dayData;
                }

                $totalEvents += $weekTotalEvents;

                $weeksList[] = array(
                    'days'              => $daysList,
                    'week_date'         => $week->getStartDateTime()->format('U:e'),
                    'week_total_events' => $weekTotalEvents,
                );
            }
        }

        $daysOfWeek = DateTimeHelper::getDayLetterTemplateTags($preference->start_day);

        $results = array(
            array(
                'previous_month'     => $monthStart->copy()->subMonth()->format('U:e'),
                'next_month'         => $monthStart->copy()->addMonth()->format('U:e'),
                'month_date'         => $monthStart->format('U:e'),
                'month'              => $monthStart->format('U:e'),
                'month_total_events' => $totalEvents,
                'weeks'              => $weeksList,
                'days_of_week'       => $daysOfWeek,
                'events'             => $events,
            ),
        );

        $results = EventTags::prefixTemplateVariables($results, $this->lower_name);

        return EventTags::parseTagdataPreservingDates($results, ee()->TMPL->tagdata);
    }

    /**
     * Renders the {exp:calendar:week} tag
     *
     * @return array EE tagdata
     */
    public function week()
    {
        $preference = PreferenceRepository::getInstance()->getOrCreate();

        $dateRangeStart = $this->fetchDateParameter('date_range_start');

        list($weekStart, $weekEnd) = Week::getSectionByTime($preference->start_day, $dateRangeStart);
        $week = new Week($weekStart, $weekEnd);

        $events       = $this->getEventsFromParams($weekStart, $weekEnd);
        $eventsByDate = self::sortEventsByDate($events, $preference->overlap_threshold);
        $daysList     = array();

        $uniqueEventIds = [];

        foreach ($week as $day) {
            $date       = $day->getDateTime();
            $dateString = $date->toDateString();

            $eventList = array();
            if (isset($eventsByDate[$dateString])) {
                $eventList = $eventsByDate[$dateString];
            }

            // Add event_first_day and event_last_day booleans
            foreach ($eventList as &$event) {
                $isFirstDay = $date->format('Ymd') == $event['start_date_carbon']->format('Ymd');
                $isLastDay  = $date->format('Ymd') == $event['end_date_carbon']->format('Ymd');

                $event['event_first_day'] = $isFirstDay;
                $event['event_last_day']  = $isLastDay;

                $uniqueEventIds[$event['event_id']] = true;
            }

            $eventList = $this->sortEventsForDay($eventList);

            $dayData = array(
                'day_date'         => $date->format('U:e'),
                'day_total_events' => count($eventList),
                'events'           => $eventList,
            );
            $dayData += $this->getDayTagData($date);

            $daysList[] = $dayData;
        }

        $results = array(
            array(
                'previous_week'     => $weekStart->copy()->subWeek()->format('U:e'),
                'next_week'         => $weekStart->copy()->addWeek()->format('U:e'),
                'week_date'         => $weekStart->format('U:e'),
                'week_total_events' => count($uniqueEventIds),
                'days_of_week'      => DateTimeHelper::getDayLetterTemplateTags($preference->start_day, $weekStart),
                'days'              => $daysList,
                'events'            => $events,
            ),
        );

        $results = EventTags::prefixTemplateVariables($results, $this->lower_name);

        return EventTags::parseTagdataPreservingDates($results);
    }

    /**
     * Renders the {exp:calendar:day} tag
     *
     * @return array EE tagdata
     */
    public function day()
    {
        $dateRangeStart = $this->fetchDateParameter('date_range_start');
        if (is_null($dateRangeStart)) {
            $dateRangeStart = Carbon::now(DateTimeHelper::TIMEZONE_UTC);
        }

        $dateRangeEnd = $dateRangeStart->copy();
        $dateRangeEnd->setTime(23, 59, 59);

        $day = $dateRangeStart->copy();

        $preference = PreferenceRepository::getInstance()->getOrCreate();

        $events       = $this->getEventsFromParams($dateRangeStart, $dateRangeEnd);
        $eventsByDate = self::sortEventsByDate($events, $preference->overlap_threshold);
        unset($events);

        $events = isset($eventsByDate[$day->toDateString()]) ? $eventsByDate[$day->toDateString()] : array();

        $hoursList = array();
        $hours     = DateTimeHelper::getHours($preference->time_format);

        $eventsByHour = $allDayEvents = array();
        $totalEvents  = 0;
        foreach ($events as $event) {

            /** @var Carbon $startDate */
            $startDate = $event['start_date_carbon'];
            /** @var Carbon $endDate */
            $endDate = $event['end_date_carbon'];
            $hour    = $startDate->hour;

            // Prevent events from other days to sneak in
            // Just a precaution
            if ($startDate->toDateString() !== $dateRangeStart->toDateString()) {
                if (!$event['event_multi_day'] || !$dateRangeStart->between($startDate, $endDate)) {
                    continue;
                } else {
                    $event['event_all_day'] = true;
                }
            }

            $totalEvents++;

            // Stack all-day events in another array
            if ($event['event_all_day']) {
                $allDayEvents[] = $event;
                continue;
            }

            if (!isset($eventsByHour[$hour])) {
                $eventsByHour[$hour] = array();
            }

            $eventsByHour[$hour][] = $event;
        }

        foreach ($eventsByHour as &$eventList) {
            $eventList = $this->sortEventsForDay($eventList);
        }

        $allDayEvents = $this->sortEventsForDay($allDayEvents);

        foreach ($hours as $hourInt => $hour) {
            $currentEvents = array();
            if (isset($eventsByHour[$hourInt])) {
                $currentEvents = $eventsByHour[$hourInt];
            }

            $hoursList[] = array(
                'hour_date'         => $day->copy()->hour($hourInt)->minute(0)->format('U:e'),
                'events'            => $currentEvents,
                'hour_total_events' => count($currentEvents),
            );
        }

        $dayData = array(
            'previous_day'     => $day->copy()->subDay()->format('U:e'),
            'next_day'         => $day->copy()->addDay()->format('U:e'),
            'day_date'         => $day->format('U:e'),
            'day_total_events' => $totalEvents,
            'hours'            => $hoursList,
            'all_day_events'   => array(
                array(
                    'all_day_total_events' => count($allDayEvents),
                    'events'               => $allDayEvents,
                ),
            ),
            'events'           => $events,
        );
        $dayData += $this->getDayTagData($day);

        $results = array($dayData);

        $results = EventTags::prefixTemplateVariables($results, $this->lower_name);

        return EventTags::parseTagdataPreservingDates($results);
    }

    /**
     * Parser for the {exp:calendar:date} tag
     *
     * @return string
     */
    public function date()
    {
        $baseDate     = $this->fetchDateParameter('base_date', null, DateTimeHelper::TIMEZONE_UTC);
        $outputDate   = $this->fetchDateParameter('output_date', $baseDate, DateTimeHelper::TIMEZONE_UTC);
        $outputFormat = $this->fetchParameter('output_format');

        return ee()->localize->format_date(
            $outputFormat,
            $outputDate->getTimestamp(),
            $outputDate->getTimezone()->getName()
        );
    }

    /**
     * Parser for the {exp:calendar:month_list} tag
     *
     * @return string
     */
    public function month_list()
    {
        $dateRangeStart = $this->fetchDateParameter('date_range_start') ?: new Carbon($this->getUserTimezone());
        $dateRangeEnd   = $this->fetchDateParameter('date_range_end');
        $limit          = $this->fetchParameter('limit');

        $dateRangeStart->day = 1;
        $dateRangeEnd->day   = 1;

        $iterations = 12;
        if ($dateRangeEnd && !$limit) {
            $iterations = $dateRangeStart->diffInMonths($dateRangeEnd) + 1;
        } else if (!$dateRangeEnd && $limit) {
            $iterations = $limit;
        } else if ($dateRangeEnd && $limit) {
            $iterations = min($dateRangeStart->diffInMonths($dateRangeEnd), $limit);
        }

        $currentDate = new Carbon(DateTimeHelper::TIMEZONE_UTC);
        $monthList   = array();
        while ($iterations > 0) {
            $monthList[] = array(
                'date'          => $dateRangeStart->format('U:e'),
                'current_month' => $dateRangeStart->format('Ym') === $currentDate->format('Ym'),
                'current_year'  => $dateRangeStart->format('Y') === $currentDate->format('Y'),
            );
            $dateRangeStart->addMonth();

            $iterations--;
        }

        $monthList = EventTags::prefixTemplateVariables($monthList, $this->lower_name);

        return EventTags::parseTagdataPreservingDates($monthList);
    }

    /**
     * Renders the {exp:calendar:calendars} tag
     *
     * @return array EE tagdata
     */
    public function calendars()
    {
        $queryParams = new CalendarQueryParameters();
        $queryParams
            ->setCalendarId($this->params['calendar_id'])
            ->setCalendarShortName($this->fetchParameter('calendar_short_name'))
            ->setSiteId($this->params['site_id'])
            ->setSiteName($this->params['site'])
            ->setLimit($this->params['limit'])
            ->setOffset(ee()->TMPL->fetch_param('orderby', 'name'))
            ->setSort($this->fetchParameter('sort', 'asc'))
            ->setOrderBy($this->fetchParameter('orderby'));

        // Build query
        $calendarRepository = CalendarRepository::getInstance();
        $calendars          = $calendarRepository
            ->getAllAsArraysForTagData($queryParams);

        if (empty($calendars)) {
            return $this->show_no_results();
        }

        $calendars = EventTags::prefixTemplateVariables($calendars, $this->lower_name);

        $this->paginateResults($calendars, $this->fetchParameter('limit', null));

        return ee()->TMPL->tagdata;
    }

    /**
     * Parses the {ext:calendar:ics_export} tag
     * Outputs the data as a file download
     *
     * @return string
     */
    public function ics_export()
    {
        $exporter = new ExportCalendarToIcs($this->getEventsForExport());

        return $exporter->export();
    }

    /**
     * Outputs the ICS string
     */
    public function ics_subscription()
    {
        $exporter = new ExportCalendarToIcs($this->getEventsForExport());

        return $exporter->output();
    }

    /**
     * Outputs the ICS string
     */
    public function icsSubscription()
    {
        $hash = ee()->input->get('hash');

        $calendar = CalendarRepository::getInstance()->getByHash($hash);

        if (!$calendar) {
            die;
        }

        $eventRepository = EventRepository::getInstance();

        $eventQueryParameters = new EventQueryParameters();
        $eventQueryParameters
            ->setCalendarId($calendar->id);

        $events = $eventRepository->getFromQueryParameters($eventQueryParameters);

        $exporter = new ExportCalendarToIcs($events);

        die($exporter->output());
    }

    /**
     * Returns a path to the third-party theme folder
     *
     * @return string
     */
    public function theme_folder_url()
    {
        return $this->theme_url;
    }

    /**
     * @param array $fieldData
     *
     * @return string
     */
    public function fieldTypeWidget($fieldData)
    {
        return $this->view('field', $fieldData, true);
    }

    /**
     * Where all of the common parameters are built into one giant array
     *
     * @return array The filled out params
     */
    private function getCommonParameters()
    {
        if (!isset(ee()->TMPL)) {
            ee()->load->library('template', null, 'TMPL');
        }

        if (!isset(ee()->TMPL->tagparams)) {
            ee()->TMPL->tagparams = array();
        }

        // Unset empty params
        if (!empty(ee()->TMPL->tagparams)) {
            foreach (ee()->TMPL->tagparams as $key => $param) {
                if (empty($param)) {
                    unset(ee()->TMPL->tagparams[$key]);
                }
            }
        }

        $params = array(
            'site_id'             => ee()->TMPL->fetch_param('site_id'),
            'site'                => ee()->TMPL->fetch_param('site'),
            'hash'                => ee()->TMPL->fetch_param('hash'),
            'calendar_id'         => ee()->TMPL->fetch_param('calendar_id'),
            'calendar_short_name' => ee()->TMPL->fetch_param('calendar_short_name'),
            'entry_id'            => ee()->TMPL->fetch_param('entry_id'),
            'event_id'            => ee()->TMPL->fetch_param('event_id'),
            'limit'               => (int) ee()->TMPL->fetch_param('limit', 1000),
            'orderby'             => ee()->TMPL->fetch_param('orderby', 'start_date'),
            'sort'                => ee()->TMPL->fetch_param('sort', 'asc'),
            'offset'              => (int) ee()->TMPL->fetch_param('offset', 0),
            'paginate'            => ee()->TMPL->fetch_param('paginate'),
            'status'              => ee()->TMPL->fetch_param('status', 'open'),
            'channel'             => ee()->TMPL->fetch_param('channel'),
            'channel_id'          => ee()->TMPL->fetch_param('channel_id'),
            'author_id'           => ee()->TMPL->fetch_param('author_id'),
            'theme_folder_url'    => ee()->TMPL->fetch_param('theme_folder_url'),
        );

        if (!$params['site_id'] && !$params['site']) {
            $params['site_id'] = ee()->config->item('site_id');
        }

        // Convert calendar name to ID if none given
        if ($params['calendar_short_name'] && !$params['calendar_id']) {
            /** @var CalendarModel $calendar */
            $calendar = ee('Model')
                ->get('calendar:Calendar')
                ->filter('url_title', $params['calendar_short_name'])
                ->first();

            $params['calendar_id'] = is_null($calendar) ? false : $calendar->id;
        }

        // Ensure valid sort
        if (!in_array($params['sort'], array('asc', 'desc'))) {
            $params['sort'] = 'asc';
        }

        return $params;
    }

    /**
     * Gets the date parameters and instantiates a Carbon object for the current date
     *
     * @return array [date => Carbon, year => int, month => int, day => int]
     */
    private function getDateParameters()
    {
        $now   = Carbon::now(DateTimeHelper::TIMEZONE_UTC);
        $dates = array(
            'month' => ee()->TMPL->fetch_param('month', $now->month),
            'year'  => ee()->TMPL->fetch_param('year', $now->year),
            'day'   => ee()->TMPL->fetch_param('day', $now->day),
        );

        $dates['date'] = Carbon::createFromDate(
            $dates['year'],
            $dates['month'],
            $dates['day'],
            DateTimeHelper::TIMEZONE_UTC
        );

        return $dates;
    }

    /**
     * Collects template tag parameters and retrieves events based on them
     *
     * @param Carbon $rangeStart
     * @param Carbon $rangeEnd
     * @param bool   $strictRange - will not look in the past or future to get additional events
     * @param int    $limit
     * @param int    $offset
     * @param null   $fetchRecurrences
     *
     * @return Collection|Event[]
     */
    private function getEventsFromParams(
        Carbon $rangeStart = null,
        Carbon $rangeEnd = null,
        $strictRange = false,
        $limit = null,
        $offset = null,
        $fetchRecurrences = null
    ) {
        $eventRepository = EventRepository::getInstance();

        if (!$rangeStart) {
            $rangeStart = Carbon::now($this->getUserTimezone());
        }
        if (!$rangeEnd) {
            $rangeEnd = Carbon::now($this->getUserTimezone());
            $rangeEnd->setTime(23, 59, 59);
        }

        $eventQueryParameters = new EventQueryParameters();
        $eventQueryParameters
            ->setOrderBy($this->params['orderby'])
            ->setSort($this->params['sort'])
            ->setCalendarId($this->params['calendar_id'])
            ->setCalendarName($this->params['calendar_short_name'])
            ->setUrlTitle($this->fetchParameter('url_title'))
            ->setEntryIds($this->params['entry_id'] ?: $this->params['event_id'])
            ->setLimit($limit)
            ->setOffset($offset)
            ->setDateRangeStart($rangeStart)
            ->setDateRangeEnd($rangeEnd);

        $authorId = $this->fetchParameter('author_id', null);
        if (strpos($authorId, 'CURRENT_USER') !== false) {
            $authorId = str_replace('CURRENT_USER', ee()->session->userdata('member_id') ?: 0, $authorId);
        }

        $loadConsumingData = $this->fetchParameter('load_resource_consuming_data', false);
        $eventQueryParameters->setLoadResourceConsumingData($loadConsumingData);

        $channelQueryParameters = new ChannelQueryParameters();
        $channelQueryParameters
            ->setName($this->params['channel'])
            ->setId($this->params['channel_id'])
            ->setAuthorId($authorId)
            ->setStatus($this->params['status'])
            ->setSiteId($this->params['site_id'])
            ->setSiteName($this->params['site'])
            ->setCategory($this->fetchParameter('category'))
            ->setUncategorizedEntriesExcluded($this->fetchParameter('uncategorized_entries'));

        $eventQueryParameters->setChannelQueryParameters($channelQueryParameters);

        if (null === $fetchRecurrences) {
            $fetchRecurrences = $this->fetchParameter('show_recurrences', true);
            if (is_string($fetchRecurrences)) {
                if (strtolower($fetchRecurrences) !== 'next') {
                    $fetchRecurrences = $this->fetchBoolParameter('show_recurrences', true);
                } else {
                    $fetchRecurrences = 'next';
                }
            }
        }

        $events = $eventRepository->getArrayResult($eventQueryParameters, $strictRange, $fetchRecurrences);

        return $events;
    }

    /**
     * @return Collection|Event[]
     */
    private function getEventsForExport()
    {
        $hash              = $this->params['hash'];
        $calendarId        = $this->params['calendar_id'];
        $calendarShortName = $this->params['calendar_short_name'];
        $eventId           = $this->params['event_id'];
        $entryId           = $this->params['entry_id'];

        if (!$hash && !$calendarId && !$calendarShortName && !$eventId && !$entryId) {
            return new Collection();
        }

        $eventRepository = EventRepository::getInstance();

        $eventQueryParameters = new EventQueryParameters();
        $eventQueryParameters
            ->setOrderBy($this->params['orderby'])
            ->setSort($this->params['sort'])
            ->setEntryIds($this->params['entry_id'] ?: $this->params['event_id'])
            ->setIcsHash($this->params['hash'])
            ->setCalendarId($this->params['calendar_id'])
            ->setCalendarName($this->params['calendar_short_name'])
            ->setUrlTitle($this->fetchParameter('url_title'))
            ->setDateRangeStart($this->fetchDateParameter('date_range_start'))
            ->setDateRangeEnd($this->fetchDateParameter('date_range_end'))
            ->setLoadResourceConsumingData(false);

        $authorId = $this->fetchParameter('author_id', null);
        if (strpos($authorId, 'CURRENT_USER') !== false) {
            $authorId = str_replace('CURRENT_USER', ee()->session->userdata('member_id') ?: 0, $authorId);
        }

        $channelQueryParameters = new ChannelQueryParameters();
        $channelQueryParameters
            ->setName($this->params['channel'])
            ->setId($this->params['channel_id'])
            ->setAuthorId($authorId)
            ->setStatus($this->params['status'])
            ->setSiteId($this->params['site_id'])
            ->setSiteName($this->params['site'])
            ->setCategory($this->fetchParameter('category'))
            ->setUncategorizedEntriesExcluded($this->fetchParameter('uncategorized_entries'));

        $eventQueryParameters->setChannelQueryParameters($channelQueryParameters);

        $events = $eventRepository->getArrayResult($eventQueryParameters, false, false);

        $eventIds = array();
        foreach ($events as $event) {
            $eventIds[] = $event['event_id'];
        }

        return $eventRepository->getByIdList($eventIds);
    }

    /**
     * Takes a param string and sorts results into truncated entries
     *
     * @param array  $results The tagdata
     * @param int    $limit
     * @param string $prefix
     */
    private function paginateResults($results, $limit = null, $prefix = "P")
    {
        $totalItems         = count($results);
        ee()->TMPL->tagdata = $this->pagination_prefix_replace('calendar:', ee()->TMPL->tagdata);
        ee()->load->library('pagination');

        $shouldPaginate = $this->params['paginate'];

        /** @var \Pagination_object $pagination */
        $pagination = ee()->pagination->create();

        // A nasty little hack which makes {switch} tags work when prefixed with {calendar:switch}
        ee()->TMPL->tagdata = str_replace(LD . 'calendar:switch', LD . 'switch', ee()->TMPL->tagdata);

        ee()->TMPL->tagdata = $pagination->prepare(ee()->TMPL->tagdata);

        if ($shouldPaginate) {
            $pagination->prefix = $prefix;
            $pagination->build($totalItems, $limit);
        }

        $results     = array_slice($results, $pagination->offset, $limit);
        $resultCount = count($results);

        // This little logic shouldn't be here, but there is no proper way to
        // move it upwards because of how backwards the whole pagination process
        // is for events, due to recurrences
        for ($i = 0; $i < $resultCount; $i++) {
            if (!isset($results[$i]['calendar:event_count'])) {
                break;
            }

            $results[$i]['calendar:event_count']         = $i + 1;
            $results[$i]['calendar:event_total_results'] = $resultCount;
        }
        // ---------------------------------------------------------------------

        ee()->TMPL->tagdata = EventTags::parseTagdataPreservingDates($results);

        ee()->TMPL->tagdata = $pagination->render(ee()->TMPL->tagdata);

        if (!$results) {
            ee()->TMPL->tagdata = $this->show_no_results();
        }
    }

    /**
     * Fetches a template parameter
     *
     * @param string $parameterName
     * @param mixed  $defaultValue
     *
     * @return mixed|null
     */
    private function fetchParameter($parameterName, $defaultValue = null)
    {
        return ee()->TMPL->fetch_param($parameterName, $defaultValue);
    }

    /**
     * Returns true if the parameter is true, or a value of "yes", "y" or "true"
     *
     * @param string $parameterName
     * @param bool   $defaultValue
     *
     * @return bool
     */
    private function fetchBoolParameter($parameterName, $defaultValue = false)
    {
        $defaultValue = (bool) $defaultValue;
        $parameter    = $this->fetchParameter($parameterName, $defaultValue);

        if ($parameter === true || in_array($parameter, array('y', 'yes', 'true'))) {
            return true;
        }

        return false;
    }

    /**
     * Fetches a date parameter. If one is found - it is converted to Carbon and returned
     * Various magic replacements also take place:
     *   In a Y-m-d date format words "year", "month", "day" are replaced with the current date values
     *   And the word "last" is replaced with the last day of current month
     *
     * @param string $parameterName
     * @param Carbon $relativeDate - Will use this date as a replacement point for placeholders
     * @param string $timezone     - if not provided - will default to users timezone
     *
     * @return null|Carbon
     */
    private function fetchDateParameter($parameterName, Carbon $relativeDate = null, $timezone = null)
    {
        $parameter = $this->fetchParameter($parameterName);

        if (!$parameter) {
            return null;
        }

        if (is_null($timezone)) {
            $timezone = $this->getUserTimezone();
        }

        // We require some custom logic for "date_range_start" and "date_range_end" parameters
        // If it's the range start and the value is "today" - we set the time to 00:00:00,
        // If it's the range end and the value is "today" - we set the time to 23:59:59
        if ($parameterName === "date_range_start" && strtolower($parameter) === "today") {
            $parameter = $parameter . ' 00:00:00';
        } else if ($parameterName === "date_range_end" && strtolower($parameter) === "today") {
            $parameter = $parameter . ' 23:59:59';
        }

        return DateTimeHelper::parseDateForCustomValues($parameter, $relativeDate, $timezone);
    }

    /**
     * Generates common day parameters relative to today
     *
     * @param Carbon $date
     *
     * @return array
     */
    private function getDayTagData(Carbon $date)
    {
        $currentDay = new Carbon($this->getUserTimezone());

        return array(
            'day_is_weekend' => $date->isWeekend(),
            'day_is_weekday' => $date->isWeekday(),
            'current_day'    => $date->format('Ymd') === $currentDay->format('Ymd'),
            'current_week'   => $date->format('YW') === $currentDay->format('YW'),
            'current_month'  => $date->format('Ym') === $currentDay->format('Ym'),
            'current_year'   => $date->format('Y') === $currentDay->format('Y'),
        );
    }

    /**
     * Goes through the list of events and sorts them by DATE
     * Returns a 2D array of [date => [event, ..], ..]
     *
     * @param array $events
     * @param int   $overlapThreshold - Number of hours in the next that which are considered to still be yesteday
     *                                E.g. - an event that ends at 4am next day with $overlapThreshold set to 4 or 5
     *                                would not show up the next day.
     *
     * @return array
     */
    private static function sortEventsByDate(array $events, $overlapThreshold = 0)
    {
        $eventsByDate = array();
        foreach ($events as $event) {
            /** @var Carbon $startDate */
            $startDate = $event['start_date_carbon'];

            /** @var Carbon $endDate */
            $endDate = $event['end_date_carbon'];

            $dateString = $startDate->toDateString();

            if (!isset($eventsByDate[$dateString])) {
                $eventsByDate[$dateString] = array();
            }

            if ($event['event_multi_day']) {
                $dayDiff     = $startDate->copy()->setTime(0, 0, 0)->diffInDays($endDate->copy()->setTime(0, 0, 0));
                $dayIterator = $dayDiff;
                $nextDate    = $startDate->copy();
                while ($dayIterator > 0) {
                    $nextDayDateString = $nextDate->addDay()->toDateString();

                    if ($dayIterator === 1) {
                        $hour   = $endDate->hour;
                        $minute = $endDate->minute;
                        $second = $endDate->second;

                        if ($hour < $overlapThreshold || ($hour == $overlapThreshold && $minute == 0 && $second == 0)) {
                            if ($dayDiff <= 1) {
                                $event['event_multi_day'] = false;
                            }
                            break;
                        }
                    }

                    if (!isset($eventsByDate[$nextDayDateString])) {
                        $eventsByDate[$nextDayDateString] = array();
                    }

                    $eventsByDate[$nextDayDateString][] = $event;
                    $dayIterator--;
                }
            }

            $eventsByDate[$dateString][] = $event;
        }

        foreach ($eventsByDate as &$eventList) {
            $count      = 0;
            $totalCount = count($eventList);
            foreach ($eventList as &$event) {
                $event['event_count']         = ++$count;
                $event['event_total_results'] = $totalCount;
            }
        }

        return $eventsByDate;
    }

    /**
     * Sorts as follows:
     * 1) Multi day events DESC by their end-dates
     * 2) All day events
     * 3) Other events ASC by start-date
     *
     * @param array $events
     *
     * @return array
     */
    private function sortEventsForMonthDay(array $events)
    {
        $ascending = $this->params['sort'] == 'asc';
        usort(
            $events,
            function ($a, $b) use ($ascending) {
                /** @var Carbon $startDateA */
                $startDateA = $a['start_date_carbon'];
                /** @var Carbon $startDateB */
                $startDateB = $b['start_date_carbon'];

                $aIsMultiDay = $a['event_multi_day'];
                $bIsMultiDay = $b['event_multi_day'];

                $aIsAllDay = $a['event_all_day'];
                $bIsAllDay = $b['event_all_day'];

                if ($aIsMultiDay && !$bIsMultiDay) {
                    return -1;
                }

                if (!$aIsMultiDay && $bIsMultiDay) {
                    return 1;
                }

                if ($aIsMultiDay && $bIsMultiDay) {
                    $endDateA = $a['end_date_carbon'];
                    $endDateB = $b['end_date_carbon'];

                    $result = $endDateA->cmp($endDateB);

                    return $ascending ? -$result : $result;
                }

                if ($aIsAllDay && !$bIsAllDay) {
                    return -1;
                }

                if (!$aIsAllDay && $bIsAllDay) {
                    return 1;
                }

                $result = $startDateA->cmp($startDateB);

                return $ascending ? $result : -$result;
            }
        );

        return $events;
    }

    /**
     * Sorts as follows:
     * 1) All day events
     * 2) Multi day events DESC by their end-dates
     * 3) Other events ASC by start-date
     *
     * @param array $events
     *
     * @return array
     */
    private function sortEventsForDay(array $events)
    {
        $ascending = $this->params['sort'] == 'asc';

        usort(
            $events,
            function ($a, $b) use ($ascending) {
                /** @var Carbon $startDateA */
                $startDateA = $a['start_date_carbon'];
                /** @var Carbon $startDateB */
                $startDateB = $b['start_date_carbon'];

                $aIsMultiDay = $a['event_multi_day'];
                $bIsMultiDay = $b['event_multi_day'];

                $aIsAllDay = $a['event_all_day'];
                $bIsAllDay = $b['event_all_day'];

                $endDateA = $a['end_date_carbon'];
                $endDateB = $b['end_date_carbon'];

                if ($aIsAllDay && !$bIsAllDay) {
                    return -1;
                } else if (!$aIsAllDay && $bIsAllDay) {
                    return 1;
                } else if ($aIsAllDay && $bIsAllDay) {
                    if ($aIsMultiDay && !$bIsMultiDay) {
                        return 1;
                    } else if (!$aIsMultiDay && $bIsMultiDay) {
                        return -1;
                    } else if ($aIsMultiDay && $bIsMultiDay) {
                        $result = $endDateA->cmp($endDateB);

                        return $ascending ? -$result : $result;
                    }

                    return 0;
                }

                if ($aIsMultiDay && !$bIsMultiDay) {
                    return -1;
                } else if (!$aIsMultiDay && $bIsMultiDay) {
                    return 1;
                } else if ($aIsMultiDay && $bIsMultiDay) {
                    $result = $endDateA->cmp($endDateB);

                    return $ascending ? -$result : $result;
                }

                $result = $startDateA->cmp($startDateB);

                return $ascending ? $result : -$result;
            }
        );

        $count      = 0;
        $totalCount = count($events);
        foreach ($events as &$event) {
            $event['event_count']         = ++$count;
            $event['event_total_results'] = $totalCount;
        }

        return $events;
    }

    /**
     * Module Specific No Results Parsing
     *
     * Looks for (your_module):no_results and uses that,
     * otherwise it returns the default no_results conditional
     *
     * @return string
     */
    private function show_no_results()
    {
        if (preg_match(
            "/" . LD . "if " . preg_quote($this->lower_name) . ":no_results" .
            RD . "(.*?)" . LD . preg_quote('/', '/') . "if" . RD . "/s",
            ee()->TMPL->tagdata,
            $match
        )
        ) {
            return $match[1];
        } else {
            return ee()->TMPL->no_results();
        }
    }

    /**
     * Attempts to get the users timezone
     * If not present - gets one from site settings
     * Else - returns UTC timezone
     *
     * @return string
     */
    private function getUserTimezone()
    {
        return DateTimeHelper::getUserTimezone();
    }
}
