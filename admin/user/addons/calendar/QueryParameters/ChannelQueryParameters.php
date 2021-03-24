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

class ChannelQueryParameters
{
    /** @var mixed */
    private $id;

    /** @var mixed */
    private $name;

    /** @var mixed */
    private $siteId;

    /** @var mixed */
    private $siteName;

    /** @var mixed */
    private $authorId;

    /** @var mixed */
    private $status;

    /** @var mixed */
    private $category;

    /** @var bool */
    private $excludeUncategorizedEntries;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSiteId()
    {
        return $this->siteId;
    }

    /**
     * @param mixed $siteId
     *
     * @return $this
     */
    public function setSiteId($siteId)
    {
        $this->siteId = $siteId;

        return $this;
    }

    /**
     * @param mixed $siteName
     *
     * @return $this
     */
    public function setSiteName($siteName)
    {
        if (!$siteName) {
            return $this;
        }

        static $sites;

        if (null === $sites) {
            ee()->db->select('site_id, site_name');
            $query = ee()->db->get('sites');

            $sites = array();
            foreach ($query->result() as $row) {
                $sites[$row->site_name] = $row->site_id;
            }
        }

        $isAnd = strpos($siteName, '&') !== false;
        $isOr  = strpos($siteName, '|') !== false;

        $siteIdString = null;
        if ($isAnd) {
            $names = explode('&', $siteName);
            $siteIds = array();
            foreach ($names as $name) {
                if (isset($sites[$name])) {
                    $siteIds[] = $sites[$name];
                }
            }

            $siteIdString = implode('&', $siteIds);
        } else if ($isOr) {
            $names = explode('|', $siteName);
            $siteIds = array();
            foreach ($names as $name) {
                if (isset($sites[$name])) {
                    $siteIds[] = $sites[$name];
                }
            }

            $siteIdString = implode('|', $siteIds);
        } else {
            if (isset($sites[$siteName])) {
                $siteIdString = $sites[$siteName];
            }
        }

        $this->setSiteId($siteIdString);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAuthorId()
    {
        return $this->authorId;
    }

    /**
     * @param mixed $authorId
     *
     * @return $this
     */
    public function setAuthorId($authorId)
    {
        $this->authorId = $authorId;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     *
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param mixed $category
     *
     * @return $this
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return boolean
     */
    public function areUncategorizedEntriesExcluded()
    {
        return $this->excludeUncategorizedEntries;
    }

    /**
     * @return boolean
     */
    public function areUncategorizedEntriesIncluded()
    {
        return !$this->excludeUncategorizedEntries;
    }

    /**
     * @param boolean $uncategorizedEntries
     *
     * @return $this
     */
    public function setUncategorizedEntriesExcluded($uncategorizedEntries)
    {
        $this->excludeUncategorizedEntries = in_array($uncategorizedEntries, array("no", "n", "false"), true);

        return $this;
    }
}
