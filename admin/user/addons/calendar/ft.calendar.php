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
use Solspace\Addons\Calendar\Controllers\ExclusionController as ExclusionController;
use Solspace\Addons\Calendar\Controllers\SelectDateController as SelectDateController;
use Solspace\Addons\Calendar\Helpers\DateTimeHelper;
use Solspace\Addons\Calendar\Helpers\FormMacrosHelper as FormMacrosHelper;
use Solspace\Addons\Calendar\Library\Carbon\Carbon;
use Solspace\Addons\Calendar\Library\EventFormDataParser\EventFormParser;
use Solspace\Addons\Calendar\Library\EventTags\EventTags as EventTags;
use Solspace\Addons\Calendar\Model\CalendarModel;
use Solspace\Addons\Calendar\Model\Event as Event;
use Solspace\Addons\Calendar\QueryParameters\EventQueryParameters;
use Solspace\Addons\Calendar\Repositories\CalendarRepository;
use Solspace\Addons\Calendar\Repositories\EventRepository;
use Solspace\Addons\Calendar\Repositories\PreferenceRepository;
use Solspace\Addons\Calendar\QueryParameters\ChannelQueryParameters;

class Calendar_ft extends EE_Fieldtype
{
    public $has_array_data = true;
    public $info           = array(
        'name'    => 'Calendar',
        'version' => '',
    );

    private $cache = array();

    /**
     * Returns true if the version has to be updated
     * False otherwise
     *
     * @param string $version
     *
     * @return bool
     */
    public function update($version = '')
    {
        return $version && version_compare($this->info['version'], $version, '>');
    }

    public function __construct()
    {
        parent::__construct();

        $this->info = require 'addon.setup.php';

        ee()->lang->loadfile('calendar');

        $this->field_id   = isset($this->settings['field_id']) ?
            $this->settings['field_id'] :
            $this->field_id;
        $this->field_name = isset($this->settings['field_name']) ?
            $this->settings['field_name'] :
            $this->field_name;

        $this->field_name = 'field_id_' . $this->field_id;
    }

    /**
     * Shows the Calendar fieldtype on the publish page
     *
     * @param array $fieldData
     *
     * @return string
     */
    public function display_field($fieldData)
    {
        if (!is_array($fieldData)) {
            $fieldData = array();
        }

        // Do EE Stuff
        ee()->lang->loadfile('calendar');
        $this->loadAssets();
        $fieldData['field_id'] = $this->field_id;

        // Load Controllers
        $eventController     = new EventController();
        $exclusionController = new ExclusionController();

        // Load Repositories
        $eventRepository    = EventRepository::getInstance();
        $calendarRepository = CalendarRepository::getInstance();

        $preference = PreferenceRepository::getInstance()->getOrCreate();

        // If we're editing an entry get its id
        $entryId = $this->content_id;
        $event   = $eventRepository->getOrCreateFromFieldAndEntryId($this->field_id, $entryId);

        // Get or create the calendar for the entry
        $settingsCalendarId = isset($this->settings['calendar_id']) ? $this->settings['calendar_id'] : 0;
        $calendar           = $calendarRepository->getById($event->calendar_id ?: $settingsCalendarId);

        // Get a list of all available calendars
        $userGroup    = ee()->session->userdata('group_id');

        if ($calendar) {
            $canEditEvent = $this->isMemberAllowedToEdit($calendar);
        } else {
            $canEditEvent = true;
        }

        /** @var CalendarModel[] $calendars */
        $calendarSelection = $calendarRepository->getForMemberGroup($userGroup);
        if ($calendarSelection->count()) {
            $calendarSelection = $calendarSelection->indexByIds();
        } else {
            $calendarSelection = array();
        }

        if ($event->id && $calendar && !$canEditEvent) {
            $calendarSelection[$calendar->id] = $calendar;
        }

        if (empty($calendarSelection)) {
            $calendarCreateUrl = ee('CP/URL', 'addons/settings/calendar/calendars/create');

            return str_replace('%link%', $calendarCreateUrl, lang('calendar_no_calendars_present'));
        }

        $exclusions = EventRepository::getInstance()->getExclusionsForEvent($event);
        $dates = EventRepository::getInstance()->getSelectDatesForEvent($event);

        if (isset($fieldData['start_day']) && !empty($fieldData['start_day'])) {
            $formParser = new EventFormParser($fieldData, $preference->date_format);
            $fieldData  = $formParser->parse();

            $eventController->updateEventWithFormData($event, $fieldData);
            $exclusionController->getExclusionsFromFormData($fieldData);
        }


        $jsCalendarDataObject                = new \stdClass();
        $jsCalendarDataObject->date_format   = $preference->getDatePickerFormat();
        $jsCalendarDataObject->first_day     = $preference->start_day;
        $jsCalendarDataObject->time_format   = $preference->time_format;
        $jsCalendarDataObject->time_interval = $preference->time_interval;
        $jsCalendarDataObject->duration      = $preference->duration;

        $validationErrors = $this->validate($fieldData, false);
        $field_data       = array(
            'isFrontend'        => strtoupper(REQ) === 'PAGE',
            'calendarSelection' => $calendarSelection,
            'calendar'          => $calendar,
            'preference'        => $preference,
            'fieldName'         => $this->field_name,
            'field_data'        => $fieldData,
            'field_name'        => ($this->field_name == 'default') ? 'calendar_f' : $this->field_name,
            'field_id'          => $this->field_id,
            'days'              => DateTimeHelper::getTranslatedDaysAbbr($preference->start_day),
            'months'            => DateTimeHelper::getTranslatedMonths(),
            'event'             => $event,
            'exclusions'        => $exclusions,
            'dates'             => $dates,
            'form'              => new FormMacrosHelper(),
            'timeFormat'        => $preference->time_format,
            'dateFormat'        => $preference->date_format,
            'startDay'          => $preference->start_day,
            'errors'            => $validationErrors === true ? '' : $validationErrors,
            'editingDisabled'   => !$canEditEvent,
            'js_calendar_data'  => $jsCalendarDataObject,
        );

        if (isset($this->content_id)) {
            $field_data['entry_id'] = $this->content_id;
        }

        require_once __DIR__ . '/mod.calendar.php';
        $modCalendar = new \Solspace\Addons\Calendar\Calendar();
        $output      = $modCalendar->fieldTypeWidget($field_data);

        return $output;
    }

    /**
     * Displays the Calendar fieldtype tag
     *
     * @param array  $data    Form data
     * @param string $params  Params
     * @param bool   $tagdata The tagdata
     *
     * @return string The parsed template parameters
     */
    public function replace_tag($data, $params = '', $tagdata = false)
    {
        if (!$tagdata) {
            return false;
        }

        $eventRepository = EventRepository::getInstance();

        $queryParameters = new EventQueryParameters();
        $entryId         = $this->row['entry_id'];

        $siteId = ee()->TMPL->fetch_param('site_id');
        $siteName = ee()->TMPL->fetch_param('site');

        if (!$siteId) {
            $siteId = ee()->config->item('site_id');
        }

        $queryParameters
            ->setEntryIds($entryId)
            ->setLimit(1000)
            ->setLoadResourceConsumingData(true);

        $channelQueryParameters = new ChannelQueryParameters();
        if ($siteName) {
            $channelQueryParameters->setSiteName($siteName);
        } else {
            $channelQueryParameters->setSiteId($siteId);
        }

        $queryParameters->setChannelQueryParameters($channelQueryParameters);

        $events = $eventRepository->getArrayResult($queryParameters);

        if (empty($events)) {
            return false;
        }

        $firstEvent = reset($events);

        $firstEvent['recurrences'] = $events;

        $recurrencePattern = sprintf('/%scalendar:recurrences(?:(?:\s+[a-z_]+=\".*\")*)*\s*%s/', LD, RD);

        preg_match_all($recurrencePattern, $tagdata, $matches);
        preg_match_all('/{calendar:recurrences([^}]*)}/s', $tagdata, $matches);

        foreach ($events as &$recurringEvent) {
            $recurringEvent['recurrence_start_date'] = $recurringEvent['event_start_date'];
            $recurringEvent['recurrence_end_date']   = $recurringEvent['event_end_date'];
        }

        if (isset($matches[1]) && !empty($matches[1])) {
            $matchedStrings = $matches[1];
            foreach ($matchedStrings as $match) {
                preg_match_all('/([a-z_]+)="([^"]+)"/', $match, $parameters);

                if (isset($parameters[1]) && isset($parameters[2])) {
                    $modifiedRecurrences = $events;

                    $modifiers = array();
                    foreach ($parameters[1] as $index => $property) {
                        $modifiers[$property] = $parameters[2][$index];
                    }

                    if (isset($modifiers["date_range_start"])) {
                        $rangeStartDate = new Carbon($modifiers["date_range_start"], DateTimeHelper::TIMEZONE_UTC);

                        // Unset all recurrences with
                        // start dates earlier than the range start date
                        foreach ($modifiedRecurrences as $i => $recurrence) {
                            if ($recurrence['start_date_carbon']->lt($rangeStartDate)) {
                                unset($modifiedRecurrences[$i]);
                                continue;
                            }

                            break;
                        }
                    }

                    if (isset($modifiers["date_range_end"])) {
                        $rangeEndDate = new Carbon($modifiers["date_range_end"], DateTimeHelper::TIMEZONE_UTC);

                        // Unset all recurrences with
                        // start dates older than the range end date
                        foreach ($modifiedRecurrences as $i => $recurrence) {
                            if ($recurrence['start_date_carbon']->gt($rangeEndDate)) {
                                unset($modifiedRecurrences[$i]);
                            }
                        }
                    }

                    if (isset($modifiers["offset"])) {
                        $modifiedRecurrences = array_slice($modifiedRecurrences, (int)$modifiers["offset"]);
                    }

                    if (isset($modifiers["limit"])) {
                        $modifiedRecurrences = array_slice($modifiedRecurrences, 0, (int)$modifiers["limit"]);
                    }

                    $firstEvent['recurrences'] = array_values($modifiedRecurrences);
                    unset($events);
                }
            }
        }

        $counter         = 0;
        $recurrenceCount = count($firstEvent['recurrences']);
        foreach ($firstEvent['recurrences'] as &$recurrence) {
            $recurrence['recurrence_count']         = ++$counter;
            $recurrence['recurrence_total_results'] = $recurrenceCount;
        }

        $firstEvent['recurrences:no_results'] = !count($firstEvent['recurrences']);

        $firstEvent = EventTags::prefixTemplateVariables(array($firstEvent), "calendar");

        return EventTags::parseTagdataPreservingDates($firstEvent, $tagdata);
    }

    /**
     * We just cache the data so we have an entry_id to save
     *
     * @param array $data
     *
     * @return string
     */
    public function save($data)
    {
        $this->cache['johnny'][$this->field_id] = $data;

        return '';
    }

    /**
     * The most important event of the entire thing. Parses the submitted
     * form data that was previously cached and saves everything
     *
     * @param array $data The passed on ExpressionEngine Data
     */
    public function post_save($data)
    {
        $fieldData             = $this->cache['johnny'][$this->field_id];
        $fieldData['field_id'] = $this->field_id;
        $calendarId            = $fieldData['calendar_id'];

        $eventController     = new EventController();
        $exclusionController = new ExclusionController();
        $selectDateController = new SelectDateController();

        $eventRepository    = EventRepository::getInstance();
        $calendarRepository = CalendarRepository::getInstance();

        $calendar = $calendarRepository->getById($calendarId);

        if (!$calendar) {
            return;
        }

        if (!$this->isMemberAllowedToEdit($calendar)) {
            return;
        }

        $entryId = $this->content_id;

        // Fetch an existing event if one exists
        $event = $eventRepository->getOrCreateFromFieldAndEntryId($this->field_id, $entryId);

        if (!$fieldData['start_day'] && $event instanceof Event) {
            $eventController->delete(array($event->id));

            return;
        }

        $preference = PreferenceRepository::getInstance()->getOrCreate();

        $formManipulator = new EventFormParser($fieldData, $preference->date_format);
        $fieldData       = $formManipulator->parse();

        $fieldData['calendar_id'] = $calendarId;
        $fieldData['entry_id']    = $entryId;
        $fieldData['field_id']    = $this->field_id;

        $event = $eventController->save($event, $fieldData);
        if ($event->isRecurring()) {
            $fieldData['event_id'] = $event->id;

            $exclusionController->save($fieldData, $event);
            $selectDateController->save($fieldData, $event);
        }
    }

    /**
     * Loads up some controllers and validates the shit out of things
     *
     * @param array $fieldData
     * @param bool  $runFormDataParser
     *
     * @return string
     */
    public function validate($fieldData, $runFormDataParser = true)
    {
        if (AJAX_REQUEST) {
            return true;
        }

        $noStartDay = !isset($fieldData['start_day']) || empty($fieldData['start_day']);
        if ($noStartDay) {
            $isFieldRequired = isset($this->settings['field_required']) && (bool)$this->settings['field_required'];
            if (isset($_POST[$this->field_name]['start_day']) && $isFieldRequired) {
                return sprintf('<ul><li>%s</li></ul>', lang('calendar_required'));
            }

            return true;
        }

        $eventController = new EventController;

        $preference = PreferenceRepository::getInstance()->getOrCreate();

        if ($runFormDataParser) {
            $formParser = new EventFormParser($fieldData, $preference->date_format);
            $fieldData  = $formParser->parse();
        }

        $errors = $eventController->validate($fieldData);

        if ($errors) {
            $returnedErrors = array();
            foreach ($errors as $field => $errorlist) {
                $returnedErrors[] = '<li>' . $errorlist[0] . '</li>';
            }

            return '<ul>' . implode("", $returnedErrors) . "</ul>";
        }

        return true;
    }

    /**
     * Displays the calendar selection for our fieldtype in its settings
     *
     * @param array $savedSettings
     *
     * @return null
     */
    public function display_settings($savedSettings)
    {
        $calendarRepository = CalendarRepository::getInstance();

        $calendarId = null;
        if (isset($savedSettings['calendar_id'])) {
            $calendarId = $savedSettings['calendar_id'];
        } else if (isset($this->settings['calendar_id'])) {
            $calendarId = $this->settings['calendar_id'];
        }

        $descriptionFieldId = null;
        if (isset($savedSettings['description_field_id'])) {
            $descriptionFieldId = $savedSettings['description_field_id'];
        } else if (isset($this->settings['description_field_id'])) {
            $descriptionFieldId = $this->settings['description_field_id'];
        }

        $locationFieldId = null;
        if (isset($savedSettings['location_field_id'])) {
            $locationFieldId = $savedSettings['location_field_id'];
        } else if (isset($this->settings['location_field_id'])) {
            $locationFieldId = $this->settings['location_field_id'];
        }

        $calendars = $calendarRepository->getAll()->indexByIds();

        if (empty($calendars)) {
            $link = ee('CP/URL', 'addons/settings/calendar/calendars/create');

            $calendarField = array(
                'type'     => 'html',
                'required' => true,
                'content'  => sprintf(
                    '%s<input type="hidden" name="calendar_id" value="0" />',
                    str_replace('%link%', $link, lang('calendar_no_calendars_present'))
                ),
            );
        } else {
            $calendarField = array(
                'type'    => 'select',
                'choices' => $calendars,
                'value'   => $calendarId,
            );
        }

        //$groupId = $this->settings['group_id'];
        $fieldId = $this->settings['field_id'];

        //if (!$groupId) {
        //    $segments = ee()->uri->rsegments;
        //    if (isset($segments[1]) && isset($segments[3])) {
        //        if ($segments[1] == "fields" && is_numeric($segments[3])) {
        //            $groupId = (int)$segments[3];
        //        }
        //    }
        //}

        $fieldsInGroup = ee('Model')
            ->get('ChannelField')
            //->filter('group_id', $groupId)
            ->filter('field_id', '!=', (int)$fieldId)
            ->all()
            ->getDictionary('field_id', 'field_label');

        $fieldsInGroup[0] = lang('calendar_no_field');
        ksort($fieldsInGroup);

        $settings = array(
            array(
                'title'  => 'calendar_default_calendar',
                'desc'   => 'calendar_default_calendar_desc',
                'fields' => array('calendar_id' => $calendarField),
            ),
            array(
                'title'  => 'calendar_description_field',
                'desc'   => 'calendar_description_field_desc',
                'fields' => array(
                    'description_field_id' => array(
                        'type'    => 'select',
                        'choices' => $fieldsInGroup,
                        'value'   => $descriptionFieldId,
                    ),
                ),
            ),
            array(
                'title'  => 'calendar_location_field',
                'desc'   => 'calendar_location_field_desc',
                'fields' => array(
                    'location_field_id' => array(
                        'type'    => 'select',
                        'choices' => $fieldsInGroup,
                        'value'   => $locationFieldId,
                    ),
                ),
            ),
        );

        return array(
            'calendar' => array(
                'label'    => 'calendar_field_options',
                'group'    => 'calendar',
                'settings' => $settings,
            ),
        );
    }

    /**
     * The only setting to save is the calendar id for a field
     *
     * @param array $data
     *
     * @return array The posted calendar id
     */
    public function save_settings($data)
    {
        $calendarId         = ee()->input->post('calendar_id', true);
        $descriptionFieldId = ee()->input->post('description_field_id', true);
        $locationFieldId    = ee()->input->post('location_field_id', true);

        if (!AJAX_REQUEST && !$calendarId) {
            //$groupId = $data['group_id'];
            $fieldId = $data['field_id'];

            ee('CP/Alert')
                ->makeInline('shared-form')
                ->asIssue()
                ->withTitle(lang('calendar_fieldtype_error'))
                ->addToBody(lang('calendar_fieldtype_no_calendar'))
                ->defer();

            if (!$fieldId) {
                ee()->functions->redirect(ee('CP/URL', 'channels/fields/create'));
            } else {
                ee()->functions->redirect(ee('CP/URL', 'channels/fields/edit/' . $fieldId));
            }
        }

        return array(
            'calendar_id'          => $calendarId,
            'description_field_id' => $descriptionFieldId,
            'location_field_id'    => $locationFieldId,
        );
    }

    /**
     * If the user group doesn't match calendar settings group
     * prevent this user from editing anything
     *
     * Unless this user belongs to the SuperAdmin group, then
     * we allow them to edit all events
     *
     * @param CalendarModel $calendar
     *
     * @return bool
     */
    private function isMemberAllowedToEdit(CalendarModel $calendar)
    {
        $calendarRepository = CalendarRepository::getInstance();

        $allowedMemberGroups = $calendarRepository->getMemberGroupIds($calendar);
        $currentMemberGroup  = ee()->session->userdata('group_id');

        if ((int)$currentMemberGroup === 1) {
            return true;
        }

        return in_array($currentMemberGroup, $allowedMemberGroups);
    }

    /**
     * Loading assets is such a weird complicated process with paths and other
     * stuff that I just added the code here and I hate it
     */
    private function loadAssets()
    {
        if (REQ === "PAGE") {
            return;
        }

        $stylesheets = array(
            'calendar/css/solspace-fa.css',
            'calendar/css/calendar.fieldtype.css',
            'calendar/plugins/jquery-timepicker-jt/jquery.timepicker.css',
        );

        $cssIncludeString = '';
        foreach ($stylesheets as $stylesheet) {
            $includePath = URL_THIRD_THEMES . $stylesheet;
            $cssIncludeString .= PHP_EOL . '<link rel="stylesheet" type="text/css" href="' . $includePath . '">';
        }

        $javascripts = array(
            URL_THIRD_THEMES . 'calendar/plugins/jquery-simplecolorpicker/jquery.simplecolorpicker.min.js',
            URL_THIRD_THEMES . 'calendar/plugins/jquery-timepicker-jt/jquery.timepicker.min.js',
            URL_THIRD_THEMES . 'calendar/js/calendar.fieldtype.js',
        );

        $jsIncludeString = '';
        foreach ($javascripts as $javascript) {
            $includePath = $javascript;
            $jsIncludeString .= PHP_EOL . '<script src="' . $includePath . '"></script>';
        }


        ee()->cp->add_to_head($cssIncludeString);
        ee()->cp->add_to_foot($jsIncludeString);
        ee()->cp->add_js_script(
            array('ui' => array('datepicker'))
        );
    }
}
