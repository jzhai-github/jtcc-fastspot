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

use Solspace\Addons\Calendar\Helpers\DateTimeHelper;
use Solspace\Addons\Calendar\Library\Carbon\Carbon;
use Solspace\Addons\Calendar\Model\Event;
use Solspace\Addons\Calendar\Repositories\EventRepository;

class ExportCalendarToIcs extends AbstractExportCalendar
{
    /**
     * Collect events and parse them, and build a string
     * That will be exported to a file
     *
     * @return string
     */
    protected function prepareStringForExport()
    {
        $events = $this->getEvents();

        $eventRepository = EventRepository::getInstance();

        $fieldIds = $events->getDictionary('entry_id', 'field_id');
        $eventIds = $events->getIds();

        $exceptions = $eventRepository->getExclusionDatesForEvents($eventIds);

        // Have to make this silly stupid workaround since EE's models don't work in frontend
        // ==================================================================================
        $entryIdsByFieldId = array();
        foreach ($fieldIds as $entryId => $fieldId) {
            if (!isset($entryIdsByFieldId[$fieldId])) {
                $entryIdsByFieldId[$fieldId] = array();
            }

            $entryIdsByFieldId[$fieldId][] = $entryId;
        }

        $entryIdsByFieldIdKeys = array_keys($entryIdsByFieldId);
        $fields = array();
        if ($entryIdsByFieldIdKeys) {
            $fields = ee('Model')
                ->get('ChannelField')
                ->filter('field_id', 'IN', $entryIdsByFieldIdKeys)
                ->all()
                ->indexByIds();
        }

        $descriptionsByEntryId = array();
        $locationsByEntryId    = array();
        foreach ($fields as $fieldId => $field) {
            $settings = $field->field_settings;

            $descriptionFieldId = isset($settings['description_field_id']) ? $settings['description_field_id'] : null;
            $locationFieldId    = isset($settings['location_field_id']) ? $settings['location_field_id'] : null;

            if (isset($entryIdsByFieldId[$fieldId])) {
                $select = array('entry_id');

                if ($descriptionFieldId) {
                    if (ee()->db->table_exists('channel_data_field_' . $descriptionFieldId)) {
                        $fetchedData = ee()->db
                            ->select("entry_id, field_id_$descriptionFieldId as description")
                            ->from('channel_data_field_' . $descriptionFieldId)
                            ->where_in('entry_id', $entryIdsByFieldId[$fieldId])
                            ->get()
                            ->result_array();

                        foreach ($fetchedData as $data) {
                            $entryId     = $data['entry_id'];
                            $description = $data['description'];

                            $descriptionsByEntryId[$entryId] = $description;
                        }
                    } else {
                        $select[] = "field_id_$descriptionFieldId AS description";
                    }
                }

                if ($locationFieldId) {
                    if (ee()->db->table_exists('channel_data_field_' . $locationFieldId)) {
                        $fetchedData = ee()->db
                            ->select("entry_id, field_id_$locationFieldId as location")
                            ->from('channel_data_field_' . $locationFieldId)
                            ->where_in('entry_id', $entryIdsByFieldId[$fieldId])
                            ->get()
                            ->result_array();

                        foreach ($fetchedData as $data) {
                            $entryId  = $data['entry_id'];
                            $location = $data['location'];

                            $locationsByEntryId[$entryId] = $location;
                        }
                    } else {
                        $select[] = "field_id_$locationFieldId AS location";
                    }
                }

                if (count($select) > 1) {
                    $fetchedData = ee()->db
                        ->select(implode(', ', $select))
                        ->from('channel_data')
                        ->where_in('entry_id', $entryIdsByFieldId[$fieldId])
                        ->get()
                        ->result_array();

                    foreach ($fetchedData as $data) {
                        $entryId     = $data['entry_id'];
                        $description = isset($data['description']) ? $data['description'] : '';
                        $location    = isset($data['location']) ? $data['location'] : '';

                        if ($description) {
                            $descriptionsByEntryId[$entryId] = $description;
                        }
                        if ($location) {
                            $locationsByEntryId[$entryId] = $location;
                        }
                    }
                }
            }
        }
        // ==================================================================================

        $now = Carbon::now(DateTimeHelper::TIMEZONE_UTC);

        $exportString = '';
        $exportString .= "BEGIN:VCALENDAR\n";
        $exportString .= "PRODID:-//Solspace/Calendar 2.x//EN\n";
        $exportString .= "VERSION:2.0\n";
        $exportString .= "CALSCALE:GREGORIAN\n";

        foreach ($events as $event) {

            /** @var Event $event */

            $entryId = $event->entry_id;

            $startDate = $event->getStartDate();
            $endDate   = $event->getEndDate();

            $description = isset($descriptionsByEntryId[$entryId]) ? $descriptionsByEntryId[$entryId] : null;
            $location    = isset($locationsByEntryId[$entryId]) ? $locationsByEntryId[$entryId] : null;
            $title       = $event->ChannelEntry->title;
            $uidHash     = md5($event->id . $title . $description);

            $exportString .= "BEGIN:VEVENT\n";
            $exportString .= sprintf("UID:%s@solspace.com\n", $uidHash);
            $exportString .= sprintf("DTSTAMP:%s\n", $now->format(self::DATE_TIME_FORMAT));

            if ($description) {
                $exportString .= sprintf("DESCRIPTION:%s\n", $this->prepareString($description));
            }
            if ($location) {
                $exportString .= sprintf("LOCATION:%s\n", $this->prepareString($location));
            }

            if ($event->all_day) {
                $exportString .= sprintf("DTSTART;VALUE=DATE:%s\n", $startDate->format(self::DATE_FORMAT));
                $exportString .= sprintf("DTEND;VALUE=DATE:%s\n", $endDate->copy()->addDay()->format(self::DATE_FORMAT));
            } else {
                $exportString .= sprintf("DTSTART:%s\n", $startDate->format(self::DATE_TIME_FORMAT));
                $exportString .= sprintf("DTEND:%s\n", $endDate->format(self::DATE_TIME_FORMAT));
            }

            if ($event->isRecurring() and !$event->isSelectDates()) {
                $exportString .= sprintf("RRULE:%s\n", $event->getRRuleData()->getRRuleForIcs());
                if (isset($exceptions[$event->id])) {
                    $exceptionDates = $exceptions[$event->id];
                    $exceptionDatesValues = array();
                    foreach ($exceptionDates as $date) {
                        $date = new Carbon($date, DateTimeHelper::TIMEZONE_UTC);
                        if ($event->all_day) {
                            $exceptionDatesValues[] = $date->format(self::DATE_FORMAT);
                        } else {
                            $date->setTime($startDate->hour, $startDate->minute, $startDate->second);
                            $exceptionDatesValues[] = $date->format(self::DATE_TIME_FORMAT);
                        }
                    }

                    $exceptionDates = implode(',', $exceptionDatesValues);
                    if ($event->all_day) {
                        $exportString .= sprintf("EXDATE;VALUE=DATE:%s\n", $exceptionDates);
                    } else {
                        $exportString .= sprintf("EXDATE:%s\n", $exceptionDates);
                    }
                }
            }

            $exportString .= sprintf("SUMMARY:%s\n", $this->prepareString($title));
            $exportString .= "END:VEVENT\n";

            if ($event->isSelectDates()) {

                $occurrences = $event->getSelectedDates();

                if ($occurrences) {
                    foreach ($occurrences as $occurrence) {

                        $carbonStartDate = new Carbon($occurrence->date, DateTimeHelper::TIMEZONE_UTC);

                        $diffInDays = $startDate->diffInDays($endDate, true, false);
                        $carbonEndDate = clone $carbonStartDate;
                        $carbonEndDate->setTime($endDate->hour, $endDate->minute, $endDate->second);
                        $carbonEndDate->day += $diffInDays;

                        $uidHash = md5($occurrence->id . $title . $description);

                        $exportString .= "BEGIN:VEVENT\n";
                        $exportString .= sprintf("UID:%s@solspace.com\n", $uidHash);
                        $exportString .= sprintf("DTSTAMP:%s\n", $now->format(self::DATE_TIME_FORMAT));

                        if ($description) {
                            $exportString .= sprintf("DESCRIPTION:%s\n", $this->prepareString($description));
                        }
                        if ($location) {
                            $exportString .= sprintf("LOCATION:%s\n", $this->prepareString($location));
                        }

                        if ($event->all_day) {
                            $exportString .= sprintf("DTSTART;VALUE=DATE:%s\n", $carbonStartDate->format(self::DATE_FORMAT));
                            $exportString .= sprintf("DTEND;VALUE=DATE:%s\n", $carbonEndDate->copy()->addDay()->format(self::DATE_FORMAT));
                        } else {
                            $exportString .= sprintf("DTSTART:%s\n", $carbonStartDate->format(self::DATE_TIME_FORMAT));
                            $exportString .= sprintf("DTEND:%s\n", $carbonEndDate->format(self::DATE_TIME_FORMAT));
                        }

                        $exportString .= sprintf("SUMMARY:%s\n", $this->prepareString($title));
                        $exportString .= "END:VEVENT\n";
                    }
                }
            }
        }

        $exportString .= "END:VCALENDAR\n";

        return $exportString;
    }
}
