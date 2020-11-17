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

class CalendarQueryParameters
{
    /** @var string */
    private $orderBy;

    /** @var string */
    private $sort;

    /** @var int */
    private $offset;

    /** @var int */
    private $limit;

    /** @var int */
    private $calendarId;

    /** @var string */
    private $calendarShortName;

    /** @var int */
    private $siteId;

    /** @var mixed */
    private $siteName;

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
     * @param string $sort
     *
     * @return $this
     */
    public function setSort($sort)
    {
        $sort = strtolower($sort);
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
     * @return string
     */
    public function getCalendarShortName()
    {
        return $this->calendarShortName;
    }

    /**
     * @param string $calendarShortName
     *
     * @return $this
     */
    public function setCalendarShortName($calendarShortName)
    {
        $this->calendarShortName = $calendarShortName;

        return $this;
    }

    /**
     * @return string|int
     */
    public function getSiteId()
    {
        return $this->siteId;
    }

    /**
     * @param string|int $siteId
     *
     * @return $this
     */
    public function setSiteId($siteId)
    {
        $this->siteId = $siteId;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSiteName()
    {
        return $this->siteName;
    }

    /**
     * @param mixed $siteName
     *
     * @return $this
     */
    public function setSiteName($siteName)
    {
        $this->siteName = $siteName;

        return $this;
    }
}
