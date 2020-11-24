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

use Solspace\Addons\Calendar\Controllers\EventController as EventController;
use Solspace\Addons\Calendar\Library\AddonBuilder as AddonBuilder;
use EllisLab\ExpressionEngine\Model\Channel\ChannelEntry;

/**
 * Only one extension hook is used in Calendar to delete events on the edit
 * page multi-select
 */
class Calendar_ext extends AddonBuilder
{
    public $required_by = array('module');

    /**
     * Deletes events in the EE edit screen
     *
     * @param ChannelEntry $entry
     *
     * @return null
     */
    public function before_channel_entry_delete(ChannelEntry $entry)
    {
        $eventController = new EventController();
        $eventController->deleteByEntryId($entry->getId());
    }
}
