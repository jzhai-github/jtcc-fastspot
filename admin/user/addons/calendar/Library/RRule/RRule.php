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

namespace Solspace\Addons\Calendar\Library\RRule;

/**
 * RRule Class
 *
 * The RRule class is for converting arrays of data into RRULe that meets
 * the iCalendar spec (http://www.ietf.org/rfc/rfc2445.txt).
 *
 * For this to work, the respective array passed to the RRule must have its name
 * equal to the rrule property (ex: a form input named 'freq' for the frequency
 * property).
 *
 * The RRule class will also take an RRule and return it as an array of property
 * value.
 */
use ArrayAccess;
use Solspace\Addons\Calendar\Library\RRule\Frequency\Frequency;
use UnexpectedValueException;

class RRule implements ArrayAccess
{
	const FREQUENCY_NAMESPACE = 'Solspace\Addons\Calendar\Library\RRule\Frequency\\';
	private $data;
	private $rule;

	/**
	 * Will create an RRule from an array of frequency data
	 * @param  array $data The raw RRule in key:value params
	 * @return RRule The RRule object
	 */
	public static function create(array $data)
	{
		$className = static::FREQUENCY_NAMESPACE
			. ucwords(strtolower($data['freq']));
		if (class_exists($className)) {
			$rrule = new $className(new RRule);
			$rrule->setData($data);
			return $rrule;
		} else {
			throw new \UnexpectedValueException(
				$className . " is not a recurrence frequency"
			);
		}
	}

	/**
	 * Creates a Frequency object from an RRule string
	 * @param  string $rrule An RRule string
	 * @return Frequency The key:value data from an RRule
	 */
	public static function createFromRRule($rrule)
	{
		$rrulian = new RRule();
		$data = static::parseRRuleForData($rrule);
		$rrulian->setData($data);
		$class = static::FREQUENCY_NAMESPACE .
			ucwords(strtolower($rrulian['freq']));
		$frequency = new $class($rrulian);
		return $frequency;
	}

	/**
	 * Takes an RRule string and creates a key:value array
	 * @param  string $rrule The RRule string
	 * @return array The RRule broken down into an array
	 */
	public static function parseRRuleForData($rrule)
	{
		$rules = explode(';', $rrule);
		$data = array();

		foreach ($rules as $rule) {
			list($name, $value) = explode('=', $rule);

			if (strpos($value, ',')) {
				$value = explode(",", $value);
			}

			// Makes sure these particular rules are integers
			// so we can do strict type checking
			if ($name === "BYMONTH" || $name === "BYMONTHDAY") {
				if (!is_array($value)) {
					$value = array($value);
				}

				for ($i=0; $i < count($value); $i++) {
					$value[$i] = is_numeric($value[$i]) ?
						(int) $value[$i] : $value[$i];
				}
			}

			$data[strtolower($name)] = is_string($value) ?
				strtolower($value) : $value;
		}

		$data = RRule::handleByDayInterval($data);

		return $data;
	}

	/**
	 * Gets the RRule string
	 * @return string An RRule string
	 */
	public function getRRule()
	{
		return $this->removeFrontSemicolon($this->rule);
	}

	/**
	 * Sometimes a semicolon will appear because of how this all works
	 * this hacky fix, removes it
	 * @return string The rrule
	 */
	public function removeFrontSemicolon()
	{
		if (strpos($this->rule, ';') === 0) {
			$this->rule = substr($this->rule, 1);
		}

		return $this->rule;
	}

	/**
	 * Returns the RRule array, not the string
	 * @return array The RRule in key value pairs
	 */
	public function getData()
	{
		if (!isset($this->data) && isset($this->rule)) {
			$this->setData(static::parseRRuleForData($this->rule));
		}

		return $this->data;
	}

	/**
	 * Will take an array of RRule data and change the configuration of the
	 * RRule
	 * @param Array $data Data to change the RRule configuration
	 */
	public function setData(Array $data)
	{
		$parsedData = array();

		if (isset($data[$data['freq']])) {
			$data = $data + $data[$data['freq']];
			unset($data[$data['freq']]);
		}

		foreach ($data as $key => $value) {
			$parsedData[strtolower($key)] = $value;
		}

		$this->data = $parsedData;
	}

	/**
	 * Adds a rule to the rule string
	 * @param string $rule The rule to add in RRule format (UNTIL=DERP;)
	 */
	public function addRule($rule)
	{
		$this->rule .= $rule;
	}

	/**
	 * Will remove an RRule rule from the RRule string
	 * @param  string $name The RRule to remove in RRule format (UNTIL=DERP)
	 */
	public function removeRule($name)
	{
		$this->rule = str_replace($name, "", $this->rule);
	}

	/**
	 * ByDayInterval is usually attached to byday when it's in the RRule string.
	 * This parses it out
	 * @param  array $data The RRule data
	 * @return array The RRule data
	 */
	public static function handleByDayInterval($data)
	{
		// Unfortunately we have to do a unique instance check here
		if (isset($data['byday'])) {
			if (!is_array($data['byday'])) {
				// php variable handling, handled
				$data['byday'] = array(strtoupper($data['byday']));
			}

			foreach ($data['byday'] as $idx => $day) {

				if ($bydayinterval =
					filter_var($day, FILTER_SANITIZE_NUMBER_INT)) {
					$data['bydayinterval'] = (int) $bydayinterval;
				}
				$data['byday'][$idx] = preg_replace("/-?[0-9]/", "", $day);
			}
		}

		return $data;
	}

	/**
	 * An RRule allows for ArrayAccess so you can go $rrule['until]
	 */
	public function offsetExists($offset)
	{
		return isset($this->data[$offset]);
	}

	public function offsetGet($offset)
	{
		return $this->offsetExists($offset) ? $this->data[$offset] : null;
	}

	public function offsetSet($offset, $value)
	{
		$this->data[$offset] = $value;
	}

	public function offsetUnset($offset)
	{
		if ($this->offsetExists($offset)) {
			unset($this->data[$offset]);
		}
	}
}
