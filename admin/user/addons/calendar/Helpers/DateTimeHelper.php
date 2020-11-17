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

namespace Solspace\Addons\Calendar\Helpers;

use Solspace\Addons\Calendar\Library\Carbon\Carbon;
use Solspace\Addons\Calendar\Library\Schedule\Type\Week;

/**
 * If lang doesn't exist, let's make it null
 */
if (!function_exists("lang")) {
    function lang($string)
    {
        return $string;
    }
}

/**
 * Handles the i18n requirements for months and times
 */
class DateTimeHelper
{
    const TIMEZONE_UTC = 'UTC';

    private static $eeTimezones = array(
        'UM12'      => '-12:00',
        'UM11'      => '-11:00',
        'UM10'      => '-10:00',
        'UM95'      => '-9:30',
        'UM9'       => '-9:00',
        'UM8'       => '-8:00',
        'UM7'       => '-7:00',
        'UM6'       => '-6:00',
        'UM5'       => '-5:00',
        'UM45'      => '-4:30',
        'UM4'       => '-4:00',
        'UM35'      => '-3:30',
        'UM3'       => '-3:00',
        'UM2'       => '-2:00',
        'UM1'       => '-1:00',
        'UTC'       => '+0:00',
        'UP1'       => '+1:00',
        'UP2'       => '+2:00',
        'UP3'       => '+3:00',
        'UP35'      => '+3:30',
        'UP4'       => '+4:00',
        'UP45'      => '+4:30',
        'UP5'       => '+5:00',
        'UP55'      => '+5:30',
        'UP575'     => '+5:45',
        'UP6'       => '+6:00',
        'UP65'      => '+6:30',
        'UP7'       => '+7:00',
        'UP8'       => '+8:00',
        'UP875'     => '+8:45',
        'UP9'       => '+9:00',
        'UP95'      => '+9:30',
        'UP10'      => '+10:00',
        'UP105'     => '+10:30',
        'UP11'      => '+11:00',
        'UP115'     => '+11:30',
        'UP12'      => '+12:00',
        'UP1275'    => '+12:45',
        'UP13'      => '+13:00',
        'UP14'      => '+14:00',
    );

    /** @var string */
    private static $userTimezone;

    /** @var int */
    private static $timezoneDiffInSeconds;

    /**
     * Returns full string translated month: August, September, etc.
     *
     * @return array Month strings
     */
    public static function getTranslatedMonths()
    {
        return array(
            1  => lang('calendar_first_month'),
            2  => lang('calendar_second_month'),
            3  => lang('calendar_third_month'),
            4  => lang('calendar_fourth_month'),
            5  => lang('calendar_fifth_month'),
            6  => lang('calendar_sixth_month'),
            7  => lang('calendar_seventh_month'),
            8  => lang('calendar_eighth_month'),
            9  => lang('calendar_ninth_month'),
            10 => lang('calendar_tenth_month'),
            11 => lang('calendar_eleventh_month'),
            12 => lang('calendar_twelfth_month'),
        );
    }

    /**
     * @param $timezone
     *
     * @return mixed
     */
    public static function transformTimezone($timezone)
    {
        if (preg_match('/^U(M|T|P)\d+$/', $timezone) && array_key_exists(strtoupper($timezone), self::$eeTimezones)) {
            return self::$eeTimezones[strtoupper($timezone)];
        }

        return $timezone;
    }

    /**
     * Returns full string translated days: Monday, Tuesday; adjusts them
     * for the starting day of the week (ex: week starts on Sunday)
     *
     * @param int $startDay The day that starts the week
     *
     * @return array Day strings
     */
    public static function getTranslatedDays($startDay = Carbon::SUNDAY)
    {
        $days = array(
            lang('calendar_day_seven'),
            lang('calendar_day_one'),
            lang('calendar_day_two'),
            lang('calendar_day_three'),
            lang('calendar_day_four'),
            lang('calendar_day_five'),
            lang('calendar_day_six'),
        );

        return static::moveDaysAround($days, $startDay);
    }

    /**
     * Functions the same as getTranslatedDays but returns the abbreviated
     * versions of the strings: Mo, Tu, We
     *
     * @param int $startDay The day that starts the week
     *
     * @return array Abbreviated day strings
     */
    public static function getTranslatedDaysAbbr($startDay = Carbon::SUNDAY)
    {
        $days = array(
            "SU" => lang('calendar_day_seven_abbr'),
            "MO" => lang('calendar_day_one_abbr'),
            "TU" => lang('calendar_day_two_abbr'),
            "WE" => lang('calendar_day_three_abbr'),
            "TH" => lang('calendar_day_four_abbr'),
            "FR" => lang('calendar_day_five_abbr'),
            "SA" => lang('calendar_day_six_abbr'),
        );

        return static::moveDaysAround($days, $startDay);
    }

    /**
     * @param int $startDay
     *
     * @return array
     */
    public static function getDays($startDay = Carbon::SUNDAY)
    {
        $days = array(
            Carbon::SUNDAY    => "Sunday",
            Carbon::MONDAY    => "Monday",
            Carbon::TUESDAY   => "Tuesday",
            Carbon::WEDNESDAY => "Wednesday",
            Carbon::THURSDAY  => "Thursday",
            Carbon::FRIDAY    => "Friday",
            Carbon::SATURDAY  => "Saturday",
        );

        return static::moveDaysAround($days, $startDay);
    }

    /**
     * Functions the same as getTranslatedDaysAbbr but returns the abbreviated
     * versions of the strings: M, T, W
     *
     * @param int $startDay The day that starts the week
     *
     * @return array Abbreviated day strings
     */
    public static function getTranslatedDaysAbbr1($startDay = Carbon::SUNDAY)
    {
        $days = array(
            "SU" => lang('calendar_day_seven_abbr_1'),
            "MO" => lang('calendar_day_one_abbr_1'),
            "TU" => lang('calendar_day_two_abbr_1'),
            "WE" => lang('calendar_day_three_abbr_1'),
            "TH" => lang('calendar_day_four_abbr_1'),
            "FR" => lang('calendar_day_five_abbr_1'),
            "SA" => lang('calendar_day_six_abbr_1'),
        );

        return static::moveDaysAround($days, $startDay);
    }

    /**
     * Functions the same as getTranslatedDaysAbbr but returns the abbreviated
     * versions of the strings: Mon, Tue, Wed
     *
     * @param int $startDay The day that starts the week
     *
     * @return array Abbreviated day strings
     */
    public static function getTranslatedDaysAbbr3($startDay = Carbon::SUNDAY)
    {
        $days = array(
            "SU" => lang('calendar_day_seven_abbr_3'),
            "MO" => lang('calendar_day_one_abbr_3'),
            "TU" => lang('calendar_day_two_abbr_3'),
            "WE" => lang('calendar_day_three_abbr_3'),
            "TH" => lang('calendar_day_four_abbr_3'),
            "FR" => lang('calendar_day_five_abbr_3'),
            "SA" => lang('calendar_day_six_abbr_3'),
        );

        return static::moveDaysAround($days, $startDay);
    }

    /**
     * Gets all translations for all kinds of day abbreviations
     * Used for {exp:calendar:month} and {exp:calendar:week} template tags
     *
     * @param int    $startDay
     * @param Carbon $relativeDate - Will generate week day timestamps starting with this date
     *
     * @return array
     */
    public static function getDayLetterTemplateTags($startDay, Carbon $relativeDate = null)
    {
        $oneLetter   = array_values(self::getTranslatedDaysAbbr1($startDay));
        $twoLetter   = array_values(self::getTranslatedDaysAbbr($startDay));
        $threeLetter = array_values(self::getTranslatedDaysAbbr3($startDay));
        $full        = array_values(self::getTranslatedDays($startDay));

        if (!$relativeDate) {
            $relativeDate = new Carbon(DateTimeHelper::TIMEZONE_UTC);
        }

        /** @var Carbon $startDate */
        list($startDate, $endDate) = Week::getSectionByTime($startDay, $relativeDate);

        $relative = array();
        foreach (range(0, 6) as $dayNumber) {
            $relative[$dayNumber] = $startDate->format('U:e');
            $startDate->addDay();
        }

        $relative = array_values(self::moveDaysAround($relative, $startDay));

        $returnArray = array();
        for ($i = 0; $i < count($oneLetter); $i++) {
            $returnArray[] = array(
                'day_of_week_1_letter' => $oneLetter[$i],
                'day_of_week_2_letter' => $twoLetter[$i],
                'day_of_week_3_letter' => $threeLetter[$i],
                'day_of_week_full'     => $full[$i],
                'day_of_week'          => $relative[$i],
            );
        }

        return $returnArray;
    }

    /**
     * Gets the hours of the day based on the time format
     *
     * @param  string $timeFormat The php time format
     *
     * @return array Hour strings
     */
    public static function getHours($timeFormat = "g:i a")
    {
        $time         = new Carbon;
        $time->hour   = 0;
        $time->minute = 0;
        $hours        = array();
        for ($i = 0; $i < 24; $i++) {
            $hours[$i] = $time->format($timeFormat);
            $time->addHour();
        }
        unset($time);

        return $hours;
    }

    /**
     * Takes an array and start date (Carbon constants preferred) and moves the
     * array around based on the number. Note that it is not the INDEX it is
     * a counter
     *
     * @param  array $days
     * @param  int   $startDay
     *
     * @return array The sorted days
     */
    public static function moveDaysAround(array $days, $startDay)
    {
        $arrayKeys = array_keys($days);

        for ($i = 0; $i < $startDay; $i++) {
            $key   = array_shift($arrayKeys);
            $value = array_shift($days);

            if (is_numeric($key)) {
                $days[] = $value;
            } else {
                $days[$key] = $value;
            }
        }

        return $days;
    }

    /**
     * Converts a PHP date format into a jquery.datepicker valid format.
     * E.g. - converts "d-m-Y" into "dd-mm-yy", etc.
     *
     * @param string $format
     *
     * @return string
     */
    public static function convertFormatToJSDateFormat($format)
    {
        $replacementTable = array(
            'Y' => 'yy',
            'd' => 'dd',
            'm' => 'mm',
        );

        return str_replace(array_keys($replacementTable), $replacementTable, $format);
    }

    /**
     * Converts a PHP date format into a human readable, familiar format.
     * E.g. - converts "d-m-Y" into "dd-mm-yyyy"
     *      - "g:i a" to "hh:mm tt", etc.
     *
     * @param string $format
     *
     * @return string
     */
    public static function convertFormatToHumanReadable($format)
    {
        $replacementTable = array(
            'Y' => 'yyyy',
            'd' => 'dd',
            'm' => 'mm',
            'H' => 'HH',
            'g' => 'hh',
            'i' => 'mm',
            'a' => 'tt',
        );

        return str_replace(array_keys($replacementTable), $replacementTable, $format);
    }

    /**
     * Converts all EE's date format items into PHP date items
     *
     * @param string $format
     * @param bool   $forceLeadingZeroes - if this is true - will convert non-leading-zero items to ones
     *                                   with leading zeroes
     *
     * @return string
     */
    public static function convertEEFormatToPHPFormat($format, $forceLeadingZeroes = false)
    {
        $convertedItems = preg_replace('/%([a-zA-Z])/', '$1', $format);

        if ($forceLeadingZeroes) {
            $replacementTable = array(
                'j' => 'd',
                'n' => 'm',
                'g' => 'h',
                'G' => 'H',
            );

            $convertedItems = str_replace(array_keys($replacementTable), $replacementTable, $convertedItems);
        }

        return $convertedItems;
    }

    /**
     * Various magic replacements take place:
     *   In a Y-m-d date format words "year", "month", "day" are replaced with the current date values
     *   And the word "last" is replaced with the last day of current month
     *
     * Time formats also get some magic:
     *   The keywords "noon", "midnight" get replaced with "12:00" and "00:00" (same day) respectively
     *   "0800" gets replaced with "08:00"
     *   "8 pm" gets replaced with "20:00"
     *   "now" when used in the time format is replaced with the H:i based on $relativeDate
     *
     * @param string $dateString
     * @param Carbon $relativeDate - Will use this date as a replacement point for placeholders
     * @param string $timezone
     *
     * @return Carbon
     */
    public static function parseDateForCustomValues(
        $dateString,
        Carbon $relativeDate = null,
        $timezone = self::TIMEZONE_UTC
    ) {
        $dateMatcherPattern = "/(year|\d{4})-(month|\d{1,2})-(day|last|\d{1,2})/";
        $currentDate        = $relativeDate ?: Carbon::now($timezone);

        $setLastDay   = false;
        $dateIsStatic = preg_match($dateMatcherPattern, $dateString);
        if ($dateIsStatic) {
            $replacementMap = array(
                'year'  => $currentDate->year,
                'month' => $currentDate->month,
                'day'   => $currentDate->day,
            );

            $dateString = str_replace(array_keys($replacementMap), $replacementMap, $dateString);

            if (strpos($dateString, '-last') !== false) {
                $dateString = str_replace('-last', '-01', $dateString);
                $setLastDay = true;
            }
        }

        // If we find a " now" occurrence, we replace it with the current hours and minutes
        if (strpos($dateString, ' now') !== false) {
            $dateString = str_replace(' now', $currentDate->format(' H:i:s'), $dateString);
        }

        if (strpos($dateString, 'midnight') !== false) {
            $dateString = str_replace('midnight', '23:59:59', $dateString);
        }

        if ($relativeDate && !$dateIsStatic) {
            $recreationalDate = $relativeDate->copy();

            // If a relative date is provided
            // We update the recreational date to match the diff in seconds
            // That the parsed date string and today have in diff
            $today               = Carbon::now($timezone);
            try {
                $recreatedDateString = new Carbon($dateString, $timezone);
            } catch (\Exception $exception) {
                $recreatedDateString = new Carbon($timezone);
            }

            // If the recreated date string doesn't have time specified
            // we reset the $today time values to zero
            if ($recreatedDateString->format('His') === '000000') {
                $today->setTime(0, 0, 0);
            }

            $diff = $today->diff($recreatedDateString);

            $recreationalDate = $recreationalDate->add($diff);
        } else {
            try {
                $recreationalDate = new Carbon($dateString, $timezone);
            } catch (\Exception $exception) {
                $recreationalDate = new Carbon($timezone);
            }

            if ($setLastDay) {
                $recreationalDate->day = $recreationalDate->daysInMonth;
            }
        }

        return $recreationalDate;
    }

    /**
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @param bool   $isAllDay
     *
     * @return array [days, hours, minutes]
     */
    public static function getDuration(Carbon $startDate, Carbon $endDate, $isAllDay = false)
    {
        $days        = $startDate->diffInDays($endDate, true, false);
        $daysAsHours = 24 * $days;

        $hours          = $startDate->diffInHours($endDate) - $daysAsHours;
        $hoursAsMinutes = 60 * ($hours + $daysAsHours);

        $minutes = $startDate->diffInMinutes($endDate) - $hoursAsMinutes;

        if ($isAllDay) {
            $days += 1;
            $hours   = 0;
            $minutes = 0;
        }

        return array($days, $hours, $minutes);
    }

    /**
     * Attempts to get the users timezone
     * If not present - gets one from site settings
     * Else - returns UTC timezone
     *
     * @return string
     */
    public static function getUserTimezone()
    {
        if (is_null(self::$userTimezone)) {
            $timezone = ee()->session->userdata('timezone');
            if (!$timezone) {
                $timezone = ee()->config->item('default_site_timezone');
            }

            $timezone = self::transformTimezone($timezone);

            if (@timezone_open($timezone) === false) {
                $timezone = DateTimeHelper::TIMEZONE_UTC;
            }

            self::$userTimezone = $timezone ?: DateTimeHelper::TIMEZONE_UTC;
        }

        return self::$userTimezone;
    }

    /**
     * @return int
     */
    public static function getServerTimezoneDiffInSeconds()
    {
        if (is_null(self::$timezoneDiffInSeconds)) {
            $userDate = Carbon::now(self::getUserTimezone());
            $morphed  = self::morphUserDateToUtc($userDate);

            self::$timezoneDiffInSeconds = $userDate->diffInSeconds($morphed, false);
        }

        return self::$timezoneDiffInSeconds;
    }

    /**
     * @param Carbon $date
     *
     * @return Carbon
     */
    public static function morphUserDateToUtc(Carbon $date)
    {
        $clone = Carbon::createFromDate($date->year, $date->month, $date->day, self::TIMEZONE_UTC);
        $clone->setTime($date->hour, $date->minute, $date->second);

        return $clone;
    }
}
