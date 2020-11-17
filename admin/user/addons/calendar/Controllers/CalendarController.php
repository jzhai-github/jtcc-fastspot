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

use EllisLab\ExpressionEngine\Library\CP\Table;
use EllisLab\ExpressionEngine\Service\Model\Collection;
use EllisLab\ExpressionEngine\Service\Validation\Result;
use Solspace\Addons\Calendar\Library\Helpers;
use Solspace\Addons\Calendar\Model\CalendarModel;
use Solspace\Addons\Calendar\Repositories\CalendarRepository;
use Solspace\Addons\Calendar\Repositories\PreferenceRepository;

/**
 * Calendar Controller handles the interfacing between EE and the codebase
 */
class CalendarController
{

    /**
     * Deletes a Calendar and its related events and exclusions
     *
     * @param CalendarModel $calendar
     */
    public function delete(CalendarModel $calendar)
    {
        $entryIds = $this->getEntryIdsFromCalendarIds(array($calendar->id));
        $this->deleteEntriesByIds($entryIds);

        $calendar->delete();
    }

    /**
     * Bulk calendar deletion
     *
     * @param array $calendarIdList
     */
    public function deleteByIds(array $calendarIdList)
    {
        $entryIds = $this->getEntryIdsFromCalendarIds($calendarIdList);
        $this->deleteEntriesByIds($entryIds);

        ee()->db
            ->from('calendar_calendars')
            ->where_in('id', $calendarIdList)
            ->delete();
    }

    /**
     * Generates the table view and everything it requires
     *
     * @param Collection $calendars
     * @param array      $cachedVariables
     *
     * @return array - view data for the table
     */
    public function generateTable(Collection $calendars, array &$cachedVariables)
    {
        // Setting the title of the page
        $cachedVariables['cp_page_title'] = lang('calendar_calendars');

        /** @var Table $table */
        $table = ee('CP/Table', array('sort_col' => 'calendar_name'));

        $table->setColumns(
            array(
                'id',
                'calendar_name' => array(
                    'encode' => false,
                ),
                'calendar_short_name',
                'calendar_description',
                'calendar_events',
                'manage'        => array(
                    'type' => Table::COL_TOOLBAR,
                ),
                array(
                    'type' => Table::COL_CHECKBOX,
                ),
            )
        );

        $table->setNoResultsText(
            'calendar_no_calendars',
            'calendar_create_calendar',
            ee('CP/URL', 'addons/settings/calendar/calendars/create')
        );

        if ($calendars->count()) {
            $columnMapping = array(
                'id'                   => 'id',
                'calendar_name'        => 'name',
                'calendar_short_name'  => 'url_title',
                'calendar_description' => 'description',
                'calendar_events'      => 'event_count',
            );

            $calendarRepository = CalendarRepository::getInstance();

            $calendarIds = (array)$calendars->getIds();
            $eventCounts = $calendarRepository->getCalendarEventCount($calendarIds);

            $sortedCalendars = $calendars->indexByIds();

            $sortColumn = ee()->input->get_post('sort_col');
            if (isset($columnMapping[$sortColumn])) {
                $sortDirection = ee()->input->get_post('sort_dir');
                $sortDirection = ($sortDirection == 'asc') ? 'ASC' : 'DESC';

                $sortableColumn = $columnMapping[$sortColumn];

                usort(
                    $sortedCalendars,
                    function (CalendarModel $a, CalendarModel $b) use ($sortableColumn, $sortDirection, $eventCounts) {
                        // Special case for event_count sorting
                        if ($sortableColumn === 'event_count') {
                            $columnA = (int)@$eventCounts[$a->id];
                            $columnB = (int)@$eventCounts[$b->id];
                        } else {
                            $columnA = $a->{$sortableColumn};
                            $columnB = $b->{$sortableColumn};
                        }

                        if ($sortDirection == 'DESC') {
                            return $columnA < $columnB;
                        } else {
                            return $columnA > $columnB;
                        }
                    }
                );
            }

            $data = array();

            try {
               $exportActionId = ee()->db
                   ->where(
                       array(
                           'class'  => 'Calendar',
                           'method' => 'icsSubscription',
                       )
                   )
                   ->get('actions')
                   ->row()
                   ->action_id;
            } catch (\Exception $e) {
                $exportActionId = 0;
            }

            /** @var CalendarModel $calendar */
            foreach ($sortedCalendars as $calendar) {
                $calendarId = $calendar->id;
                $urlPrefix  = 'addons/settings/calendar/calendars/';
                $color      = '<div class="calendar-color enlarged" style="background-color: ' . $calendar->color . ';"></div>';
                $editUrl    = ee('CP/URL', $urlPrefix . 'edit/' . $calendarId);

                $toolbarItems = array(
                    'edit' => array(
                        'href'  => $editUrl,
                        'title' => lang('edit'),
                    )
                );



                if ($calendar->ics_hash) {
                    $toolbarItems['copy'] = array(
                        'href' => sprintf(
                            '%s%s?ACT=%d&hash=%s',
                            ee()->config->item('base_url'),
                            ee()->config->item('site_index'),
                            $exportActionId,
                            $calendar->ics_hash
                        ),
                        'class' => 'copy-ics-url',
                        'title' => lang('calendar_copy_ics_url'),
                    );
                    $toolbarItems['uninstall'] = array(
                        'href' => ee('CP/URL', $urlPrefix . 'disable_ics/' . $calendarId),
                        'title' => lang('calendar_disable_ics'),
                    );
                } else {
                    $toolbarItems['install'] = array(
                        'href' => ee('CP/URL', $urlPrefix . 'enable_ics/' . $calendarId),
                        'title' => lang('calendar_enable_ics'),
                    );
                }

                $data[] = array(
                    $calendarId,
                    array(
                        'content' => $color . $calendar->name,
                        'href'    => $editUrl,
                    ),
                    $calendar->url_title,
                    Helpers::truncateString($calendar->description, 40),
                    (int)@$eventCounts[$calendar->id],
                    array('toolbar_items' => $toolbarItems),
                    array(
                        'name'     => 'calendars[]',
                        'value'    => $calendarId,
                        'disabled' => (bool)$calendar->default,
                        'data'     => array(
                            'confirm-text' => 'you sure, baby?',
                            'confirm'      => sprintf(
                                '%s: <b>%s</b>',
                                lang('calendar_calendar'),
                                htmlentities($calendar->name, ENT_QUOTES)
                            ),
                        ),
                    ),
                );
            }

            $table->setData($data);

            $cachedVariables['footer'] = array(
                'submit_lang' => lang('calendar_delete_calendars'),
                'type'        => 'bulk_action_form',
            );

            $cachedVariables['form_right_links'] = array(
                array(
                    'link'  => ee('CP/URL', 'addons/settings/calendar/calendars/create'),
                    'title' => lang('calendar_add_calendar'),
                ),
            );

            $modalProperties = array(
                'name'      => 'modal-confirm-remove',
                'form_url'  => ee('CP/URL', 'addons/settings/calendar/calendars/delete'),
                'checklist' => array(
                    array(
                        'kind' => lang('calendar_calendars'),
                    ),
                ),
            );

            ee()->javascript->set_global(
                'lang.remove_confirm',
                lang('calendar_remove') . ': <b>### ' . lang('calendar_remove_calendars_plural') . '</b>'
            );

            ee('CP/Modal')->addModal(
                'modal-confirm-remove',
                ee('View')->make('_shared/modal_confirm_remove')->render($modalProperties)
            );

            ee()->cp->add_js_script(array('file' => array('cp/confirm_remove')));

        }

        return $table->viewData(ee('CP/URL', 'addons/settings/calendar/calendars'));
    }

    /**
     * Returns a formatted array of form sections for rendering a shared EE form
     *
     * @param CalendarModel $calendar
     *
     * @return array
     */
    public function getEditFormVariables(CalendarModel $calendar)
    {
        if ($calendar->id) {
            $calendarMemberGroups = CalendarRepository::getInstance()->getMemberGroupIds($calendar);

            $action = 'edit';
        } else {
            $preferenceRepository = PreferenceRepository::getInstance();

            $preference           = $preferenceRepository->getOrCreate();
            $calendarMemberGroups = $preferenceRepository->getMemberGroupIds($preference);

            $action = 'create';
        }
        $memberGroups = ee('Model')
            ->get('MemberGroup')
            ->fields('group_id', 'group_title')
            ->filter('group_id', '!=', 1)
            ->all()
            ->getDictionary('group_id', 'group_title');

        $vars['sections'] = array(
            array(
                array(
                    'title'  => 'calendar_name',
                    'fields' => array(
                        'name' => array(
                            'type'     => 'text',
                            'value'    => $calendar->name,
                            'required' => true,
                        ),
                    ),
                ),
                array(
                    'title'  => 'calendar_short_name',
                    'desc'   => 'calendar_calendar_url_title_bad_characters',
                    'fields' => array(
                        'short_name' => array(
                            'type'     => 'text',
                            'value'    => $calendar->url_title,
                            'required' => true,
                        ),
                    ),
                ),
                array(
                    'title'  => 'calendar_description',
                    'desc'   => 'calendar_description_desc',
                    'fields' => array(
                        'description' => array(
                            'type'     => 'textarea',
                            'value'    => $calendar->description,
                            'required' => false,
                        ),
                    ),
                ),
                array(
                    'title'  => 'calendar_color',
                    'desc'   => 'calendar_color_desc',
                    'fields' => array(
                        'color_selector' => array(
                            'type'    => 'html',
                            'content' => '<div id="colorSelector"><div style="background-color: ' . $calendar->color . ';"></div></div>',
                        ),
                        'color'          => array(
                            'type'     => 'text',
                            'attrs'    => 'id="color-picker" style="width: 100px; position: relative; top: 4px; left: 10px;"',
                            'value'    => $calendar->color,
                            'required' => true,
                        ),
                    ),
                ),
            ),
            'calendar_permissions' => array(
                array(
                    'title'   => 'calendar_allowed_member_groups',
                    'desc'    => 'calendar_allowed_member_groups_desc',
                    'caution' => true,
                    'fields'  => array(
                        'allowed_member_groups' => array(
                            'type'    => 'checkbox',
                            'choices' => $memberGroups,
                            'value'   => $calendarMemberGroups,
                        ),
                    ),
                ),
            ),
        );

        // Final view variables we need to render the form
        $vars += array(
            'base_url'              => ee('CP/URL', 'addons/settings/calendar/calendars/edit/' . $calendar->id),
            'cp_page_title'         => lang("calendar_calendar_$action"),
            'save_btn_text'         => 'btn_save_settings',
            'save_btn_text_working' => 'btn_saving',
        );

        return $vars;
    }

    /**
     * Checks the $_POST for values, validates them and saves if valid
     * Redirects to list on success
     *
     * @param CalendarModel $calendar
     *
     * @return string - response
     */
    public function handlePostedData(CalendarModel $calendar)
    {
        $postedData = $this->gatherPostedData();

        if ($this->validatePostedData($postedData) === true) {
            $calendar->name        = $postedData['name'];
            $calendar->url_title   = $this->sluggify($postedData['short_name']);
            $calendar->description = $postedData['description'];
            $calendar->color       = $postedData['color'];

            $calendar->save();

            $allowedMemberGroups = ee('Model')
                ->get('MemberGroup')
                ->filter('group_id', 'IN', $postedData['allowed_member_groups'])
                ->all()
                ->getIds();

            ee()->db->delete('calendar_calendar_member_groups', array('id' => $calendar->id));
            foreach ($allowedMemberGroups as $memberGroupId) {
                ee()->db->insert(
                    'calendar_calendar_member_groups',
                    array(
                        'group_id' => $memberGroupId,
                        'id'       => $calendar->getId(),
                    )
                );
            }

            ee('CP/Alert')
                ->makeInline('shared-form')
                ->asSuccess()
                ->withTitle(lang('success'))
                ->defer();

            ee()->functions->redirect(ee('CP/URL', 'addons/settings/calendar/calendars'));
        }
    }

    /**
     * Validates the posted data, returns the boolean result
     * Attaches alerts based on validation results
     *
     * @param array $postedData
     *
     * @return bool
     */
    private function validatePostedData($postedData = null)
    {
        // If the posted data is empty - do not even attempt validation
        if (empty($_POST)) {
            return false;
        }

        $postedData = array_filter($postedData);

        $rules = array(
            'name'       => 'required',
            'short_name' => 'required', //|regex[/[a-zA-Z_-]/]',
            'color'      => 'required', //|regex[/^#(?:\w{3}){1,2}$/]',
        );

        /** @var Result $result */
        $result = ee('Validation')->make($rules)->validate($postedData);

        if ($result->failed()) {
            $errors       = $result->getAllErrors();
            $messageArray = array();
            foreach ($errors as $field => $messages) {
                $messageArray[] = sprintf(
                    '<b>%s</b>: %s',
                    lang('calendar_' . $field),
                    implode(". ", $messages)
                );
            }

            ee('CP/Alert')
                ->makeInline('shared-form')
                ->asIssue()
                ->withTitle(lang('calendar_calendar_error'))
                ->addToBody($messageArray)
                ->now();

        } else {
            ee('CP/Alert')
                ->makeInline('shared-form')
                ->asSuccess()
                ->addToBody(lang('calendar_calendar_success'))
                ->defer();
        }

        return $result->isValid();
    }

    /**
     * Makes sure that the url_title of a given calendar is sluggified
     *
     * @param string $urlTitle
     *
     * @return string
     */
    private function sluggify($urlTitle)
    {
        ee()->load->helper('url');

        $word_separator = ee()->config->item('word_separator') ?: 'dash';

        return url_title($urlTitle, $word_separator, true);
    }

    /**
     * Gets posted data from $_POST if any present
     *
     * @return array
     */
    private function gatherPostedData()
    {
        /** @var \EE_Input $input */
        $input = ee()->input;

        $postedData = array(
            'name'                  => $input->post('name', true),
            'short_name'            => $input->post('short_name', true),
            'description'           => $input->post('description', true),
            'color'                 => $input->post('color', true),
            'date_format'           => $input->post('date_format', true),
            'time_format'           => $input->post('time_format', true),
            'start_day'             => $input->post('start_day', true),
            'overlap_threshold'     => $input->post('overlap_threshold', true),
            'allowed_member_groups' => $input->post('allowed_member_groups', true),
        );

        return $postedData;
    }

    /**
     * Deletes all events provided in $eventIds, and their exclusions
     *
     * @param array $entryIds
     */
    private function deleteEntriesByIds(array $entryIds)
    {
        if (empty($entryIds)) {
            return;
        }

        $entries = ee('Model')
            ->get('ChannelEntry')
            ->filter('entry_id', 'IN', $entryIds)
            ->all();

        foreach ($entries as $entry) {
            $entry->delete();
        }
    }

    /**
     * @param array $calendarIdList
     *
     * @return array
     */
    private function getEntryIdsFromCalendarIds(array $calendarIdList)
    {
        $entryIds = ee()->db
            ->select('entry_id')
            ->from('calendar_events')
            ->where_in('calendar_id', $calendarIdList)
            ->get()
            ->result_array();

        $entryIds = array_column($entryIds, 'entry_id');

        return $entryIds;
    }
}
