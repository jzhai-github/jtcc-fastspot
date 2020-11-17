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

namespace Solspace\Addons\Calendar;

use EllisLab\ExpressionEngine\Service\Model\Collection;
use Solspace\Addons\Calendar\Controllers\CalendarController as CalendarController;
use Solspace\Addons\Calendar\Controllers\MigrationController;
use Solspace\Addons\Calendar\Controllers\PreferenceController;
use Solspace\Addons\Calendar\Controllers\UpdateController;
use Solspace\Addons\Calendar\Exceptions\CalendarNotFoundException;
use Solspace\Addons\Calendar\Helpers\DateTimeHelper;
use Solspace\Addons\Calendar\Helpers\DateTimeHelper as GDateTimeHelper;
use Solspace\Addons\Calendar\Helpers\FormMacrosHelper as FormMacrosHelper;
use Solspace\Addons\Calendar\Library\AddonBuilder;
use Solspace\Addons\Calendar\Library\Carbon\Carbon as Carbon;
use Solspace\Addons\Calendar\Library\EventList\EventList as EventList;
use Solspace\Addons\Calendar\Library\Schedule\Type\Month as Month;
use Solspace\Addons\Calendar\Library\Schedule\Type\Week as Week;
use Solspace\Addons\Calendar\Model\CalendarModel;
use Solspace\Addons\Calendar\Model\Event;
use Solspace\Addons\Calendar\Repositories\CalendarRepository;
use Solspace\Addons\Calendar\Repositories\EventRepository;
use Solspace\Addons\Calendar\Repositories\PreferenceRepository;
use Solspace\Addons\Calendar\Services\UpdateService;
use Solspace\Addons\Calendar\Utilities\ControlPanelView;

class Calendar_mcp extends ControlPanelView
{
    const VIEW_MONTH = 'month';
    const VIEW_WEEK  = 'week';
    const VIEW_DAY   = 'day';

    const SECTION_MONTH      = 'month';
    const SECTION_WEEK       = 'week';
    const SECTION_DAY        = 'day';
    const SECTION_CALENDAR   = 'calendar';
    const SECTION_PREFERENCE = 'preference';
    const SECTION_UTILITIES  = 'utilities';
    const SECTION_DEMO       = 'demo';

    public function __construct()
    {
        parent::__construct('module');

        $this->loadSidebar();
        $this->loadAssets();

        //shimming the old macros setup for PHP views.
        $this->cached_vars['form'] = new FormMacrosHelper();
    }

    /**
     * Displays a calendar view of events for a given date
     *
     * @param string $view - "month", "week" or "day" (defaults to "month")
     * @param int    $year
     * @param int    $month
     * @param int    $day
     *
     * @return string
     */
    public function index($view = null, $year = null, $month = null, $day = null)
    {
        if (empty($view)) {
            ee()->functions->redirect($this->mcp_link(self::VIEW_MONTH));
        }
        ee()->cp->add_js_script(array('ui' => array('datepicker')));

        $calendarRepository = CalendarRepository::getInstance();
        $calendarListing    = $calendarRepository->getAll();

        // Store the calendars in cached vars, we need to access them in the templates
        $this->cached_vars['calendars'] = array();
        foreach ($calendarListing as $calendar) {
            $this->cached_vars['calendars'][$calendar->id] = $calendar;
        }

        $date = $this->getDateObject($year, $month, $day);

        switch (trim(strtolower($view))) {
            case self::VIEW_WEEK:
                return $this->getWeekView($calendarListing, $date);
                break;

            case self::VIEW_DAY:
                return $this->getDayView($calendarListing, $date);
                break;

            case self::VIEW_MONTH:
            default:
                return $this->getMonthView($calendarListing, $date);
                break;
        }
    }

    // Aliases for the ::index() method
    // ================================

    /**
     * @return string
     */
    public function day()
    {
        $arguments = array_merge(array(__FUNCTION__), func_get_args());

        return call_user_func_array(array($this, 'index'), $arguments);
    }

    /**
     * @return string
     */
    public function week()
    {
        $arguments = array_merge(array(__FUNCTION__), func_get_args());

        return call_user_func_array(array($this, 'index'), $arguments);
    }

    /**
     * @return string
     */
    public function month()
    {
        $arguments = array_merge(array(__FUNCTION__), func_get_args());

        return call_user_func_array(array($this, 'index'), $arguments);
    }

    /**
     * Either lists, creates or deletes a calendar. Based on $action
     *
     * @param string $action
     * @param int    $calendarId
     *
     * @return string
     * @throws CalendarNotFoundException
     */
    public function calendars($action = null, $calendarId = null)
    {
        $vars = array();

        $calendarController = new CalendarController();
        $calendarRepository = CalendarRepository::getInstance();

        switch (strtolower(trim($action))) {
            case 'delete':
                $calendarIds = ee()->input->get_post('calendars');
                if ($calendarIds && is_array($calendarIds)) {
                    $calendarController->deleteByIds($calendarIds);

                    $message = lang('calendar_calendar_deleted');
                    if (count($calendarIds) > 1) {
                        $message = str_replace('%count%', count($calendarIds), lang('calendar_calendars_deleted'));
                    }

                    ee('CP/Alert')
                        ->makeInline('shared-form')
                        ->asSuccess()
                        ->withTitle(lang('success'))
                        ->addToBody($message)
                        ->defer();
                }

                $file = null;
                ee()->functions->redirect(ee('CP/URL', 'addons/settings/calendar/calendars'));
                break;

            case 'edit':
            case 'create':
                if ((int)$calendarId) {
                    $calendar = $calendarRepository->getById((int)$calendarId);

                    if (!$calendar) {
                        throw new CalendarNotFoundException("Could not find Calendar by ID [$calendarId]");
                    }
                } else {
                    $calendar = CalendarModel::create(ee()->config->item('site_id'));
                }

                $vars = $calendarController->getEditFormVariables($calendar);
                $calendarController->handlePostedData($calendar);

                $file   = 'calendar/edit';
                $crumbs = array(
                    array(lang('calendar_calendars'), $this->mcp_link('calendars')),
                    array(lang("calendar_calendar_$action")),
                );

                break;

            case 'enable_ics':
                $calendar = $calendarRepository->getById((int)$calendarId);

                if (!$calendar) {
                    throw new CalendarNotFoundException("Could not find Calendar by ID [$calendarId]");
                }

                $calendar->regenerateIcsHash();
                $calendar->save();

                ee()->functions->redirect(ee('CP/URL', 'addons/settings/calendar/calendars'));

                break;

            case 'disable_ics':
                $calendar = $calendarRepository->getById((int)$calendarId);

                if (!$calendar) {
                    throw new CalendarNotFoundException("Could not find Calendar by ID [$calendarId]");
                }

                $calendar->ics_hash = null;
                $calendar->save();

                ee()->functions->redirect(ee('CP/URL', 'addons/settings/calendar/calendars'));

                break;

            default:
                $calendars     = $calendarRepository->getAll();
                $vars['table'] = $calendarController->generateTable($calendars, $this->cached_vars);
                $file          = 'calendar/listing';

                $crumbs = array(
                    array(lang('calendar_calendars')),
                );
                break;
        }

        $this->cached_vars = array_merge($this->cached_vars, $vars);

        return $this->mcp_view(
            array(
                'file'      => $file,
                'highlight' => 'calendars',
                'crumbs'    => $crumbs,
            )
        );
    }

    /**
     * Preference form
     *
     * @param string $message
     *
     * @return string
     */
    public function preferences($message = '')
    {
        if ($message == 'saved') {
            $this->prep_message(lang('calendar_preferences_saved'), true, true);
        }

        $preferenceController = new PreferenceController();

        $preference = PreferenceRepository::getInstance()->getOrCreate();

        $formVariables     = $preferenceController->getFormVariables($preference);
        $this->cached_vars = array_merge($this->cached_vars, $formVariables);

        $preferenceController->savePostedData($preference);

        return $this->mcp_view(
            array(
                'file'         => 'preferences',
                'highlight'    => 'preferences',
                'show_message' => false,
                'crumbs'       => array(
                    array(lang('calendar_preferences'),),
                ),
            )
        );
    }

    /**
     * Show the migration page
     *
     * @param string $message
     *
     * @return string
     */
    public function migration($message = '')
    {
        if ($message == 'saved') {
            $this->prep_message(lang('calendar_migration_complete'), true, true);
        }

        $migrationController = new MigrationController();
        if (!$migrationController->isMigrationPossible()) {
            return '';
        }

        if ($message === 'cleanup' && isset($_POST['cleanup'])) {
            $migrationController->executeCleanUp();
        }

        $formVariables     = $migrationController->getFormVariables();
        $this->cached_vars = array_merge($this->cached_vars, $formVariables);

        $migrationController->executeMigration();

        return $this->mcp_view(
            array(
                'file'         => 'migration',
                'highlight'    => 'migration',
                'show_message' => false,
                'crumbs'       => array(
                    array(lang('calendar_migration'),),
                ),
            )
        );
    }

    /**
     * Show the Low Events migration page
     *
     * @param string $message
     *
     * @return string
     */
    public function migration_low($message = '')
    {
        if ($message == 'saved') {
            $this->prep_message(lang('calendar_migration_complete'), true, true);
        }

        $migrationController = new MigrationController();
        if (!$migrationController->isMigrationFromLowPossible()) {
            return '';
        }

        $formVariables     = $migrationController->getLowFormVariables();
        $this->cached_vars = array_merge($this->cached_vars, $formVariables);

        $migrationController->executeMigrationFromLow();

        return $this->mcp_view(
            array(
                'file'         => 'migration_low',
                'highlight'    => 'migration_low',
                'show_message' => false,
                'crumbs'       => array(
                    array(lang('calendar_migration_from_low'),),
                ),
            )
        );
    }

    /**
     * @return array
     */
    public function updates()
    {
        $updateController = new UpdateController();

        return $this->renderView($updateController->index());
    }

    /**
     * Code pack page
     *
     * @param string $message - lang line for update message
     *
     * @return string html output
     */
    public function code_pack($message = '')
    {
        $this->prep_message($message, true, true);

        // --------------------------------------------
        //	Load vars from code pack lib
        // --------------------------------------------
        $codePackLib     = $this->lib('CodePack');
        $codePackLibrary =& $codePackLib;

        $codePackLibrary->autoSetLang = true;

        $cpt = $codePackLibrary->getTemplateDirectoryArray(
            $this->addon_path . 'code_pack/'
        );

        // --------------------------------------------
        //  Start sections
        // --------------------------------------------
        $main_section = array();

        // --------------------------------------------
        //  Prefix
        // --------------------------------------------
        $main_section['template_group_prefix'] = array(
            'title'  => lang('template_group_prefix'),
            'desc'   => lang('template_group_prefix_desc'),
            'fields' => array(
                'prefix' => array(
                    'type'  => 'text',
                    'value' => $this->lower_name . '_',
                ),
            ),
        );

        // --------------------------------------------
        //  Templates
        // --------------------------------------------
        $main_section['templates'] = array(
            'title'  => lang('groups_and_templates'),
            'desc'   => lang('groups_and_templates_desc'),
            'fields' => array(
                'templates' => array(
                    'type'    => 'html',
                    'content' => $this->view('code_pack_list', compact('cpt')),
                ),
            ),
        );

        // --------------------------------------------
        //  Compile
        // --------------------------------------------
        $this->cached_vars['sections'][] = $main_section;
        $this->cached_vars['form_url']   = $this->mcp_link('code_pack_install');
        $this->cached_vars['box_class']  = 'code_pack_box';

        //---------------------------------------------
        //  Load Page and set view vars
        //---------------------------------------------
        // Final view variables we need to render the form
        $this->cached_vars += array(
            'base_url'              => $this->mcp_link('code_pack_install'),
            'cp_page_title'         => lang('demo_templates') . '<br /><i>' . lang('demo_description') . '</i>',
            'save_btn_text'         => 'install_demo_templates',
            'save_btn_text_working' => 'btn_saving',
        );

        ee('CP/Alert')
            ->makeInline('shared-form')
            ->asIssue()
            ->addToBody(lang('prefix_error'))
            ->cannotClose()
            ->now();

        return $this->mcp_view(
            array(
                'file'      => 'code_pack_form',
                'highlight' => 'demo_templates',
                'pkg_css'   => array('mcp_defaults'),
                'pkg_js'    => array('code_pack'),
                'crumbs'    => array(
                    array(lang('demo_templates')),
                ),
            )
        );
    }

    /**
     * Code Pack Install
     *
     * @return string html output
     */
    public function code_pack_install()
    {
        $prefix = trim((string)ee()->input->get_post('prefix'));

        if ($prefix === '') {
            return ee()->functions->redirect($this->mcp_link('code_pack'));
        }

        // -------------------------------------
        //	load lib
        // -------------------------------------
        $codePack        = $this->lib('CodePack');
        $codePackLibrary =& $codePack;

        $codePackLibrary->autoSetLang = true;

        // -------------------------------------
        //	¡Las Variables en vivo! ¡Que divertido!
        // -------------------------------------
        $variables = array(
            'code_pack_name' => $this->lower_name . '_code_pack',
            'code_pack_path' => $this->addon_path . 'code_pack/',
            'prefix'         => $prefix,
        );

        // -------------------------------------
        //	install
        // -------------------------------------
        $return = $codePackLibrary->installCodePack($variables);

        //--------------------------------------------
        //	Table
        //--------------------------------------------
        $table = ee(
            'CP/Table',
            array(
                'sortable' => false,
                'search'   => false,
            )
        );

        $tableData = array();

        //--------------------------------------------
        //	Errors or regular
        //--------------------------------------------
        if (!empty($return['errors'])) {
            foreach ($return['errors'] as $error) {
                $item = array();

                //	Error
                $item[] = lang('error');

                //	Label
                $item[] = $error['label'];

                //	Field type
                $item[] = str_replace(
                    array(
                        '%conflicting_groups%',
                        '%conflicting_data%',
                        '%conflicting_global_vars%',
                    ),
                    array(
                        implode(", ", $return['conflicting_groups']),
                        implode("<br />", $return['conflicting_global_vars']),
                    ),
                    $error['description']
                );

                $tableData[] = $item;
            }
        } else {
            foreach ($return['success'] as $success) {
                $item = array();

                //	Error
                $item[] = lang('success');

                //	Label
                $item[] = $success['label'];

                //	Field type
                if (isset($success['link'])) {
                    $item[] = array(
                        'content' => $success['description'],
                        'href'    => $success['link'],
                    );
                } else {
                    $item[] = str_replace(
                        array(
                            '%template_count%',
                            '%global_vars%',
                            '%success_link%',
                        ),
                        array(
                            $return['template_count'],
                            implode("<br />", $return['global_vars']),
                            '',
                        ),
                        $success['description']
                    );
                }

                $tableData[] = $item;
            }
        }

        $table->setColumns(
            array(
                'status',
                'description',
                'details',
            )
        );

        $table->setData($tableData);
        $table->setNoResultsText('no_results');

        $this->cached_vars['table']    = $table->viewData();
        $this->cached_vars['form_url'] = '';

        return $this->mcp_view(
            array(
                'file'      => 'code_pack_install',
                'highlight' => 'demo_templates',
                'pkg_css'   => array('mcp_defaults'),
                'crumbs'    => array(
                    array(lang('demo_templates')),
                ),
            )
        );
    }

    /**
     * Returns the Month type vie wfor the modules
     *
     * @param Collection|CalendarModel[] $calendars
     * @param Carbon                     $date
     *
     * @return string The rendered month view
     */
    private function getMonthView($calendars, $date)
    {
        $view = self::VIEW_MONTH;

        $eventRepository = EventRepository::getInstance();

        $preferences = PreferenceRepository::getInstance()->getOrCreate();

        $firstDayOfMonth      = $date->copy();
        $firstDayOfMonth->day = 1;

        $lastDayOfMonth      = $date->copy();
        $lastDayOfMonth->day = $date->daysInMonth;

        $month = new Month($firstDayOfMonth, $lastDayOfMonth);
        $month->setfirstDayOfWeek($preferences->start_day);

        $rangeStart = clone $firstDayOfMonth;
        $rangeStart->day -= 6;
        $rangeEnd = clone $lastDayOfMonth;
        $rangeEnd->day += 6;

        $events    = $eventRepository->getFromCalendars($calendars, $rangeStart, $rangeEnd);
        $eventList = new EventList($events, $rangeStart, $rangeEnd);

        $this->cacheEventChannelEntries($events);

        $this->cached_vars = array_merge(
            $this->cached_vars,
            array(
                'days'       => GDateTimeHelper::getTranslatedDaysAbbr3($preferences->start_day),
                'schedule'   => $month,
                'events'     => $eventList,
                'now'        => new Carbon('now', DateTimeHelper::getUserTimezone()),
                'timeFormat' => $preferences->time_format,
                'dateFormat' => $preferences->date_format,
                'urls'       => $this->getURLs($date, $view),
                'type'       => $view,
                'title'      => $month->getStartDateTime()->format('F Y'),
            )
        );

        return $this->mcp_view(
            array(
                'file'         => "calendar/$view",
                'highlight'    => "events/$view",
                'show_message' => false,
                'crumbs'       => array(
                    array(lang('calendar_by_month')),
                ),
            )
        );
    }

    /**
     * Returns the Week type view for the modules
     *
     * @param  Collection $calendars The sites' calendars
     * @param  Carbon     $date
     *
     * @return string
     */
    private function getWeekView($calendars, Carbon $date)
    {
        $view = self::VIEW_WEEK;

        $eventRepository = EventRepository::getInstance();

        $preferences = PreferenceRepository::getInstance()->getOrCreate();

        /** @var $startDate Carbon */
        /** @var $endDate Carbon */
        list($startDate, $endDate) = Week::getSectionByTime($preferences->start_day, $date);

        $rangeStart = clone $startDate;
        $rangeStart->day -= 14;
        $rangeEnd = clone $endDate;

        $week      = new Week($startDate, $endDate);
        $events    = $eventRepository->getFromCalendars($calendars, $rangeStart, $rangeEnd);
        $eventList = new EventList($events, $rangeStart, $rangeEnd);
        $days      = GDateTimeHelper::getTranslatedDaysAbbr3($preferences->start_day);
        $hours     = GDateTimeHelper::getHours($preferences->time_format);

        $this->cacheEventChannelEntries($events);

        $this->cached_vars = array_merge(
            $this->cached_vars,
            array(
                'week'       => $week,
                'hours'      => $hours,
                'days'       => $days,
                'schedule'   => $week,
                'events'     => $eventList,
                'now'        => new Carbon('now', DateTimeHelper::getUserTimezone()),
                'timeFormat' => $preferences->time_format,
                'dateFormat' => $preferences->date_format,
                'urls'       => $this->getURLs($date, $view),
                'type'       => $view,
                'title'      => "Week of " . $startDate->format('F j, Y'),
            )
        );

        return $this->mcp_view(
            array(
                'file'         => "calendar/$view",
                'highlight'    => "events/$view",
                'show_message' => false,
                'crumbs'       => array(
                    array(lang('calendar_by_week')),
                ),
            )
        );
    }

    /**
     * Returns the Day View string
     *
     * @param Collection $calendars
     * @param Carbon     $date
     *
     * @return string
     */
    private function getDayView(Collection $calendars, Carbon $date)
    {
        $view    = self::VIEW_DAY;
        $nowDate = new Carbon('now', DateTimeHelper::getUserTimezone());

        $preferences = PreferenceRepository::getInstance()->getOrCreate();

        $eventRepository = EventRepository::getInstance();

        $rangeStart = clone $date;
        $rangeStart->day -= 14;
        $rangeEnd = clone $date;
        $rangeEnd->setTime(23, 59, 59);

        $events    = $eventRepository->getFromCalendars($calendars, $rangeStart, $rangeEnd);
        $eventList = new EventList($events, $rangeStart, $rangeEnd);

        $this->cacheEventChannelEntries($events);

        $hours          = GDateTimeHelper::getHours($preferences->time_format);
        $translatedDays = GDateTimeHelper::getTranslatedDays($preferences->start_day);

        $this->cached_vars = array_merge(
            $this->cached_vars,
            array(
                'hours'      => $hours,
                'days'       => $translatedDays,
                'day'        => $date,
                'now'        => $nowDate,
                'events'     => $eventList,
                'timeFormat' => $preferences->time_format,
                'dateFormat' => $preferences->date_format,
                'urls'       => $this->getURLs($date, $view),
                'type'       => $view,
                'title'      => $date->format('F j, Y'),
            )
        );

        return $this->mcp_view(
            array(
                'file'         => "calendar/$view",
                'highlight'    => "events/$view",
                'show_message' => false,
                'crumbs'       => array(
                    array(lang('calendar_by_day')),
                ),
            )
        );
    }

    /**
     * A helper method for getting the dates of a request ton the module page
     *
     * @param int $year
     * @param int $month
     * @param int $day
     *
     * @return Carbon
     */
    private function getDateObject($year = null, $month = null, $day = null)
    {
        $now  = new Carbon('now', DateTimeHelper::TIMEZONE_UTC);
        $time = array(
            $year ?: $now->year,
            $month ?: $now->month,
            $day ?: $now->day,
        );

        return new Carbon(implode("-", $time), DateTimeHelper::TIMEZONE_UTC);
    }

    /**
     * Helper for all the date handling involved in setting up links
     *
     * @param Carbon $date
     * @param string $view - "month", "week", "day"
     *
     * @return array The list of URLs
     */
    private function getURLs(Carbon $date, $view = 'month')
    {
        $next = $date->copy();
        $prev = $date->copy();

        switch ($view) {
            case self::VIEW_WEEK:
                $next          = $next->addDays(7);
                $prev          = $prev->subDays(7);
                $prevLinkTitle = lang('prev');
                $nextLinkTitle = lang('next');
                break;
            case self::VIEW_DAY:
                $next          = $next->addDay();
                $prev          = $prev->subDay();
                $prevLinkTitle = $prev->format('F j');
                $nextLinkTitle = $next->format('F j');
                break;
            case self::VIEW_MONTH:
            default:
                $date->day     = 1;
                $next          = $next->addMonth();
                $prev          = $prev->subMonth();
                $prevLinkTitle = $prev->format('F');
                $nextLinkTitle = $next->format('F');
                break;
        }

        $now = $date->copy()->now();

        return array(
            'calendars'     => $this->mcp_link('calendars'),
            'calendarForm'  => $this->mcp_link('calendars/edit'),
            'today'         => $this->mcp_link("{$view}/{$now->year}/{$now->month}/{$now->day}"),
            'monthView'     => $this->mcp_link(self::VIEW_MONTH . "/{$now->year}/{$now->month}/{$now->day}"),
            'dayView'       => $this->mcp_link(self::VIEW_DAY . "/{$now->year}/{$now->month}/{$now->day}"),
            'weekView'      => $this->mcp_link(self::VIEW_WEEK . "/{$now->year}/{$now->month}/{$now->day}"),
            'eventSaveURL'  => $this->mcp_link('event'),
            'nextLink'      => $this->mcp_link("{$view}/{$next->year}/{$next->month}/{$next->day}"),
            'prevLink'      => $this->mcp_link("{$view}/{$prev->year}/{$prev->month}/{$prev->day}"),
            'prevLinkTitle' => $prevLinkTitle,
            'nextLinkTitle' => $nextLinkTitle,
        );
    }

    /**
     * Stores channel entry models indexed by their ID in the cached_vars
     * We need access to them from the templates
     *
     * @param Collection|Event[] $events
     */
    private function cacheEventChannelEntries(Collection $events)
    {
        $channelEntryIds = array();
        foreach ($events as $event) {
            $channelEntryIds[] = $event->entry_id;
        }

        if (empty($channelEntryIds)) {
            return;
        }

        $channelEntries = ee('Model')
            ->get('ChannelEntry')
            ->filter('entry_id', 'IN', $channelEntryIds)
            ->all()
            ->indexByIds();

        $this->cached_vars['channel_entries'] = $channelEntries;
    }

    /**
     * Does the heavy lifting of setting up and loading the assets
     */
    private function loadAssets()
    {
        $stylesheetPathList      = array(
            URL_THIRD_THEMES . 'calendar/css/solspace-fa.css',
            URL_THIRD_THEMES . 'calendar/css/calendar.module.css',
            URL_THIRD_THEMES . 'calendar/plugins/qtip2/jquery.qtip.min.css',
            URL_THIRD_THEMES . 'calendar/css/colorpicker.css',
        );
        $stylesheetIncludeString = '';
        foreach ($stylesheetPathList as $stylesheetPath) {
            $stylesheetIncludeString .= '<link rel="stylesheet" type="text/css" href="' . $stylesheetPath . '">';
        }


        $javascriptsPathList     = array(
            URL_THIRD_THEMES . 'calendar/js/calendar.module.js',
            URL_THIRD_THEMES . 'calendar/plugins/qtip2/jquery.qtip.min.js',
            URL_THIRD_THEMES . 'calendar/plugins/colorpicker/colorpicker.js',
        );
        $javascriptIncludeString = '';
        foreach ($javascriptsPathList as $scriptPath) {
            $javascriptIncludeString .= '<script src="' . $scriptPath . '"></script>';
        }

        ee()->cp->add_to_head($stylesheetIncludeString);
        ee()->cp->add_to_foot($javascriptIncludeString);
    }

    /**
     * Creates a sidebar and adds all elements to it
     */
    private function loadSidebar()
    {
        $navData = array(
            'events'         => array(
                'title'    => lang('calendar_events'),
                'sub_list' => array(
                    'month' => array(
                        'link'  => $this->mcp_link('month'),
                        'title' => lang('calendar_by_month'),
                    ),
                    'week'  => array(
                        'link'  => $this->mcp_link('week'),
                        'title' => lang('calendar_by_week'),
                    ),
                    'day'   => array(
                        'link'  => $this->mcp_link('day'),
                        'title' => lang('calendar_by_day'),
                    ),
                ),
            ),
            'calendars'      => array(
                'title'      => lang('calendar_calendars'),
                'link'       => $this->mcp_link('calendars'),
                'nav_button' => array(
                    'title' => lang('new'),
                    'link'  => $this->mcp_link('calendars/create'),
                ),
            ),
            'preferences'    => array(
                'title' => lang('calendar_preferences'),
                'link'  => $this->mcp_link('preferences'),
            ),
            'migration'      => array(
                'title' => lang('calendar_migration'),
                'link'  => $this->mcp_link('migration'),
            ),
            'migration_low'  => array(
                'title' => lang('calendar_migration_from_low'),
                'link'  => $this->mcp_link('migration_low'),
            ),
            'demo_templates' => array(
                'title' => lang('calendar_demo_templates'),
                'link'  => $this->mcp_link('code_pack'),
            ),
            'resources'      => array(
                'title'    => lang('calendar_resources'),
                'sub_list' => array(
                    'product_info'  => array(
                        'link'     => 'https://docs.solspace.com/expressionengine/calendar/',
                        'title'    => lang('calendar_product_info'),
                        'external' => true,
                    ),
                    'documentation' => array(
                        'link'     => $this->docs_url,
                        'title'    => lang('calendar_documentation'),
                        'external' => true,
                    ),
                    'support'       => array(
                        'link'     => 'https://solspace.com/expressionengine/support',
                        'title'    => lang('calendar_official_support'),
                        'external' => true,
                    ),
                ),
            ),
        );

        $migrationController = new MigrationController();
        if (!$migrationController->isMigrationPossible()) {
            unset($navData['migration']);
        }
        if (!$migrationController->isMigrationFromLowPossible()) {
            unset($navData['migration_low']);
        }


        $updates = null;
        $updateService = new UpdateService();
        if ($updateService->updateCount()) {
            $navData['updates'] = array(
                'title' => 'Updates Available (' . $updateService->updateCount() . ')',
                'link'  => $this->mcp_link('updates'),
            );
        }

        $this->set_nav($navData);
    }
}
