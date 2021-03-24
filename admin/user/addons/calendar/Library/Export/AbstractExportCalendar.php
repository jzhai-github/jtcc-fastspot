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

abstract class AbstractExportCalendar implements ExportCalendarInterface
{

    /** @var Collection|Event[] */
    private $events;

    /**
     * @param Collection|Event[] $events
     */
    final public function __construct(Collection $events)
    {
        $this->events = $events;
    }

    /**
     * Collects the exportable string and outputs it
     * Sets headers to file download and content-type to text/calendar
     *
     * @return string
     */
    final public function export()
    {
        header('Content-type: text/calendar; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . time() . '.ics');

        $exportString = $this->prepareStringForExport();

        echo $exportString;
        exit();
    }

    /**
     * @return string
     */
    final public function output()
    {
        return $this->prepareStringForExport();
    }

    /**
     * Collect events and parse them, and build a string
     * That will be exported to a file
     *
     * @return string
     */
    abstract protected function prepareStringForExport();

    /**
     * @return Collection|Event[]
     */
    final protected function getEvents()
    {
        return $this->events;
    }

    /**
     * @param string $string
     *
     * @return string
     */
    final protected function prepareString($string)
    {
        $string = (string) preg_replace('/(\r\n|\r|\n)+/', ' ', $string);
        $string = (string) preg_replace('/([\,;])/', '\\\$1', $string);
        $string = (string) preg_replace('/^\h*\v+/m', '', $string);
        $string = chunk_split($string, 60, "\r\n ");
        $string = trim($string);

        return $string;
    }
}
