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

namespace Solspace\Addons\Calendar\Library\EventFormDataParser;

use Solspace\Addons\Calendar\Helpers\DateTimeHelper;
use Solspace\Addons\Calendar\Library\Carbon\Carbon;

class EventFormParser
{
    /**
     * Form Data parser takes an array of pre-validation form data and a
     * date format, and makes sure everything is ready for validation and
     * entry
     *
     * @param array  $formData Pre-validated form data
     * @param string $format   A date format (not a time format)
     */
    public function __construct(array $formData, $format = 'm/d/Y')
    {
        $this->formData   = $formData;
        $this->dateFormat = $format;
    }

    /**
     * Takes the form data in the constructor and preps it for validation
     * and saving
     *
     * @return array The prepped form data
     */
    public function parse()
    {
        /**
         * Event data
         */
        $this->formData['start_day'] = $this->convertDateToISO8601($this->formData['start_day']);

        if (!empty($this->formData['end_day'])) {
            $this->formData['end_day'] = $this->convertDateToISO8601($this->formData['end_day']);
        }

        if (!empty($this->formData['until'])) {
            $this->formData['until'] = $this->convertDateToISO8601($this->formData['until']);
        }

        if (isset($this->formData['all_day']) && (bool)$this->formData['all_day']) {
            $this->formData['start_time'] = "00:01";
            $this->formData['end_time']   = "23:59";
        }

        if (isset($this->formData['start_time'])) {
            $startTime = new Carbon($this->formData['start_time'], DateTimeHelper::TIMEZONE_UTC);

            $this->formData['start_date'] = $this->formData['start_day'] . " " . $startTime->toTimeString();
        }

        if (isset($this->formData['end_time']) && isset($this->formData['end_day'])) {
            $endTime = new Carbon($this->formData['end_time'], DateTimeHelper::TIMEZONE_UTC);

            $this->formData['end_date'] = $this->formData['end_day'] . " " . $endTime->toTimeString();
        }

        /**
         * Recurrence data
         */
        $repeats = isset($this->formData['repeats']) && (bool)$this->formData['repeats'];
        if ($repeats) {
            if ($this->formData['freq'] == "monthly") {
                $this->formData = $this->dropByMonthDayOrByDay($this->formData);
            }

            if ($this->formData['freq'] == "yearly") {
                $this->formData = $this->dropByDayInterval($this->formData);

                if (!isset($this->formData['yearly']['isbyday'])) {
                    $startDate = new Carbon($this->formData['start_date'], DateTimeHelper::TIMEZONE_UTC);

                    $this->formData['yearly']['bymonth'] = array(
                        $startDate->month,
                    );
                }
            }
        }

        return $this->formData;
    }

    /**
     * If it's a monthly recursion, we need to make sure that any data from
     * the option by month or by month day fields are dropped
     *
     * @param array $formData Form Data
     *
     * @return array The altered form data
     */
    private function dropByMonthDayOrByDay($formData)
    {
        $monthlyRecurrence = $formData["monthly"];

        if (isset($monthlyRecurrence['bymonthdayorbyday']) && $monthlyRecurrence["bymonthdayorbyday"] == "byday") {
            if (isset($monthlyRecurrence["bymonthday"])) {
                unset($monthlyRecurrence["bymonthday"]);
            }
        } else {
            if (isset($monthlyRecurrence["byday"])) {
                unset($monthlyRecurrence["byday"]);
            }
            unset($monthlyRecurrence["bydayinterval"]);
        }

        return $formData;
    }

    /**
     * If isbyday isn't checked, remove the data for a yearly recursion
     *
     * @param array $formData The form data
     *
     * @return array The altered form data
     */
    private function dropByDayInterval(array $formData)
    {
        if (!isset($formData["yearly"]["isbyday"])) {
            unset($formData["yearly"]["bydayinterval"]);

            if (isset($formData["yearly"]["byday"])) {
                unset($formData["yearly"]["byday"]);
            }
        }

        return $formData;
    }

    /**
     * We convert all dates to ISO 8601 format (YYYY-MM-DD) to get over the
     * intricacies of formats where October 8th, 2012 is valid as August 10th,
     * 2012 based on date formatting
     *
     * @param string $day The date to format
     *
     * @return string The formatted date
     */
    private function convertDateToISO8601($day)
    {
        $dateTime = Carbon::createFromFormat($this->dateFormat, $day, DateTimeHelper::TIMEZONE_UTC);

        return $dateTime->toDateString();
    }
}
