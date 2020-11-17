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

namespace Solspace\Addons\Calendar\QueryParameters;

use Solspace\Addons\Calendar\Library\Carbon\Carbon;

class EventQueryParameters
{
    /** @var string */
    private $orderBy;

    /** @var string */
    private $sort = 'asc';

    /** @var int */
    private $offset;

    /** @var int */
    private $limit;

    /** @var string */
    private $icsHash;

    /** @var int */
    private $calendarId;

    /** @var string */
    private $calendarName;

    /** @var Carbon */
    private $dateRangeStart;

    /** @var Carbon */
    private $dateRangeEnd;

    /** @var array */
    private $entryIds;

    /** @var int */
    private $eventId;

    /** @var string */
    private $urlTitle;

    /** @var ChannelQueryParameters */
    private $channelQueryParameters;

    /** @var bool */
    private $loadResourceConsumingData;

    /**
     * EventQueryParameters constructor.
     */
    public function __construct()
    {
        $this->loadResourceConsumingData = false;
    }

    /**
     * @return string
     */
    public function getOrderBy()
    {
        return $this->orderBy;
    }

    /**
     * @param string $orderBy
     *
     * @return $this
     */
    public function setOrderBy($orderBy)
    {
        $this->orderBy = $orderBy;

        return $this;
    }

    /**
     * @return string - ASC or DESC
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * @return bool
     */
    public function isSortAscending()
    {
        return $this->sort === "asc";
    }

    /**
     * @return bool
     */
    public function isSortDescending()
    {
        return $this->sort === "desc";
    }

    /**
     * @param string $sort
     *
     * @return $this
     */
    public function setSort($sort)
    {
        $sort       = strtolower($sort);
        $this->sort = $sort == 'asc' ? 'asc' : 'desc';

        return $this;
    }

    /**
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @param int $offset
     *
     * @return $this
     */
    public function setOffset($offset)
    {
        $this->offset = (int)$offset;

        return $this;
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @param int $limit
     *
     * @return $this
     */
    public function setLimit($limit)
    {
        $this->limit = (int)$limit;

        return $this;
    }

    /**
     * @return string
     */
    public function getIcsHash()
    {
        return $this->icsHash;
    }

    /**
     * @param string $icsHash
     *
     * @return $this
     */
    public function setIcsHash($icsHash)
    {
        $this->icsHash = $icsHash;

        return $this;
    }

    /**
     * @return string|int
     */
    public function getCalendarId()
    {
        return $this->calendarId;
    }

    /**
     * @param string|int $calendarId
     *
     * @return $this
     */
    public function setCalendarId($calendarId)
    {
        $this->calendarId = $calendarId;

        return $this;
    }

    /**
     * @return int
     */
    public function getEventId()
    {
        return $this->eventId;
    }

    /**
     * @param int $eventId
     *
     * @return $this
     */
    public function setEventId($eventId)
    {
        $this->eventId = $eventId;

        return $this;
    }

    /**
     * @return string
     */
    public function getCalendarName()
    {
        return $this->calendarName;
    }

    /**
     * @param string $calendarName
     *
     * @return $this
     */
    public function setCalendarName($calendarName)
    {
        $this->calendarName = $calendarName;

        return $this;
    }

    /**
     * @return Carbon
     */
    public function getDateRangeStart()
    {
        return $this->dateRangeStart;
    }

    /**
     * @param Carbon $dateRangeStart
     *
     * @return $this
     */
    public function setDateRangeStart(Carbon $dateRangeStart = null)
    {
        if (is_null($dateRangeStart)) {
            return $this;
        }

        $this->dateRangeStart = clone $dateRangeStart;
        $this->dateRangeStart->setTime(0, 0, 0);

        return $this;
    }

    /**
     * @return Carbon
     */
    public function getDateRangeEnd()
    {
        return $this->dateRangeEnd;
    }

    /**
     * @param Carbon $dateRangeEnd
     *
     * @return $this
     */
    public function setDateRangeEnd(Carbon $dateRangeEnd = null)
    {
        if (is_null($dateRangeEnd)) {
            return $this;
        }

        $this->dateRangeEnd = clone $dateRangeEnd;
        $this->dateRangeEnd->setTime(23, 59, 59);

        return $this;
    }

    /**
     * @return array
     */
    public function getEntryIds()
    {
        return $this->entryIds;
    }

    /**
     * @param string|int $entryIds
     *
     * @return $this
     */
    public function setEntryIds($entryIds)
    {
        $this->entryIds = $entryIds;

        return $this;
    }

    /**
     * @return string
     */
    public function getUrlTitle()
    {
        return $this->urlTitle;
    }

    /**
     * @param string $urlTitle
     *
     * @return $this
     */
    public function setUrlTitle($urlTitle)
    {
        $this->urlTitle = $urlTitle;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isLoadResourceConsumingData()
    {
        return $this->loadResourceConsumingData;
    }

    /**
     * @param boolean $loadResourceConsumingData
     *
     * @return $this
     */
    public function setLoadResourceConsumingData($loadResourceConsumingData)
    {
        $this->loadResourceConsumingData = (bool)$loadResourceConsumingData;

        return $this;
    }

    /**
     * @return ChannelQueryParameters
     */
    public function getChannelQueryParameters()
    {
        return $this->channelQueryParameters;
    }

    /**
     * @param ChannelQueryParameters $channelQueryParameters
     *
     * @return $this
     */
    public function setChannelQueryParameters($channelQueryParameters)
    {
        $this->channelQueryParameters = $channelQueryParameters;

        return $this;
    }

    /**
     * Get a unique SHA1 hash based on the values of each property in this object
     */
    public function getHash()
    {
        $valueDump = print_r($this, true);
        $hash      = sha1($valueDump);

        return $hash;
    }
}
