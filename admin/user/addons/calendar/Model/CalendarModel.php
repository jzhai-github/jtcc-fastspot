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
use Solspace\Addons\Calendar\Helpers\DateTimeHelper;
use Solspace\Addons\Calendar\Repositories\PreferenceRepository;

/**
 * Calendar model
 *
 * @property int    $id
 * @property int    $site_id
 * @property string $name
 * @property string $url_title
 * @property string $color
 * @property string $description
 * @property string $ics_hash
 * @property bool   $default
 */
class CalendarModel extends Model
{
    const COLOR_LIGHTNESS = 0.3;

    protected static $_primary_key = 'id';
    protected static $_table_name  = 'calendar_calendars';

    protected $id;
    protected $site_id;
    protected $name;
    protected $url_title;
    protected $color;
    protected $description;
    protected $default;
    protected $ics_hash;

    /**
     * Creates a fresh Calendar instance with default settings
     *
     * @param int $siteId
     *
     * @return CalendarModel
     */
    public static function create($siteId)
    {
        /** @var CalendarModel $calendar */
        $calendar = ee('Model')->make(
            'calendar:Calendar',
            array(
                'default'           => 0,
                'site_id'           => $siteId,
            )
        );

        return $calendar;
    }

    /**
     * Gets a human readable date and time format combination as string
     *
     * @return string
     * @throws \Solspace\Addons\Calendar\Exceptions\FormatNotFoundException
     */
    public function getHumanReadableDateTimeFormat()
    {
        $preference = $this->getPreference();
        $format = sprintf('%s %s', $preference->date_format, $preference->time_format);

        return DateTimeHelper::convertFormatToHumanReadable($format);
    }

    /**
     * Converts a PHP date format into a jquery.datepicker valid format.
     * E.g. - converts "d-m-Y" into "dd-mm-yy", etc.
     *
     * @return string
     */
    public function getDatePickerFormat()
    {
        $preference = $this->getPreference();
        return DateTimeHelper::convertFormatToJSDateFormat($preference->date_format);
    }


    /**
     * Returns the name of this calendar if toString() is invoked
     *
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function regenerateIcsHash()
    {
        $hash = uniqid(sha1($this->id), true);

        $this->set(array('ics_hash' => $hash));

        return $hash;
    }

    /**
     * @return Preference
     */
    private function getPreference()
    {
        return PreferenceRepository::getInstance()->getOrCreate();
    }
}
