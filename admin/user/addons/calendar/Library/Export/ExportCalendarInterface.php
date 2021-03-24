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

namespace Solspace\Addons\Calendar\Library\Export;

use EllisLab\ExpressionEngine\Service\Model\Collection;
use Solspace\Addons\Calendar\Model\Event;

interface ExportCalendarInterface
{
    const DATE_TIME_FORMAT = 'Ymd\THis';
    const DATE_FORMAT      = 'Ymd';

    /**
     * ExportCalendarInterface constructor.
     *
     * Must pass an array of events that will be exported
     *
     * @param Collection|Event[] $events
     */
    public function __construct(Collection $events);

    /**
     * @return string
     */
    public function export();

    /**
     * @return
     */
    public function output();
}
