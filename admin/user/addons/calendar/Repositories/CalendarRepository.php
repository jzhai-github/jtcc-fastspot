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

use EllisLab\ExpressionEngine\Service\Model\Collection;
use Solspace\Addons\Calendar\Library\Helpers;
use Solspace\Addons\Calendar\Model\CalendarModel;
use Solspace\Addons\Calendar\Model\Event;
use Solspace\Addons\Calendar\QueryParameters\CalendarQueryParameters;
use Solspace\Addons\Calendar\TemplateParameters\CalendarTemplateParameters;

class CalendarRepository extends AbstractRepository
{
    /**
     * @return CalendarRepository
     */
    public static function getInstance()
    {
        return parent::getInstance();
    }

    /**
     * Takes an array of IDs and returns the calendar models
     *
     * @param array $calendarIds
     *
     * @return CalendarModel[]|Collection
     */
    public function getByIds(array $calendarIds)
    {
        return ee('Model')
            ->get('calendar:Calendar')
            ->filter('id', 'IN', $calendarIds)
            ->all();
    }

    /**
     * @param int $calendarId
     *
     * @return CalendarModel|null
     */
    public function getById($calendarId)
    {
        $calendar = ee('Model')
            ->get('calendar:Calendar')
            ->filter('id', $calendarId)
            ->first();

        return $calendar;
    }

    /**
     * @param string $hash
     *
     * @return CalendarModel|null
     */
    public function getByHash($hash)
    {
        $calendar = ee('Model')
            ->get('calendar:Calendar')
            ->filter('ics_hash', $hash)
            ->first();

        return $calendar;
    }

    /**
     * Gets all calendar models
     *
     * @return CalendarModel[]|Collection
     */
    public function getAll()
    {
        $siteId = ee()->config->item('site_id');

        /** @var Collection|CalendarModel[] $calendars */
        $calendars = ee('Model')
            ->get('calendar:Calendar')
            ->filter('site_id', $siteId)
            ->order('name')
            ->all();

        return $calendars;
    }

    /**
     * Gets all calendars as an array for EE template tags
     *
     * @param CalendarQueryParameters $queryParameters
     *
     * @return array
     */
    public function getAllAsArraysForTagData(CalendarQueryParameters $queryParameters)
    {
        $builder = ee()->db
            ->select(
                'c.id,
                c.name,
                c.ics_hash,
                c.url_title AS short_name,
                c.description,
                c.color'
            )
            ->from('calendar_calendars c');

        if ($queryParameters->getCalendarId()) {
            self::attachQueryParameter($builder, 'c.id', $queryParameters->getCalendarId());
        }

        if ($queryParameters->getCalendarShortName()) {
            self::attachQueryParameter($builder, 'c.url_title', $queryParameters->getCalendarShortName());
        }

        if ($queryParameters->getSiteId()) {
            self::attachQueryParameter($builder, 'c.site_id', $queryParameters->getSiteId());
        }

        if ($queryParameters->getSiteName()) {
            $builder->join('sites s', 'c.site_id = s.site_id');
            self::attachQueryParameter($builder, 's.site_name', $queryParameters->getSiteName());
        }

        $orderBy = $queryParameters->getOrderBy();
        if ($orderBy) {
            $fieldMap = array(
                'calendar_name' => 'name',
                'calendar_short_name' => 'short_name',
                'calendar_id' => 'id',
            );

            if (isset($fieldMap[$orderBy])) {
                $orderByField = $fieldMap[$orderBy];
                $builder->order_by($orderByField, $queryParameters->getSort());
            }
        }

        $resultSet = $builder->get();
        $calendars = $resultSet->result_array();

        foreach ($calendars as &$calendar) {
            $calendar['color_light'] = Helpers::lightenDarkenColour($calendar['color'], CalendarModel::COLOR_LIGHTNESS);
            $calendar['text_color']  = Helpers::getContrastYIQ($calendar['color_light']);
        }

        return $calendars;
    }

    /**
     * Gets all calendar models
     *
     * @param int $memberGroupId
     *
     * @return CalendarModel[]|Collection
     */
    public function getForMemberGroup($memberGroupId)
    {
        // If current user is a super admin, we return all models
        $isSuperAdmin = (int)$memberGroupId === 1;
        if ($isSuperAdmin) {
            return $this->getAll();
        }

        $calendarIds = ee()->db
            ->select('id')
            ->from('calendar_calendar_member_groups')
            ->where(array('group_id' => $memberGroupId))
            ->get()
            ->result_array();
        $calendarIds = array_column($calendarIds, 'id');

        if (empty($calendarIds)) {
            return new Collection();
        }

        return ee('Model')
            ->get('calendar:Calendar')
            ->filter('id', 'IN', $calendarIds)
            ->filter('site_id', ee()->config->item('site_id'))
            ->all();
    }

    /**
     * Returns events for a given calendar
     *
     * @param CalendarModel $calendar
     *
     * @return Collection|Event[]
     */
    public function getEvents(CalendarModel $calendar)
    {
        return ee('Model')
            ->get('calendar:Event')
            ->filter('calendar_id', $calendar->id)
            ->all();
    }

    /**
     * Gets a list of all member group ID's for this calendar
     *
     * @param CalendarModel $calendar
     *
     * @return array
     */
    public function getMemberGroupIds(CalendarModel $calendar)
    {
        /** @var array $memberGroupIds */
        $memberGroupIds = ee()->db
            ->select('group_id')
            ->from('calendar_calendar_member_groups')
            ->where(array('id' => $calendar->id))
            ->get()
            ->result_array();

        $result = array_column($memberGroupIds, 'group_id');

        return $result;
    }

    /**
     * Gets a list of event counts per calendar
     * For calendars listed in $calendarIds
     *
     * @param array $calendarIds
     *
     * @return array
     */
    public function getCalendarEventCount(array $calendarIds)
    {
        /** @var array $eventCounts */
        $eventCounts = ee()->db
            ->select('COUNT(e.id) AS count, e.calendar_id')
            ->from('calendar_events AS e')
            ->where_in('e.calendar_id', $calendarIds)
            ->group_by('e.calendar_id')
            ->get()
            ->result_array();

        $result = array_column($eventCounts, 'count', 'calendar_id');

        return $result;
    }
}
