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

use EllisLab\ExpressionEngine\Model\Channel\Channel;
use EllisLab\ExpressionEngine\Model\Channel\ChannelEntry;
use EllisLab\ExpressionEngine\Service\Model\Collection;
use Solspace\Addons\Calendar\Helpers\DateTimeHelper;
use Solspace\Addons\Calendar\Library\Carbon\Carbon;
use Solspace\Addons\Calendar\Model\CalendarModel;
use Solspace\Addons\Calendar\Model\Event;
use Solspace\Addons\Calendar\Model\Exclusion;
use Solspace\Addons\Calendar\Repositories\MigrationRepository;

class MigrationController
{
    const MAX_EVENTS_SHOWN = 20;
    const BATCH_LIMIT      = 100;

    /**
     * Checks if there are any legacy tables present
     * If there are, and their count is correct - it returns true
     *
     * @return bool
     */
    public function isMigrationPossible()
    {
        $legacyTables = \Calendar_upd::getLegacyTablesRealNames();

        if ((int) ee()->config->item('site_id') !== 1) {
            return false;
        }

        $matchedLegacyTables = 0;
        foreach ($legacyTables as $table) {
            try {
                $numRows = ee()->db
                    ->query("SELECT COUNT(*) FROM " . $table)
                    ->num_rows();

                if ($numRows > 0) {
                    $matchedLegacyTables++;
                }
            } catch (\Exception $e) {
            }
        }

        return $matchedLegacyTables === count($legacyTables);
    }

    /**
     * Returns a formatted array of form sections for rendering a shared EE form
     *
     * @return array
     */
    public function getFormVariables()
    {
        $utilitiesRepository = MigrationRepository::getInstance();

        $legacyCalendars  = $utilitiesRepository->getLegacyCalendars();
        $legacyEventCount = $utilitiesRepository->getLegacyEventCount();

        $calendarList = '';
        foreach ($legacyCalendars as $calendar) {
            $calendarList .= '<li>' . $calendar->title . '</li>';
        }

        $vars['sections'] = array(
            array(
                array(
                    'title'  => lang('calendar_legacy_calendars'),
                    'desc'   => lang('calendar_legacy_calendars_desc'),
                    'fields' => array(
                        'calendar_list' => array(
                            'type'     => 'html',
                            'content'  => '<ul class="listing">' . $calendarList . '</ul>',
                            'required' => false,
                        ),
                    ),
                ),
                array(
                    'title'  => lang('calendar_legacy_events'),
                    'desc'   => lang('calendar_legacy_events_desc'),
                    'fields' => array(
                        'event_list' => array(
                            'type'     => 'html',
                            'content'  => $legacyEventCount
                                . '<input type="hidden" name="migrate" value="0" />'
                                . '<input type="hidden" name="cleanup" value="0" />',
                            'required' => false,
                        ),
                    ),
                ),
            ),
        );

        // Final view variables we need to render the form
        $migrationLink = ee('CP/URL', 'addons/settings/calendar/migration');
        $cleanupLink   = ee('CP/URL', 'addons/settings/calendar/migration/cleanup');

        $vars += array(
            'base_url'              => $migrationLink,
            'cp_page_title'         => sprintf(
                '<div>%s<br><i>%s</i></div>%s',
                lang('calendar_migration'),
                lang('calendar_migration_desc'),
                sprintf(
                    '<a class="btn" data-confirm="%s" id="cleanup-button" href="%s">%s</a>',
                    htmlspecialchars(lang('calendar_migration_cleanup_confirm')),
                    $cleanupLink,
                    lang('calendar_migration_cleanup')
                )
            ),
            'save_btn_text'         => 'calendar_migrate',
            'save_btn_text_working' => 'calendar_migrating',
        );

        $jsIncludeString = PHP_EOL . '<script src="' . URL_THIRD_THEMES . 'calendar/js/migration.js' . '"></script>';
        ee()->cp->add_to_foot($jsIncludeString);

        return $vars;
    }

    /**
     * Performs all acts of migration
     *
     * @return Exclusion[]
     */
    public function executeMigration()
    {
        $utilitiesRepository = MigrationRepository::getInstance();

        /** @var \EE_Input $input */
        $input = ee()->input;

        $migrate = $input->post('migrate', false);

        if ($migrate) {
            $legacyCalendars = $utilitiesRepository->getLegacyCalendars();
            $eventCount      = $utilitiesRepository->getLegacyEventCount();

            $siteId = ee()->config->item('site_id');

            $legacyToNewCalendarMap = array();
            foreach ($legacyCalendars as $calendar) {
                $calendarModel = $utilitiesRepository->getCalendarFromLegacy($calendar, $siteId);
                $calendarModel->save();

                $legacyToNewCalendarMap[$calendar->entry_id] = $calendarModel->id;
            }

            $count = 0;
            while ($count < $eventCount) {
                $batch = $utilitiesRepository->getLegacyEventBatch(self::BATCH_LIMIT, $count);

                foreach ($batch as $batchItem) {
                    $data  = $batchItem['data'];
                    $model = $batchItem['model'];

                    $eventCalendarId = $data['calendar_id'];
                    $calendarId      = $legacyToNewCalendarMap[$eventCalendarId];

                    $eventModel = $utilitiesRepository->getEventFromLegacy($model, $calendarId, $data);
                    $eventModel->save();

                    $utilitiesRepository->handleEventExceptions($eventModel);
                }

                $count += self::BATCH_LIMIT;
            }

            ee('CP/Alert')
                ->makeInline('shared-form')
                ->asSuccess()
                ->withTitle(lang('success'))
                ->defer();

            ee()->functions->redirect(ee('CP/URL', 'addons/settings/calendar/migration'));
        }
    }

    /**
     * Removes all legacy tables and the Calendar channel
     * Sets a success message
     * Redirects to Calendars page
     */
    public function executeCleanUp()
    {
        $legacyTables = \Calendar_upd::getLegacyTablesRealNames();
        foreach ($legacyTables as $table) {
            ee()->db->query(sprintf('DROP TABLE %s', $table));
        }

        ee('CP/Alert')
            ->makeInline('shared-form')
            ->asSuccess()
            ->withTitle(lang('calendar_cleanup_successful'))
            ->defer();

        ee()->functions->redirect(ee('CP/URL', 'addons/settings/calendar/calendars'));
    }

    // ===============================================================
    // Migration from Low Events
    // ===============================================================

    /**
     * Checks if the Low Events plugin exists
     * If it does - checks if we support that version
     *
     * @return bool
     */
    public function isMigrationFromLowPossible()
    {
        $lowEvents = ee('Model')
            ->get('Module')
            ->filter('module_name', 'Low_events')
            ->first();

        if ($lowEvents) {
            return version_compare($lowEvents->module_version, '1.5', '>=');
        }

        return false;
    }

    /**
     * Returns a formatted array of form sections for rendering a shared EE form
     *
     * @return array
     */
    public function getLowFormVariables()
    {
        $eventsByEntryId  = $this->getLowEvents();
        $lowFieldGroupIds = $this->getLowFieldGroupIds();

        list($calendarIds, $calendarFieldsByGroupId) = $this->getCalendarFieldsByGroupId($lowFieldGroupIds);

        $lowChannels = $this->getLowChannels($lowFieldGroupIds);
        if ($calendarIds) {
            $calendars = ee('Model')
                ->get('calendar:Calendar')
                ->filter('id', 'IN', $calendarIds)
                ->all()
                ->indexByIds();
        } else {
            $calendars = array();
        }


        $migratableEventList = array();
        /** @var Channel $channel */
        foreach ($lowChannels as $channel) {
            $group         = $channel->field_group;
            $calendarField = isset($calendarFieldsByGroupId[$group]) ? $calendarFieldsByGroupId[$group] : null;
            $calendar      = null;

            if ($calendarField && isset($calendarIds[$group])) {
                $calendarId = $calendarIds[$group];
                /** @var CalendarModel $calendar */
                $calendar = isset($calendars[$calendarId]) ? $calendars[$calendarId] : null;
            }


            /** @var Collection|ChannelEntry[] $channelEntries */
            $channelEntries = $channel->Entries;

            $events      = array();
            $eventsShown = 0;
            foreach ($channelEntries as $entry) {
                if (!isset($eventsByEntryId[$entry->entry_id])) {
                    continue;
                }

                // If we reach the max events per channel - show how many more will be included
                // without actually showing them
                if ($eventsShown >= self::MAX_EVENTS_SHOWN) {
                    $count    = $channelEntries->count() - $eventsShown;
                    $events[] = str_replace('%count%', $count, lang('calendar_more_event_count'));

                    break;
                }

                $events[] = $entry->title;
                $eventsShown++;
            }

            $data = array(
                'channel'        => $channel,
                'calendar_field' => $calendarField,
                'calendar'       => $calendar,
                'events'         => $events,
            );

            $migratableEventList[$channel->channel_id] = $data;
        }

        $lowEventsView    = ee('View')->make('calendar:migration_low_events');
        $vars['sections'] = array(
            array(
                array(
                    'title'  => lang('calendar_low_events'),
                    'desc'   => lang('calendar_low_events_desc'),
                    'fields' => array(
                        'calendar_list' => array(
                            'type'     => 'html',
                            'content'  => $lowEventsView->render(array('migratableEvents' => $migratableEventList)),
                            'required' => false,
                        ),
                    ),
                ),
            ),
        );

        // Final view variables we need to render the form
        $migrationLink = ee('CP/URL', 'addons/settings/calendar/migration_low');
        $vars += array(
            'base_url'              => $migrationLink,
            'cp_page_title'         => sprintf(
                '%s<br><i>%s</i>',
                lang('calendar_migration_from_low'),
                lang('calendar_migration_from_low_desc')
            ),
            'save_btn_text'         => 'calendar_migrate',
            'save_btn_text_working' => 'calendar_migrating',
        );

        return $vars;
    }

    /**
     * Performs all acts of migration from Low Events
     *
     * @return Exclusion[]
     */
    public function executeMigrationFromLow()
    {
        /** @var \EE_Input $input */
        $input = ee()->input;

        $migrate = $input->post('migrate', false);

        if ($migrate) {
            $eventsByEntryId  = $this->getLowEvents();
            $lowFieldGroupIds = $this->getLowFieldGroupIds();

            list($calendarIds, $calendarFieldsByGroupId) = $this->getCalendarFieldsByGroupId($lowFieldGroupIds);

            $lowChannels    = $this->getLowChannels($lowFieldGroupIds);
            $eventsMigrated = 0;

            /** @var Channel $channel */
            foreach ($lowChannels as $channel) {
                $group         = $channel->field_group;
                $calendarField = isset($calendarFieldsByGroupId[$group]) ? $calendarFieldsByGroupId[$group] : null;

                if (!$calendarField || !isset($calendarIds[$group])) {
                    continue;
                }

                $calendarId = $calendarIds[$group];

                foreach ($channel->Entries as $entry) {
                    $entryId         = $entry->entry_id;
                    $calendarFieldId = $calendarField->field_id;

                    if (!isset($eventsByEntryId[$entryId])) {
                        continue;
                    }

                    $event = $eventsByEntryId[$entryId];

                    $eventExists = ee()->db
                            ->where('entry_id', $entryId)
                            ->where('field_id', $calendarFieldId)
                            ->where('calendar_id', $calendarId)
                            ->get('calendar_events')
                            ->num_rows() > 0;

                    if ($eventExists) {
                        continue;
                    }

                    $isAllDay  = $event['all_day'] === 'y';
                    $startDate = $event['start_date'];
                    $endDate   = $event['end_date'];

                    if (!$isAllDay) {
                        $startDate .= ' ' . $event['start_time'];
                        $endDate .= ' ' . $event['end_time'];
                    } else {
                        $endDate .= ' 23:59:59';
                    }

                    $startDate = new Carbon($startDate, DateTimeHelper::TIMEZONE_UTC);
                    $endDate   = new Carbon($endDate, DateTimeHelper::TIMEZONE_UTC);

                    $eventModel              = Event::create();
                    $eventModel->all_day     = $isAllDay;
                    $eventModel->start_date  = $startDate->toDateTimeString();
                    $eventModel->end_date    = $endDate->toDateTimeString();
                    $eventModel->calendar_id = $calendarId;
                    $eventModel->field_id    = $calendarFieldId;
                    $eventModel->entry_id    = $entryId;

                    $eventModel->save();

                    $eventsMigrated++;
                }
            }

            ee('CP/Alert')
                ->makeInline('shared-form')
                ->asSuccess()
                ->withTitle(lang('success'))
                ->addToBody(str_replace('%count%', $eventsMigrated, lang('calendar_events_migrated')))
                ->defer();

            ee()->functions->redirect(ee('CP/URL', 'addons/settings/calendar/migration_low'));
        }
    }

    /**
     * Returns all Low Events indexed by their entry_id
     *
     * @return array
     */
    private function getLowEvents()
    {
        $lowEvents       = ee()->db->get('low_events')->result_array();
        $eventsByEntryId = array();
        foreach ($lowEvents as $event) {
            $eventsByEntryId[$event['entry_id']] = $event;
        }

        return $eventsByEntryId;
    }

    /**
     * Gets Low Events field group ID's
     *
     * @return array
     */
    private function getLowFieldGroupIds()
    {
        $lowField = ee('Model')
            ->get('ChannelField')
            ->filter('field_type', 'low_events')
            ->all();

        $lowFieldGroupIds = $lowField->getDictionary('field_id', 'group_id');

        return $lowFieldGroupIds;
    }

    /**
     * @param array $lowFieldGroupIds
     *
     * @return array [calendarIds[], calendarFieldsByGroupId[]]
     */
    private function getCalendarFieldsByGroupId($lowFieldGroupIds)
    {
        if ($lowFieldGroupIds) {
            $calendarFields = ee('Model')
                ->get('ChannelField')
                ->filter('field_type', 'calendar')
                ->filter('group_id', 'IN', $lowFieldGroupIds)
                ->all();
        } else {
            $calendarFields = new Collection();
        }

        $calendarFieldsByGroupId = $calendarIds = array();
        foreach ($calendarFields as $field) {
            $calendarFieldsByGroupId[$field->group_id] = $field;

            $settings                      = $field->getSettingsValues();
            $calendarIds[$field->group_id] = $settings['field_settings']['calendar_id'];
        }

        return array($calendarIds, $calendarFieldsByGroupId);
    }

    /**
     * @param array $lowFieldGroupIds
     *
     * @return Channel[]|Collection
     */
    private function getLowChannels($lowFieldGroupIds)
    {
        if (empty($lowFieldGroupIds)) {
            return new Collection();
        }

        return $lowChannels = ee('Model')
            ->get('Channel')
            ->filter('field_group', 'IN', $lowFieldGroupIds)
            ->all();
    }
}
