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

namespace Solspace\Addons\Calendar\Library\RRule\Frequency;

use ArrayAccess;
use Solspace\Addons\Calendar\Helpers\DateTimeHelper;
use Solspace\Addons\Calendar\Library\Carbon\Carbon;
use Solspace\Addons\Calendar\Library\RRule\RRule as RRule;

/**
 * The abstract class for Frequency, has a bunch of helper methods
 */
abstract class Frequency implements ArrayAccess
{
    protected $rrule;

    /**
     * Creates the Frequency object
     *
     * @param RRule $rrule An RRule
     */
    public function __construct(RRule $rrule)
    {
        $this->rrule = $rrule;
    }

    /**
     * Converts function arguments (2) to ";UNTIL=DATE" as example
     *
     * return Frequency
     */
    protected function setWithValue()
    {
        $properties = func_get_args();
        foreach ($properties as $property) {
            if (isset($this->rrule[$property]) &&
                !empty($this->rrule[$property])
            ) {
                $ucase = strtoupper($property);
                $value = strtoupper($this->rrule[$property]);
                $this->rrule->addRule(";$ucase=$value");
            }
        }

        return $this;
    }

    /**
     * Handles the various clauses for by day recursion, including byday fields
     * with intervals
     */
    protected function setByDay()
    {
        if (isset($this->rrule['byday'])) {
            $days          = array();
            $bydayinterval = (isset($this->rrule['bydayinterval'])) ?
                $this->rrule['bydayinterval'] : null;

            for ($i = 0; $i < count($this->rrule['byday']); $i++) {
                $days[$i] = $bydayinterval .
                    strtoupper($this->rrule['byday'][$i]);
            }

            $this->rrule->addRule(";BYDAY=" . implode(",", $days));
        }

        return $this;
    }

    /**
     * Takes an array and turns it into comma separated values
     *
     * @param string $property The key of the array in the rrule
     *
     * @return $this
     */
    protected function setWithArray($property)
    {
        if (isset($this->rrule[$property])) {
            // PHP sometimes returns strings
            if (is_string($this->rrule[$property])) {
                $this->rrule[$property] = array($this->rrule[$property]);
            }
            $ucase  = strtoupper($property);
            $values = implode(',', $this->rrule[$property]);
            $this->rrule->addRule(";$ucase=$values");
        }

        return $this;
    }

    /**
     * Returns the formated RRule string
     *
     * @return string The RRule
     */
    public function getRRule()
    {
        $this->setWithValue('freq', 'until', 'interval');

        return $this->rrule->getRRule();
    }

    /**
     * Returns the formatted RRule with UNTIL date re-parsed to fit the ICS rules
     *
     * @return string
     */
    public function getRRuleForIcs()
    {
        $rrule = $this->getRRule();

        preg_match('/UNTIL=([^;]+)/', $rrule, $matches);

        if (isset($matches[1])) {
            $date = new Carbon($matches[1], DateTimeHelper::TIMEZONE_UTC);

            $rrule = str_replace($matches[0], 'UNTIL=' . $date->format('Ymd\THis\Z'), $rrule);
        }

        return $rrule;
    }

    /**
     * Returns the RRule as an array of data
     *
     * @return array RRule data
     */
    public function getData()
    {
        return $this->rrule->getData();
    }

    /**
     * Will take an array of data to set as an RRule
     *
     * @param array $data The data to set
     */
    public function setData(Array $data)
    {
        $this->rrule->setData($data);
    }

    /**
     * Will set an RRule overriding the default
     *
     * @param string $rrule The RRule
     */
    public function setRRule($rrule)
    {
        $this->rrule->setRRule($rrule);
    }

    /**
     * Implements ArrayAccess
     */
    public function offsetExists($offset)
    {
        return isset($this->rrule[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->offsetExists($offset) ? $this->rrule[$offset] : null;
    }

    public function offsetSet($offset, $value)
    {
        $this->rrule[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        if ($this->offsetExists($offset)) {
            unset($this->rrule[$offset]);
        }
    }
}
