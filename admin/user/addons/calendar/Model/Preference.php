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

namespace Solspace\Addons\Calendar\Model;

use EllisLab\ExpressionEngine\Service\Model\Model;
use Solspace\Addons\Calendar\Exceptions\CalendarException;
use Solspace\Addons\Calendar\Helpers\DateTimeHelper;

/**
 * @property int    $id
 * @property int    $site_id
 * @property string $date_format
 * @property string $time_format
 * @property int    $start_day
 * @property int    $overlap_threshold
 * @property int    $time_interval
 * @property bool   $all_day
 * @property int    $duration
 *
 * Preference model
 */
class Preference extends Model
{
    const DEFAULT_DATE_FORMAT            = 'm/d/Y';
    const DEFAULT_TIME_FORMAT            = 'H:i';
    const DEFAULT_DATE_OVERLAP_THRESHOLD = 5;
    const DEFAULT_START_DAY              = 0;
    const DEFAULT_TIME_INTERVAL          = 30;
    const DEFAULT_DURATION               = 60;

    const TIME_FORMAT_24 = 'H:i';
    const TIME_FORMAT_12 = 'g:i a';

    private static $dateFormats = array(
        'm/d/Y' => 'mm/dd/yyyy',
        'd/m/Y' => 'dd/mm/yyyy',
        'd-m-Y' => 'dd-mm-yyyy',
        'Y-m-d' => 'yyyy-mm-dd',
    );

    private static $timeFormats = array(
        self::TIME_FORMAT_24 => '24_hour', // HH:mm
        self::TIME_FORMAT_12 => '12_hour', // hh:mm tt
    );

    protected static $_primary_key = 'id';
    protected static $_table_name  = 'calendar_preferences';

    protected $id;
    protected $site_id;
    protected $date_format;
    protected $time_format;
    protected $start_day;
    protected $overlap_threshold;
    protected $time_interval;
    protected $all_day;
    protected $duration;

    /**
     * Creates a preference model and returns it without saving
     *
     * @param int $siteId
     *
     * @return Preference
     */
    public static function create($siteId)
    {
        $confDateFormat = ee()->config->item('date_format');
        $confTimeFormat = ee()->config->item('time_format');

        $dateFormat = DateTimeHelper::convertEEFormatToPHPFormat($confDateFormat, true);
        $timeFormat = $confTimeFormat == "24" ? Preference::TIME_FORMAT_24 : Preference::TIME_FORMAT_12;


        /** @var Preference $preference */
        $preference = ee('Model')->make(
            'calendar:Preference',
            array(
                'site_id'           => $siteId,
                'date_format'       => $dateFormat,
                'time_format'       => $timeFormat,
                'overlap_threshold' => Preference::DEFAULT_DATE_OVERLAP_THRESHOLD,
                'start_day'         => Preference::DEFAULT_START_DAY,
                'time_interval'     => Preference::DEFAULT_TIME_INTERVAL,
                'duration'          => Preference::DEFAULT_DURATION,
            )
        );

        return $preference;
    }

    /**
     * @return array
     */
    public static function getDateFormats()
    {
        return self::$dateFormats;
    }

    /**
     * @return array
     */
    public static function getTimeFormats()
    {
        return self::$timeFormats;
    }

    /**
     * @return array of [key => translation, ..]
     */
    public static function getStartingDayTranslations()
    {
        return array(
            0 => lang('calendar_day_seven'),
            1 => lang('calendar_day_one'),
            2 => lang('calendar_day_two'),
            3 => lang('calendar_day_three'),
            4 => lang('calendar_day_four'),
            5 => lang('calendar_day_five'),
            6 => lang('calendar_day_six'),
        );
    }

    /**
     * Returns the time format key=>value pairs, where value is using a translation
     *
     * @return array
     */
    public static function getTranslatedTimeFormats()
    {
        $timeFormats = self::$timeFormats;

        $timeFormats = array_map(
            function ($value) {
                return lang('calendar_' . $value);
            },
            $timeFormats
        );

        return $timeFormats;
    }

    /**
     * Converts a PHP date format into a jquery.datepicker valid format.
     * E.g. - converts "d-m-Y" into "dd-mm-yy", etc.
     *
     * @return string
     */
    public function getDatePickerFormat()
    {
        return DateTimeHelper::convertFormatToJSDateFormat($this->date_format);
    }

    /**
     * Converts a PHP date format into a human readable, familiar format.
     * E.g. - converts "d-m-Y" into "dd-mm-yyyy", etc.
     *
     * @return string
     */
    public function getHumanReadableDateFormat()
    {
        return DateTimeHelper::convertFormatToHumanReadable($this->date_format);
    }

    /**
     * Converts a PHP time format into a human readable, familiar format.
     * E.g. - converts "g:i a" to "hh:mm tt", etc
     *
     * @return string
     */
    public function getHumanReadableTimeFormat()
    {
        return DateTimeHelper::convertFormatToHumanReadable($this->time_format);
    }

    /**
     * Gets a translation string for the currently selected starting day
     *
     * @return string
     * @throws CalendarException
     */
    public function getStartDayTranslation()
    {
        $startDayTranslations = self::getStartingDayTranslations();

        if (isset($startDayTranslations[$this->start_day])) {
            return $startDayTranslations[$this->start_day];
        }

        throw new CalendarException("Could not find the starting day");
    }
}
