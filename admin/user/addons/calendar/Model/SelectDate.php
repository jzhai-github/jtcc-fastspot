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
use Solspace\Addons\Calendar\Library\Carbon\Carbon;

/**
 * @property int    $id
 * @property int    $event_id
 * @property string $date
 */
class SelectDate extends Model
{
    protected static $_primary_key = 'id';
    protected static $_table_name  = 'calendar_select_dates';

    protected $id;
    protected $event_id;
    protected $date;

    /**
     * Returns a formatted date, e.g. "Sunday, January 31, 2016"
     *
     * @return string
     */
    public function getDescriptiveDate()
    {
        $date = new Carbon($this->date, DateTimeHelper::TIMEZONE_UTC);

        return $date->format('l, F j, Y');
    }
}
