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

use EllisLab\ExpressionEngine\Model\File\FileDimension;
use EllisLab\ExpressionEngine\Service\Database\Query;
use EllisLab\ExpressionEngine\Service\Model\Collection;
use EllisLab\ExpressionEngine\Service\Model\Query\Builder;
use Solspace\Addons\Calendar\Exceptions\CacheException;
use Solspace\Addons\Calendar\Helpers\DateTimeHelper;
use Solspace\Addons\Calendar\Library\Carbon\Carbon;
use Solspace\Addons\Calendar\Library\Helpers;
use Solspace\Addons\Calendar\Library\RRule\RRule;
use Solspace\Addons\Calendar\Library\When\When;
use Solspace\Addons\Calendar\Model\CalendarModel;
use Solspace\Addons\Calendar\Model\Event;
use Solspace\Addons\Calendar\Model\Exclusion;
use Solspace\Addons\Calendar\Model\SelectDate;
use Solspace\Addons\Calendar\QueryParameters\EventQueryParameters;

class EventRepository extends AbstractRepository
{
    const CUSTOM_DATA_FIELD_BATCH_SIZE = 40;

    private static $eventArrayResultCache = array();
    private static $fileDimensionCache;

    /** @var Carbon */
    private static $now;

    /**
     * @return EventRepository
     */
    public static function getInstance()
    {
        return parent::getInstance();
    }

    /**
     * Takes an id or an array of IDs and returns the eloquent collection or
     * object
     *
     * @param int|array $id An array of ids or id string
     *
     * @return Event
     */
    public function get($id)
    {
        if (is_array($id)) {
            return ee('Model')
                ->get('calendar:Event')
                ->filter('id', 'IN', $id)
                ->all();
        } else {
            $event = ee('Model')
                ->get('calendar:Event')
                ->filter('id', $id)
                ->first();

            if (!$event instanceof Event) {
                $event = ee('Model')->make('calendar:Event');
            }

            return $event;
        }
    }

    /**
     * Gets Events form a calendar
     *
     * @param Collection $calendars A Calendar Collection
     * @param Carbon     $rangeStart
     * @param Carbon     $rangeEnd
     *
     * @return Collection|\Solspace\Addons\Calendar\Model\Event[] An Event Collection
     */
    public function getFromCalendars(Collection $calendars, Carbon $rangeStart = null, Carbon $rangeEnd = null)
    {
        $calendarIds = $calendars->getIds();

        if ($calendarIds) {
            /** @var Builder $builder */
            $builder = ee('Model')
                ->get('calendar:Event')
                ->filter('calendar_id', 'IN', $calendarIds);

            $builder->filterGroup();

            if ($rangeStart || $rangeEnd) {
                $builder->filterGroup();

                if ($rangeStart) {
                    $builder->filter('start_date', '>=', $rangeStart->toDateTimeString());
                }

                if ($rangeEnd) {
                    $builder->filter('start_date', '<=', $rangeEnd->toDateTimeString());
                }

                $builder->endFilterGroup();

                $builder->orFilterGroup();
                $builder->orFilter('rrule', '!=', null);
                $builder->orFilter('select_dates', '!=', null);
                $builder->endFilterGroup();
            }

            $builder->endFilterGroup();

            $eventList = $builder->all();
        } else {
            $eventList = new Collection();
        }

        return $eventList;
    }

    /**
     * Returns an event collection for the given event ids
     *
     * @param array $ids
     *
     * @return Event[]|Collection An Event Collection
     */
    public function getByIdList(array $ids)
    {
        if (empty($ids)) {
            return new Collection();
        }

        return ee('Model')
            ->get('calendar:Event')
            ->filter('id', 'IN', $ids)
            ->all();
    }

    /**
     * Returns an event collection from a field ID
     *
     * @param string $id The Field id
     *
     * @return Event[]|Collection An Event Collection
     */
    public function getFromFieldId($id)
    {
        return ee('Model')
            ->get('calendar:Event')
            ->filter('field_id', $id)
            ->all();
    }

    /**
     * Returns an event collection from an entry ID
     *
     * @param string $id The Field id
     *
     * @return Event[]|Collection An Event Collection
     */
    public function getFromEntryId($id)
    {
        return ee('Model')
            ->get('calendar:Event')
            ->filter('entry_id', $id)
            ->all();
    }

    /**
     * Returns an event from a field and site id
     *
     * @param int $fieldId
     * @param int $entryId
     *
     * @return Event An Event Object
     */
    public function getOrCreateFromFieldAndEntryId($fieldId, $entryId)
    {
        $event = ee('Model')
            ->get('calendar:Event')
            ->filter('field_id', $fieldId)
            ->filter('entry_id', $entryId)
            ->first();

        if (!$event) {
            $event           = Event::create();
            $event->field_id = $fieldId;
            $event->entry_id = $entryId;
        }

        return $event;
    }

    /**
     * Returns an array indexed by each Event ID containing all exclusion Carbon dates for that event
     *
     * @param array $eventIds
     *
     * @return array - [eventId => [exclusionId => exclusionDate, ..], ..]
     */
    public function getExclusionDatesForEvents(array $eventIds)
    {
        if (empty($eventIds)) {
            return array();
        }

        /** @var Collection|Exclusion[] $exclusions */
        $exclusions = ee('Model')
            ->get('calendar:Exclusion')
            ->filter('event_id', 'IN', $eventIds)
            ->all();

        $exclusionsByEventId = array();
        foreach ($exclusions as $exclusion) {
            $eventId = $exclusion->event_id;

            if (!isset($exclusionsByEventId[$eventId])) {
                $exclusionsByEventId[$eventId] = array();
            }

            $date                                          = new Carbon($exclusion->date, DateTimeHelper::TIMEZONE_UTC);
            $exclusionsByEventId[$eventId][$exclusion->id] = $date->toDateString();
        }

        unset($exclusions);

        return $exclusionsByEventId;
    }

    /**
     * @param Event $event
     *
     * @return Collection|Exclusion[]
     */
    public function getExclusionsForEvent(Event $event)
    {
        $exclusions = ee('Model')
            ->get('calendar:Exclusion')
            ->filter('event_id', $event->id)
            ->all();

        return $exclusions;
    }

    /**
     * @param Event $event
     *
     * @return Collection|SelectDate[]
     */
    public function getSelectDatesForEvent(Event $event)
    {
        $selectDates = ee('Model')
            ->get('calendar:SelectDate')
            ->filter('event_id', $event->id)
            ->all();

        return $selectDates;
    }

    /**
     * Builds a Query Builder from Event based parameters specified in $queryParameters
     *
     * @param EventQueryParameters $queryParameters
     *
     * @return Collection|Event[]
     */
    public function getFromQueryParameters(EventQueryParameters $queryParameters)
    {
        /** @var Builder $builder */
        $builder = ee('Model')
            ->get('calendar:Event');

        if ($queryParameters->getCalendarId()) {
            self::attachBuilderParameter($builder, 'calendar_id', $queryParameters->getCalendarId());
        }

        if ($queryParameters->getEntryIds()) {
            self::attachBuilderParameter($builder, 'entry_id', $queryParameters->getEntryIds());
        }

        if ($queryParameters->getDateRangeStart()) {
            $builder->filter('start_date', '>=', $queryParameters->getDateRangeStart()->toDateTimeString());
        }

        if ($queryParameters->getDateRangeEnd()) {
            $builder->filter('end_date', '<=', $queryParameters->getDateRangeEnd()->toDateTimeString());
        }

        if ($queryParameters->getLimit()) {
            $builder->limit($queryParameters->getLimit());
        }

        if ($queryParameters->getOffset()) {
            $builder->offset($queryParameters->getOffset());
        }

        if ($queryParameters->getChannelQueryParameters()) {
            $channelParameters = $queryParameters->getChannelQueryParameters();

            if ($channelParameters->getCategory()) {
                $builder->filter('category_id', $channelParameters->getCategory());
            }
        }

        return $builder->all();
    }

    /**
     * @param EventQueryParameters $queryParameters
     * @param bool                 $strictRange
     * @param bool|string          $fetchRecurrences - If false - won't fetch recurrences
     *
     * @return array
     * @throws CacheException
     */
    public function getArrayResult(
        EventQueryParameters $queryParameters,
        $strictRange = false,
        $fetchRecurrences = true
    ) {
        $queryParameterHash = $queryParameters->getHash();
        if ($this->isEventArrayInCache($queryParameterHash)) {
            return $this->getEventArrayFromCache($queryParameterHash);
        }

        $rangeStart = $queryParameters->getDateRangeStart();
        $rangeEnd   = $queryParameters->getDateRangeEnd();

        if ($rangeStart) {
            $rangeStart = new Carbon($rangeStart->toDateTimeString(), DateTimeHelper::TIMEZONE_UTC);
        }

        if ($rangeEnd) {
            $rangeEnd = new Carbon($rangeEnd->toDateTimeString(), DateTimeHelper::TIMEZONE_UTC);
        }

        if (!$strictRange) {
            // We must move the range start a month back after the events have been fetched
            // This will prevent issues with the recurrence rule generation
            if ($rangeStart) {
                $rangeStart->subMonth();
            }
            if ($rangeEnd) {
                $rangeEnd->addDays(6);
            }
        }

        $currentSiteId = ee()->config->item('site_id');
        $channelQueryParameters = $queryParameters->getChannelQueryParameters();

        if (!$channelQueryParameters->getSiteId()) {
            $channelQueryParameters->setSiteId($currentSiteId);
        }

        // Building a custom query for optimal speed performance
        // ======================================================

        $channelIds = array();
        if ($channelQueryParameters->getName()) {
            $channelNames = explode('|', $channelQueryParameters->getName());
            $channelIdResults = ee()->db
                ->select('channel_id')
                ->from('channels')
                ->where_in('channel_name', $channelNames)
                ->get()
                ->result_array();

            foreach ($channelIdResults as $channelIdResult) {
                $channelIds[] = $channelIdResult['channel_id'];
            }
        }

        if ($channelQueryParameters->getId()) {
            $explodedIds = explode('|', $channelQueryParameters->getId());
            $channelIds = array_merge($channelIds, $explodedIds);
        }

        $fieldIds = array();
        if (!empty($channelIds)) {
            $channelIds = array_map('trim', $channelIds);
            $channelIds = array_map('intval', $channelIds);

            $fieldIdResults = ee()->db
                ->select('field_id')
                ->from('channels_channel_fields')
                ->where_in('channel_id', $channelIds)
                ->get()
                ->result_array();

            foreach ($fieldIdResults as $idResult) {
                $fieldIds[] = $idResult['field_id'];
            }

            $fieldIdResults = ee()->db
                ->select('cfgf.field_id')
                ->from('channel_field_groups_fields cfgf')
                ->join('channels_channel_field_groups ccfg', 'ccfg.group_id = cfgf.group_id')
                ->where_in('ccfg.channel_id', $channelIds)
                ->get()
                ->result_array();

            foreach ($fieldIdResults as $idResult) {
                if (in_array($idResult['field_id'], $fieldIds)) {
                    continue;
                }

                $fieldIds[] = $idResult['field_id'];
            }
        }

        $channelFieldsBuilder = ee()->db
            ->select('cf.field_id, cf.field_name, cf.field_type')
            ->from('channel_fields cf')
            ->where_in('cf.site_id', array($channelQueryParameters->getSiteId(), 0));

        if ($fieldIds) {
            $channelFieldsBuilder->where_in('cf.field_id', $fieldIds);
        }

        $channelFields = $channelFieldsBuilder
            ->get()
            ->result_array();

        $fileFieldNames = array();
        foreach ($channelFields as $field) {
            if ($field['field_type'] === 'file') {
                $fileFieldNames[] = $field['field_name'];
            }
        }
        $channelFields = array_column($channelFields, 'field_name', 'field_id');

        $channelFieldDataSQL = '';
        $joinableTables = array();

        $fieldCount = 0;
        $batchableChannelFieldSql = array();
        foreach ($channelFields as $fieldId => $fieldName) {
            if (ee()->db->table_exists('channel_data_field_' . $fieldId)) {
                if ($fieldCount++ > self::CUSTOM_DATA_FIELD_BATCH_SIZE) {
                    $index = floor($fieldCount / self::CUSTOM_DATA_FIELD_BATCH_SIZE);
                    if (!isset($batchableChannelFieldSql[$index])) {
                        $batchableChannelFieldSql[$index] = array();
                    }

                    $batchableChannelFieldSql[$index][] = array(
                        'join' => array("channel_data_field_$fieldId cd_$fieldId", "cd_$fieldId.entry_id = t.entry_id"),
                        'select' => sprintf("cd_$fieldId.field_id_%d as `%s`", $fieldId, $fieldName),
                    );
                } else {
                    $joinableTables[]    = array(
                        "channel_data_field_$fieldId cd_$fieldId",
                        "cd_$fieldId.entry_id = e.entry_id"
                    );
                    $channelFieldDataSQL .= sprintf(", MIN(cd_$fieldId.field_id_%d) as `%s`", $fieldId, $fieldName);
                }
            } else {
                $channelFieldDataSQL .= sprintf(', MIN(cd.field_id_%d) as `%s`', $fieldId, $fieldName);
            }
        }

        /** @var Query $builder */
        $builder = ee()->db
            ->select(
                'e.id AS event_id,
                e.entry_id,
                e.all_day AS event_all_day,
                e.select_dates AS event_select_dates,
                e.calendar_id,
                e.rrule,
                e.start_date AS event_start_date,
                e.end_date AS event_end_date,
                c.name AS calendar_name,
                c.url_title AS calendar_short_name,
                c.color AS calendar_color,
                c.description AS calendar_description,
                c.ics_hash as calendar_ics_hash,
                chn.channel_title AS channel,
                chn.channel_name AS channel_short_name,
                m.screen_name AS author,
                m.avatar_width AS avatar_image_width,
                m.avatar_height AS avatar_image_height,
                m.avatar_filename,
                GROUP_CONCAT(cp.cat_id) AS category_ids,
                ct.site_id AS entry_site_id,
                ct.entry_date,
                ct.expiration_date,
                ct.status,
                ct.author_id,
                ct.title,
                ct.url_title,
                ct.channel_id' . $channelFieldDataSQL
            )
            ->from('calendar_events e')
            ->join('calendar_calendars c', 'c.id = e.calendar_id')
            ->join('channel_titles ct', 'ct.entry_id = e.entry_id')
            ->join('channel_data cd', 'cd.entry_id = e.entry_id')
            ->join('members m', 'm.member_id = ct.author_id')
            ->join('channels chn', 'chn.channel_id = ct.channel_id')
            ->join('category_posts cp', 'cp.entry_id = e.entry_id', 'left')
            ->group_by('e.id');

        foreach ($joinableTables as $data) {
            ee()->db->join($data[0], $data[1], 'left');
        }

        // Attaching query parameters based on tag parameters
        // =====================================================
        if ($queryParameters->getCalendarId()) {
            self::attachQueryParameter($builder, 'e.calendar_id', $queryParameters->getCalendarId());
        }
        if ($queryParameters->getIcsHash()) {
            self::attachQueryParameter($builder, 'c.ics_hash', $queryParameters->getIcsHash());
        }
        if ($queryParameters->getEventId()) {
            self::attachQueryParameter($builder, 'e.id', $queryParameters->getEventId());
        }
        if ($queryParameters->getEntryIds()) {
            self::attachQueryParameter($builder, 'e.entry_id', $queryParameters->getEntryIds());
        }
        if ($queryParameters->getCalendarName()) {
            self::attachQueryParameter($builder, 'c.url_title', $queryParameters->getCalendarName());
        }
        if ($queryParameters->getUrlTitle()) {
            self::attachQueryParameter($builder, 'ct.url_title', $queryParameters->getUrlTitle());
        }
        if ($queryParameters->getDateRangeStart()) {
            $where = sprintf(
                '(
                    (e.rrule IS NULL AND e.start_date <= "%s" AND e.end_date >= "%s")
                    OR (e.rrule IS NULL AND e.start_date >= "%s")
                    OR (e.rrule IS NOT NULL AND e.until_date >= "%s")
                    OR (e.rrule IS NULL AND e.select_dates IS NOT NULL)
                )',
                $rangeStart->toDateTimeString(),
                $rangeStart->toDateTimeString(),
                $rangeStart->toDateTimeString(),
                $rangeStart->toDateTimeString()
            );
            $builder->where($where);
        }
        if ($queryParameters->getDateRangeEnd()) {
            $builder->where('e.start_date <=', $rangeEnd->copy()->setTime(23, 59, 59)->toDateTimeString());
        }

        self::attachQueryParameter($builder, 'cd.site_id', $channelQueryParameters->getSiteId());

        if ($channelQueryParameters) {
            $authorId = $channelQueryParameters->getAuthorId();
            if (null !== $authorId && $authorId !== false) {
                self::attachQueryParameter($builder, 'ct.author_id', $authorId);
            }
            if (!$queryParameters->getCalendarId() && !$queryParameters->getCalendarName()) {
                if ($channelQueryParameters->getSiteId()) {
                    self::attachQueryParameter($builder, 'ct.site_id', $channelQueryParameters->getSiteId());
                }
                //if ($channelQueryParameters->getSiteName()) {
                //    $builder->join('sites s', 's.site_id = ct.site_id');
                //    self::attachQueryParameter($builder, 's.site_name', $channelQueryParameters->getSiteName());
                //}
            }
            if ($channelQueryParameters->getStatus()) {
                self::attachQueryParameter($builder, 'ct.status', $channelQueryParameters->getStatus());
            }
            if ($channelQueryParameters->getName()) {
                self::attachQueryParameter($builder, 'chn.channel_name', $channelQueryParameters->getName());
            }

            $category = $channelQueryParameters->getCategory();
            if ($category) {
                $builder->join('category_posts cat', 'cat.entry_id = e.entry_id', 'left');

                $includeUncategorized = $channelQueryParameters->areUncategorizedEntriesIncluded();
                self::attachQueryParameter($builder, 'cat.cat_id', $category, $includeUncategorized);
            }
        }

        // Gettings results
        $result      = $builder->get();
        $resultArray = $result->result_array();

        if (!empty($batchableChannelFieldSql)) {
            $entryIds = $indexByEntryId = array();
            foreach ($resultArray as $index => $data) {
                $entryIds[$index] = $data['entry_id'];
                $indexByEntryId[$data['entry_id']] = $index;
            }

            if ($entryIds) {

                foreach ($batchableChannelFieldSql as $batch) {
                    $query     = ee()->db->from('exp_channel_titles as t');
                    $fieldsSql = '';

                    foreach ($batch as $items) {
                        list($table, $on) = $items['join'];
                        $fieldsSql .= ', ' . $items['select'];
                        $query->join($table, $on, 'left');
                    }

                    $query->select('t.entry_id' . $fieldsSql);
                    $query->where_in('t.entry_id', $entryIds);
                    $batchResult = $query->get();
                    $batchData   = $batchResult->result_array();

                    foreach ($batchData as $item) {
                        $index = $indexByEntryId[$item['entry_id']];
                        $resultArray[$index] = array_merge($resultArray[$index], $item);
                    }
                }
            }
        }
//
//        var_dump('what');
//        var_dump($resultArray);

        // Filter out events with wrong categories
        if (self::isAmpersand($channelQueryParameters->getCategory())) {
            list($categories, $isIn) = self::explodeParameter($channelQueryParameters->getCategory());
            foreach ($resultArray as $index => $event) {
                $catList = array_filter(explode(',', $event['category_ids']));
                $catCount = count(array_intersect($categories, $catList));

                if (!$isIn && $catCount === 0) {
                    continue;
                }

                if ($isIn && $catCount === count($categories)) {
                    continue;
                }

                unset($resultArray[$index]);
            }
            $resultArray = array_values($resultArray);
        }

        if (empty($resultArray)) {
            return array();
        }

        $eventIds   = array_column($resultArray, 'event_id');
        $exceptions = ee()->db
            ->select('event_id, date')
            ->from('exp_calendar_exclusions')
            ->where_in('event_id', $eventIds)
            ->get()
            ->result_array();

        $selectDates = ee()->db
            ->select('event_id, date')
            ->from('exp_calendar_select_dates')
            ->where_in('event_id', $eventIds)
            ->get()
            ->result_array();

        $exceptionsByEventId = array();
        foreach ($exceptions as $exception) {
            $eventId = $exception['event_id'];
            if (!isset($exceptionsByEventId[$eventId])) {
                $exceptionsByEventId[$eventId] = array();
            }
            $exceptionsByEventId[$eventId][] = $exception['date'];
        }

        $selectDatesByEventId = array();

        foreach ($selectDates as $selectDate) {
            $eventId = $selectDate['event_id'];
            if (!isset($selectDatesByEventId[$eventId])) {
                $selectDatesByEventId[$eventId] = array();
            }
            $selectDatesByEventId[$eventId][] = $selectDate['date'];
        }

        // Define limits
        // Our limits will be used in PHP not SQL, since we can't know how many occurrences there are
        // For each event
        $limit  = $queryParameters->getLimit();
        $offset = $queryParameters->getOffset();

        $eventList = array();
        $iterator  = 0;

        $categories            = $this->getEventCategories();
        $timezoneDiffInSeconds = DateTimeHelper::getServerTimezoneDiffInSeconds();

        // Runs through each entry, checks if there are recurrences
        // If there are - each recurrence is modified with its respective dates
        // And pushed in the event list to be returned.

        foreach ($resultArray as $eventData) {

            $startDate = new Carbon($eventData['event_start_date'], DateTimeHelper::TIMEZONE_UTC);
            $endDate   = new Carbon($eventData['event_end_date'], DateTimeHelper::TIMEZONE_UTC);

            list($durationDays, $durationHours, $durationMinutes) = DateTimeHelper::getDuration(
                $startDate,
                $endDate,
                $eventData['event_all_day']
            );

            if ($queryParameters->isLoadResourceConsumingData()) {
                self::attachFileDimensions($eventData, $fileFieldNames);
            }

            $avatarFilename = $eventData['avatar_filename'];

            $eventData['calendar_color_light']   = Helpers::lightenDarkenColour(
                $eventData['calendar_color'],
                CalendarModel::COLOR_LIGHTNESS
            );
            $eventData['calendar_text_color']    = Helpers::getContrastYIQ($eventData['calendar_color_light']);
            $eventData['start_date_carbon']      = $startDate;
            $eventData['end_date_carbon']        = $endDate;
            $eventData['event_recurs']           = false;
            $eventData['event_recurrence_rule']  = null;
            $eventData['event_duration_days']    = $durationDays;
            $eventData['event_duration_hours']   = $durationHours;
            $eventData['event_duration_minutes'] = $durationMinutes;
            $eventData['expiration_date']        = $eventData['expiration_date'] > 0 ? $eventData['expiration_date'] : null;
            $eventData['event_all_day']          = (bool)$eventData['event_all_day'];
            $eventData['event_multi_day']        = $startDate->format('Ymd') !== $endDate->format('Ymd');
            $eventData['avatar_url']             = ee()->config->slash_item('avatar_url') . $avatarFilename;
            $eventData['entry_id_path']          = array($eventData['entry_id'], array('path_variable' => true));
            $eventData['url_title_path']         = array($eventData['url_title'], array('path_variable' => true));
            $eventData['categories']             = array();

            $categoryIds = explode(',', $eventData['category_ids']);
            foreach ($categories as $category) {
                if (in_array($category['category_id'], $categoryIds)) {
                    $eventData['categories'][] = $category;
                }
            }

            $dateSelect = false;

            if ($eventData['event_select_dates']) {
                $dateSelect = $this->isDateSelect($eventData['event_id']);
            }

            if ($dateSelect) {
                if (isset($selectDatesByEventId[$eventData['event_id']])) {
                    $occurrenceDates = $selectDatesByEventId[$eventData['event_id']];
                } else {
                    $occurrenceDates = array();
                }

                $occurrences = $this->getSelectDatesCarbonObjects($occurrenceDates);
                $eventList = $this->generateEventListFromOccurrences(
                    $occurrences,
                    $rangeStart,
                    $rangeEnd,
                    $fetchRecurrences,
                    $startDate,
                    $endDate,
                    $eventData,
                    $timezoneDiffInSeconds,
                    $eventList
                );
            } elseif ($eventData['rrule']) {

                // If the recurrence rule exists - we get all occurrences in the given timeframe
                // And add them to the list, minding the limit and offset

                $rrule = RRule::createFromRRule($eventData['rrule']);
                $until = new Carbon($rrule['until'], DateTimeHelper::TIMEZONE_UTC);
                $until->setTimezone(DateTimeHelper::TIMEZONE_UTC);

                $eventData['event_recurrence_rule'] = ucfirst($rrule['freq']);

                $r = new When();
                $r->freq($rrule['freq']);
                $r->rrule($rrule->getRRule());

                // Check if our preference rangeStart is not before the actual
                // startDate of the event
                if ($rangeStart && $fetchRecurrences) {
                    $rStartDate = $rangeStart->copy();
                } else {
                    $rStartDate = $startDate->copy();
                }

                $r->startDate($startDate);
                //if ($rStartDate->lt($startDate) || !$fetchRecurrences) {
                //    $r->startDate($startDate);
                //} else {
                //    $rStartDate->day   = $startDate->day;
                //    $rStartDate->month = $startDate->month;
                //    $r->startDate($rStartDate);
                //}

                // Same with rangeEnd
                if ($rangeEnd) {
                    if ($rangeEnd->gt($until)) {
                        $r->until($until);
                    } else {
                        $r->until($rangeEnd);
                    }
                } else {
                    $r->until($until);
                }

                $r->generateOccurrences();
                $occurrences = $r->occurrences;
                $occurrenceIteration = 0;
                $addedOccurrences = 0;

                /** @var Carbon $occurrence */
                foreach ($occurrences as $occurrence) {
                    if (isset($exceptionsByEventId[$eventData['event_id']])) {
                        if (in_array($occurrence->toDateString(), $exceptionsByEventId[$eventData['event_id']])) {
                            continue;
                        }
                    }

                    if (!$fetchRecurrences && $occurrenceIteration++ > 0) {
                        break;
                    }

                    if ($rangeStart && $rangeStart->gt($occurrence)) {
                        continue;
                    }

                    if ($fetchRecurrences === 'next' && $addedOccurrences > 0) {
                        break;
                    }

                    $diffInDays = $startDate->diffInDays($endDate, true, false);

                    // We modify the events start date to reflect on the recurrence date
                    $modifiedStartDate = clone $occurrence;
                    $modifiedStartDate->setTime($startDate->hour, $startDate->minute, $startDate->second);

                    // Save with its end-date
                    $modifiedEndDate = clone $occurrence;
                    $modifiedEndDate->setTime($endDate->hour, $endDate->minute, $endDate->second);
                    $modifiedEndDate->day += $diffInDays;

                    $modifiedEvent = $eventData;

                    $eventLastDate = $until->copy();
                    $eventLastDate->setTime($endDate->hour, $endDate->minute, $endDate->second);

                    // We just copy the events data and modify its dates and store it as a separate event
                    $modifiedEvent['start_date_carbon']              = $modifiedStartDate;
                    $modifiedEvent['end_date_carbon']                = $modifiedEndDate;
                    $modifiedEvent['event_start_date']               = $modifiedStartDate->format('U:e');
                    $modifiedEvent['event_end_date']                 = $modifiedEndDate->format('U:e');
                    $modifiedEvent['event_start_date_timestamp']     = $modifiedStartDate->timestamp - $timezoneDiffInSeconds;
                    $modifiedEvent['event_end_date_timestamp']       = $modifiedEndDate->timestamp - $timezoneDiffInSeconds;
                    $modifiedEvent['event_start_date_timestamp_utc'] = $modifiedStartDate->timestamp;
                    $modifiedEvent['event_end_date_timestamp_utc']   = $modifiedEndDate->timestamp;
                    $modifiedEvent['event_first_date']               = $startDate->format('U:e');
                    $modifiedEvent['event_first_date_timestamp']     = $startDate->timestamp - $timezoneDiffInSeconds;
                    $modifiedEvent['event_first_date_timestamp_utc'] = $startDate->timestamp;
                    $modifiedEvent['event_last_date']                = $eventLastDate->format('U:e');
                    $modifiedEvent['event_last_date_timestamp']      = $eventLastDate->timestamp - $timezoneDiffInSeconds;
                    $modifiedEvent['event_last_date_timestamp_utc']  = $eventLastDate->timestamp;
                    $modifiedEvent['event_ampm_match']               = $modifiedStartDate->format('a') === $modifiedEndDate->format('a');

                    $modifiedEvent['event_is_past']    = $this->isInPast($modifiedEndDate);
                    $modifiedEvent['event_is_current'] = $this->isCurrent($modifiedStartDate, $modifiedEndDate);
                    $modifiedEvent['event_is_future']  = $this->isFuture($modifiedStartDate);

                    $modifiedEvent['start_date_carbon'] = $modifiedStartDate;
                    $modifiedEvent['end_date_carbon']   = $modifiedEndDate;
                    $modifiedEvent['event_recurs']      = true;

                    $eventList[] = $modifiedEvent;

                    $iterator++;
                    $addedOccurrences++;
                }
            } else {
                if ($rangeStart && $rangeStart->gt($startDate) && $rangeStart->gt($endDate)) {
                    continue;
                }

                if ($rangeEnd && $rangeEnd->lt($startDate)) {
                    continue;
                }

                // Nothing special needs to get done if the event isn't recurring
                $eventData['event_start_date']               = $startDate->format('U:e');
                $eventData['event_end_date']                 = $endDate->format('U:e');
                $eventData['event_start_date_timestamp']     = $startDate->timestamp - $timezoneDiffInSeconds;
                $eventData['event_end_date_timestamp']       = $endDate->timestamp - $timezoneDiffInSeconds;
                $eventData['event_start_date_timestamp_utc'] = $startDate->timestamp;
                $eventData['event_end_date_timestamp_utc']   = $endDate->timestamp;
                $eventData['event_first_date']               = $eventData['event_start_date'];
                $eventData['event_first_date_timestamp']     = $startDate->timestamp - $timezoneDiffInSeconds;
                $eventData['event_first_date_timestamp_utc'] = $startDate->timestamp;
                $eventData['event_last_date']                = $eventData['event_end_date'];
                $eventData['event_last_date_timestamp']      = $endDate->timestamp - $timezoneDiffInSeconds;
                $eventData['event_last_date_timestamp_utc']  = $endDate->timestamp;
                $eventData['event_ampm_match']               = $startDate->format('a') === $endDate->format('a');

                $eventData['event_is_past']    = $this->isInPast($endDate);
                $eventData['event_is_current'] = $this->isCurrent($startDate, $endDate);
                $eventData['event_is_future']  = $this->isFuture($startDate);

                $eventList[]                   = $eventData;

                $iterator++;
            }
        }


        $ascending = $queryParameters->isSortAscending();
        usort(
            $eventList,
            function ($a, $b) use ($ascending) {
                /** @var Carbon $startDateA */
                $startDateA = $a['start_date_carbon'];
                /** @var Carbon $startDateB */
                $startDateB = $b['start_date_carbon'];

                $result = $startDateA->cmp($startDateB);

                return $ascending ? $result : -$result;
            }
        );

        if ($limit) {
            $eventList = array_slice($eventList, $offset, $limit);
        }

        $eventCount = count($eventList);
        for ($i = 0; $i < $eventCount; $i++) {
            $eventList[$i]['event_absolute_results'] = $eventCount;
            $eventList[$i]['event_total_results']    = $eventCount;
            $eventList[$i]['event_absolute_count']   = $i + 1;
            $eventList[$i]['event_count']            = $i + 1;
        }

        $this->storeEventArrayInCache($queryParameterHash, $eventList);

        return $this->getEventArrayFromCache($queryParameterHash);
    }

    private function generateEventListFromOccurrences($occurrences, $rangeStart, $rangeEnd, $fetchRecurrences, $startDate, $endDate, $eventData, $timezoneDiffInSeconds, $eventList)
    {
        if (!$fetchRecurrences) {
            $occurrences = [$startDate];
        } else {
            $occurrences = array_merge([$startDate], $occurrences);
        }

        $addedOccurrences = 0;

        /** @var Carbon $occurrence */
        foreach ($occurrences as $occurrence) {
            if ($rangeStart && $rangeStart->gt($occurrence)) {
                continue;
            }

            if ($rangeEnd && !$rangeEnd->gt($occurrence)) {
                continue;
            }

            if ($fetchRecurrences === 'next' && $addedOccurrences > 0) {
                break;
            }

            $diffInDays = $startDate->diffInDays($endDate, true, false);

            // We modify the events start date to reflect on the recurrence date
            $modifiedStartDate = clone $occurrence;
            $modifiedStartDate->setTime($startDate->hour, $startDate->minute, $startDate->second);

            // Save with its end-date
            $modifiedEndDate = clone $occurrence;
            $modifiedEndDate->setTime($endDate->hour, $endDate->minute, $endDate->second);
            $modifiedEndDate->day += $diffInDays;

            $modifiedEvent = $eventData;

            $eventLastDate = end($occurrences)->copy();
            $eventLastDate->setTime($endDate->hour, $endDate->minute, $endDate->second);

            // We just copy the events data and modify its dates and store it as a separate event
            $modifiedEvent['start_date_carbon']              = $modifiedStartDate;
            $modifiedEvent['end_date_carbon']                = $modifiedEndDate;
            $modifiedEvent['event_start_date']               = $modifiedStartDate->format('U:e');
            $modifiedEvent['event_end_date']                 = $modifiedEndDate->format('U:e');
            $modifiedEvent['event_start_date_timestamp']     = $modifiedStartDate->timestamp - $timezoneDiffInSeconds;
            $modifiedEvent['event_end_date_timestamp']       = $modifiedEndDate->timestamp - $timezoneDiffInSeconds;
            $modifiedEvent['event_start_date_timestamp_utc'] = $modifiedStartDate->timestamp;
            $modifiedEvent['event_end_date_timestamp_utc']   = $modifiedEndDate->timestamp;
            $modifiedEvent['event_first_date']               = $startDate->format('U:e');
            $modifiedEvent['event_first_date_timestamp']     = $startDate->timestamp - $timezoneDiffInSeconds;
            $modifiedEvent['event_first_date_timestamp_utc'] = $startDate->timestamp;
            $modifiedEvent['event_last_date']                = $eventLastDate->format('U:e');
            $modifiedEvent['event_last_date_timestamp']      = $eventLastDate->timestamp - $timezoneDiffInSeconds;
            $modifiedEvent['event_last_date_timestamp_utc']  = $eventLastDate->timestamp;
            $modifiedEvent['event_ampm_match']               = $modifiedStartDate->format('a') === $modifiedEndDate->format('a');

            $modifiedEvent['event_is_past']    = $this->isInPast($modifiedEndDate);
            $modifiedEvent['event_is_current'] = $this->isCurrent($modifiedStartDate, $modifiedEndDate);
            $modifiedEvent['event_is_future']  = $this->isFuture($modifiedStartDate);

            $modifiedEvent['start_date_carbon'] = $modifiedStartDate;
            $modifiedEvent['end_date_carbon']   = $modifiedEndDate;
            $modifiedEvent['event_recurs']      = true;

            $eventList[] = $modifiedEvent;

            $addedOccurrences++;
        }

        return $eventList;
    }

    private function getSelectDatesCarbonObjects($selectDates)
    {
        $selectDatesCarbonObjects = array();

        if (is_array($selectDates) && !empty($selectDates)) {
            foreach ($selectDates as $selectDate) {
                $selectDatesCarbonObjects[] = new Carbon($selectDate, DateTimeHelper::TIMEZONE_UTC);
            }
        }

        return $selectDatesCarbonObjects;
    }


    /**
     * @param Carbon $date
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
     * @param Carbon $endDate
     *
     * @return bool
     */
    private function isInPast(Carbon $endDate)
    {
        return (ee()->localize->now + DateTimeHelper::getServerTimezoneDiffInSeconds()) > $endDate->timestamp;
    }

    /**
     * @param Carbon $startDate
     * @param Carbon $endDate
     *
     * @return bool
     */
    private function isCurrent(Carbon $startDate, Carbon $endDate)
    {
        $now = ee()->localize->now + DateTimeHelper::getServerTimezoneDiffInSeconds();
        $start = $startDate->timestamp;
        $end   = $endDate->timestamp;

        return ($now >= $start) && ($now <= $end);
    }

    /**
     * @param Carbon $startDate
     *
     * @return bool
     */
    private function isFuture(Carbon $startDate)
    {
        return (ee()->localize->now + DateTimeHelper::getServerTimezoneDiffInSeconds()) < $startDate->timestamp;
    }

    /**
     * @param string $hash
     *
     * @return bool
     */
    private function isEventArrayInCache($hash)
    {
        return isset(self::$eventArrayResultCache[$hash]);
    }

    /**
     * @param string $hash
     *
     * @return array|null
     */
    private function getEventArrayFromCache($hash)
    {
        if ($this->isEventArrayInCache($hash)) {
            return self::$eventArrayResultCache[$hash];
        }

        return null;
    }

    /**
     * @param string $hash
     * @param array  $eventData
     *
     * @throws CacheException
     */
    private function storeEventArrayInCache($hash, array $eventData)
    {
        if ($this->isEventArrayInCache($hash)) {
            throw new CacheException("Events already stored in cache by the hash of [$hash]");
        }

        self::$eventArrayResultCache[$hash] = $eventData;
    }

    /**
     * Gets event categories if the calendar:categories tag varpair is present
     *
     * @return array
     */
    private function getEventCategories()
    {
        $fetchCategories = false;

        $categoryIdsInclusive = $categoryIdsExclusive = array();
        $groupIdsInclusive    = $groupIdsExclusive = array();
        foreach (ee()->TMPL->var_pair as $key => $val) {
            if (strncmp($key, 'calendar:categories', 19) == 0) {
                $fetchCategories = true;

                if (is_array($val)) {
                    if (isset($val['show'])) {
                        $ids = $val['show'];
                        if (substr($ids, 0, 4) === 'not ') {
                            $categoryIdsExclusive = explode('|', substr($ids, 4));
                        } else {
                            $categoryIdsInclusive = explode('|', $ids);
                        }
                    }
                    if (isset($val['show_group'])) {
                        $groupIds = $val['show_group'];
                        if (substr($groupIds, 0, 4) === 'not ') {
                            $groupIdsExclusive = explode('|', substr($groupIds, 4));
                        } else {
                            $groupIdsInclusive = explode('|', $groupIds);
                        }
                    }
                }
            }
        }

        if (!$fetchCategories) {
            return array();
        }

        $categoryBuilder = ee('Model')->get('Category');
        if (!empty($categoryIdsInclusive)) {
            $categoryBuilder->filter('cat_id', 'IN', $categoryIdsInclusive);
        }
        if (!empty($categoryIdsExclusive)) {
            $categoryBuilder->filter('cat_id', 'NOT IN', $categoryIdsExclusive);
        }
        if (!empty($groupIdsInclusive)) {
            $categoryBuilder->filter('group_id', 'IN', $groupIdsInclusive);
        }
        if (!empty($groupIdsExclusive)) {
            $categoryBuilder->filter('group_id', 'NOT IN', $groupIdsExclusive);
        }

        $currentURL              = $_SERVER['REQUEST_URI'];
        $useCategoryName         = ee()->config->item("use_category_name") == 'y';
        $reservedCategoryKeyword = ee()->config->item("reserved_category_word");

        if ($useCategoryName && empty($reservedCategoryKeyword)) {
            $useCategoryName = false;
        }

        $categoryModels = $categoryBuilder->all();
        $categories     = array();
        foreach ($categoryModels as $category) {
            $category = $category->toArray();
            foreach ($category as $key => $value) {
                unset($category[$key]);
                $category[str_replace('cat_', 'category_', $key)] = $value;
            }

            $category['category_group'] = $category['group_id'];
            unset($category['group_id']);

            if ($useCategoryName) {
                $path = '/' . $reservedCategoryKeyword . '/' . $category['category_url_title'];
            } else {
                $path = '/C' . $category['category_id'];
            }

            $category['active'] = strpos($currentURL, $path) !== false;
            $category['path']   = array($path, array('path_variable' => true));

            $categories[] = $category;
        }

        return $categories;
    }

    /**
     * @param int $id
     *
     * @return array
     */
    private function getFileDimensionsById($id)
    {
        $dimensions = $this->getFileDimensions();

        if (isset($dimensions[$id])) {
            return $dimensions[$id];
        }

        return null;
    }

    /**
     * @return array
     */
    private function getFileDimensions()
    {
        if (is_null(self::$fileDimensionCache)) {
            /** @var FileDimension[] $fileDimensions */
            $fileDimensions = ee('Model')
                ->get('FileDimension')
                ->all();

            $dimensionsByUploadLocationId = array();
            foreach ($fileDimensions as $dimension) {
                $uploadLocationId = $dimension->upload_location_id;
                if (!isset($dimensionsByUploadLocationId[$uploadLocationId])) {
                    $dimensionsByUploadLocationId[$uploadLocationId] = array();
                }

                $dimensionsByUploadLocationId[$uploadLocationId][] = $dimension->short_name;
            }

            self::$fileDimensionCache = $dimensionsByUploadLocationId;
        }

        return self::$fileDimensionCache;
    }

    /**
     * Attaches dimensions for field types
     *
     * @param array $eventData
     * @param array $fileFieldNames
     */
    private function attachFileDimensions(array &$eventData, array $fileFieldNames)
    {
        foreach ($fileFieldNames as $fileField) {
            $value = $eventData[$fileField];

            preg_match("/^{filedir_(\d+)}(.*)$/", $value, $matches);

            if (count($matches) <= 1) {
                continue;
            }

            list ($_, $uploadDirId, $path) = $matches;

            $eventData["$fileField:thumbs"] = "{filedir_$uploadDirId}_thumbs/$path";

            $dimensions = $this->getFileDimensionsById($uploadDirId);

            if (empty($dimensions)) {
                continue;
            }

            foreach ($dimensions as $dimensionShortName) {
                $eventData["$fileField:$dimensionShortName"] = "{filedir_$uploadDirId}_$dimensionShortName/$path";
            }
        }
    }
}
