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

namespace Solspace\Addons\Calendar\Controllers;

use Solspace\Addons\Calendar\Helpers\DateTimeHelper;
use Solspace\Addons\Calendar\Library\Carbon\Carbon;
use Solspace\Addons\Calendar\Library\RRule\RRule;
use Solspace\Addons\Calendar\Model\Event as Event;
use Solspace\Addons\Calendar\Repositories\EventRepository;
use Solspace\Addons\Calendar\Validators\EventValidator as EventValidator;
use Solspace\Addons\Calendar\Validators\RecurrenceValidator;

/**
 * The event controller handles all the fetching and mingling of
 * ExpressionEngine data with our model objects
 */
class EventController
{
    /**
     * Takes data from an event form and saves
     * an event object
     *
     * @param Event $event
     * @param array $formData An array of submitted form data
     *
     * @return Event
     */
    public function save(Event $event, $formData)
    {
        $this->updateEventWithFormData($event, $formData);

        $event->save();

        // Since EE's models can't handle NULL values
        // We have to manually set them to NULL via ActiveRecord
        if (is_null($event->rrule)) {
            ee()->db
                ->where('id', $event->id)
                ->update('calendar_events', array('rrule' => null, 'until_date' => null));
        }

        return $event;
    }

    /**
     * Clears out exceptions before finally deleting event
     *
     * @param array $eventIds
     */
    public function delete(array $eventIds)
    {
        if (empty($eventIds)) {
            return;
        }

        $exclusionController = new ExclusionController();
        $exclusionController->deleteByEvents($eventIds);

        // Delete all events provided in $eventIds
        ee()->db
            ->from('calendar_events')
            ->where_in('id', $eventIds)
            ->delete();
    }

    /**
     * Deletes all events and exclusions based on ChannelEntry ID
     *
     * @param int $entryId
     */
    public function deleteByEntryId($entryId)
    {
        $eventRepository = EventRepository::getInstance();
        $eventIds        = (array)$eventRepository->getFromEntryId($entryId)->getIds();

        $this->delete($eventIds);
    }

    /**
     * Helper method for validating the data
     *
     * @param  array $fieldData The submitted form data
     *
     * @return mixed An array of errors or an empty array
     */
    public function validate($fieldData)
    {
        $eventValidator = new EventValidator($fieldData, ee()->lang->language);
        $errors         = $eventValidator->validate();

        if (isset($fieldData['repeats']) && (bool)$fieldData['repeats'] && $fieldData['freq'] != 'dates') {
            $recurrenceValidator = new RecurrenceValidator($fieldData, ee()->lang->language);
            $recurrenceErrors    = $recurrenceValidator->validate();

            $errors = array_merge($errors, $recurrenceErrors);
        }

        return $errors;
    }

    /**
     * Updates all Event properties from $formData
     *
     * @param Event $event
     * @param array $formData
     */
    public function updateEventWithFormData(Event $event, array $formData)
    {
        $event->start_date  = $formData['start_date'];
        $event->end_date    = $formData['end_date'];
        $event->entry_id    = @$formData['entry_id'];
        $event->field_id    = $formData['field_id'];
        $event->calendar_id = $formData['calendar_id'];
        $event->all_day     = isset($formData['all_day']) && (bool)$formData['all_day'];

        $isRepeating = isset($formData['repeats']) && (bool)$formData['repeats'];
        if ($isRepeating) {
            if (isset($formData['until'])) {
                $until = new Carbon($formData['until'], DateTimeHelper::TIMEZONE_UTC);
                $until->setTime(23, 59, 59);
                $event->until_date = $until->toDateTimeString();
                $formData['until'] = $until->toISO8601String();
            }

            if (array_key_exists('freq', $formData)) {
                if ($formData['freq'] != 'dates') {
                    $rrule        = RRule::create($formData);
                    $event->rrule = $rrule->getRRule();
                    $event->select_dates = false;
                } else {
                    $event->rrule = null;
                    $event->select_dates = true;
                }
            }

        } else {
            $event->rrule      = null;
            $event->until_date = null;
            $event->select_dates = null;
        }
    }
}
