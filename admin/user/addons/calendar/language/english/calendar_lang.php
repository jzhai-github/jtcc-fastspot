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

$lang = array(
    'calendar_module_name'            => 'Calendar',
    'calendar_module_description'     =>
        'Create full-featured calendars and recurring events.',

    /**
     * Fieldtype
     */
    'calendar_all_day'                => 'All Day',
    'calendar_field_options'          => 'Calendar field options',
    'calendar_default_calendar'       => 'Default Calendar',
    'calendar_default_calendar_desc'  => 'Select the default calendar to be selected when creating new events. The user will be allowed to change this to other calendars they have permissions to when creating events.',
    'calendar_no_calendars_present'   => 'There are no calendars. Click <a href="%link%">here</a> to create one.',
    'calendar_no_permissions'         => 'You don\'t have permissions to edit this event',
    'calendar_fieldtype_error'        => 'There was an error while creating Field Type',
    'calendar_fieldtype_no_calendar'  => 'You must have a default calendar specified for this Field Type',
    'calendar_description_field'      => 'ICS Export Description field (optional)',
    'calendar_description_field_desc' => 'Select which channel field will contain the event description when using Calendar:ICS_Export tag.',
    'calendar_location_field'         => 'ICS Export Location field (optional)',
    'calendar_location_field_desc'    => 'Select which channel field will contain the event location when using Calendar:ICS_Export tag.',
    'calendar_no_field'               => 'Do not use this field',

    // Used for switches
    'calendar_yes'                    => 'Yes',
    'calendar_no'                     => 'No',
    'calendar_starts'                 => 'Starts',
    'calendar_ends'                   => 'Ends',
    'calendar_event_form_success'     => 'Event saved',
    'calendar_event_delete_confirm'   => 'Delete this event?',
    'calendar_event_deleted_success'  => 'Event deleted',
    'calendar_occurence_deleted'      => 'Occurence deleted',

    // Recurrence
    'calendar_is_repeating'           => 'Repeats',
    'calendar_frequency'              => 'Frequency',
    'calendar_yearly'                 => 'Yearly',
    'calendar_monthly'                => 'Monthly',
    'calendar_weekly'                 => 'Weekly',
    'calendar_daily'                  => 'Daily',
    'calendar_dates'                  => 'Select Dates',
    'calendar_years'                  => 'Year(s)',
    'calendar_months'                 => 'Month(s)',
    'calendar_weeks'                  => 'Week(s)',
    'calendar_days'                   => 'Day(s)',

    // Recurrence Interval
    'calendar_repeats_every'          => 'Every',
    'calendar_each'                   => 'Each', // Each day
    'calendar_days'                   => 'day(s)',
    'calendar_years_on'               => 'year(s)',
    'calendar_on_the'                 => 'On the',
    'calendar_weeks_on'               => 'week(s) on',
    'calendar_months_on'              => 'month(s)',

    // ByDay Interval
    'calendar_first'                  => 'First',
    'calendar_second'                 => 'Second',
    'calendar_third'                  => 'Third',
    'calendar_fourth'                 => 'Fourth',
    'calendar_last'                   => 'Last',

    'calendar_until'                             => 'Until',
    'calendar_except'                            => 'Except on',
    'calendar_select_dates'                      => 'Select dates',

    /**
     * Module
     */
    'calendar_events'                            => 'Events',
    'calendar_day'                               => 'Day',
    'calendar_week'                              => 'Week',
    'calendar_month'                             => 'Month',
    'calendar_calendars'                         => 'Calendars',
    'calendar_calendar'                          => 'Calendar',
    'calendar_today'                             => 'Today',
    'calendar_add_calendar'                      => 'Create Calendar',
    'calendar_calendar_create'                   => 'Create Calendar',
    'calendar_calendar_edit'                     => 'Edit Calendar',
    'calendar_by_month'                          => 'by Month',
    'calendar_by_week'                           => 'by Week',
    'calendar_by_day'                            => 'by Day',
    'calendar_preferences'                       => 'Preferences',
    'calendar_preferences_desc'                  => 'Date & time handling and formatting',
    'calendar_demo_templates'                    => 'Demo Templates',
    'calendar_resources'                         => 'Resources',
    'calendar_product_info'                      => 'Product Info',
    'calendar_documentation'                     => 'Documentation',
    'calendar_official_support'                  => 'Official Support',
    'calendar_time_from_date_separator'          => ' at ',
    'calendar_more_event_count'                  => '+%count% more',
    'calendar_overlap_threshold'                 => 'Date Overlap Threshold',
    'calendar_overlap_threshold_desc'            => 'Specify the amount of hours in the next day which are still considered as those of the previous day. This prevents late night events from showing up the next day in calendars.',
    'calendar_preferences_saved'                 => 'Your preferences have been saved successfully.',
    'calendar_time_interval'                     => 'Time Interval',
    'calendar_time_interval_desc'                => 'The time picker interval for fieldtype, in minutes.',
    'calendar_preference_all_day'                => 'Select \'All Day\' by default?',
    'calendar_preference_all_day_desc'           => 'Select \'All Day\' option in fieldtype by default.',
    'calendar_preference_duration'               => 'Event Duration',
    'calendar_preference_duration_desc'          => 'The default event time duration for fieldtype, in minutes.',
    'calendar_warning_will_delete_entries'       => 'WARNING: Deleting calendar(s) will also permanently delete all associated event channel entries!',

    // Migration
    'calendar_migration'                         => 'Migration',
    'calendar_migration_desc'                    => 'This tool allows you to migrate your Calendar 1.x events and calendars data to Calendar 2.x.',
    'calendar_migration_complete'                => 'Migration completed successfully',
    'calendar_legacy_calendars'                  => 'Calendars to be migrated',
    'calendar_legacy_calendars_desc'             => 'The calendars listed will be migrated to the new Calendar version.',
    'calendar_migrate'                           => 'Start migration',
    'calendar_migrating'                         => 'Migrating...',
    'calendar_legacy_events'                     => 'Events to be migrated',
    'calendar_legacy_events_desc'                => 'The number of events that will be migrated to the new Calendar version.',
    'calendar_migration_cleanup'                 => 'Remove Calendar 1.x Data',
    'calendar_migration_cleanup_confirm'         => 'WARNING: By clicking "OK" you agree to remove all Calendar 1.x legacy data. This action cannot be undone.',
    'calendar_migration_from_low'                => 'Migrate from Low Events',
    'calendar_migration_from_low_desc'           => 'This tool allows you to migrate your Low Events events to Calendar 2.x.',
    'calendar_low_no_events'                     => 'No Low Events found',
    'calendar_low_events'                        => 'Events found from Low Events',
    'calendar_low_events_desc'                   => 'A list of all events found from Low Events. Events grayed out with red message means there is currently no calendar field created yet to migrate.',
    'calendar_low_no_default_calendar'           => 'No calendar set as field default. <a href="%link%">Set default field here</a>',
    'calendar_low_no_calendar_field_present'     => 'No Calendar field found. <a href="%link%">Create field here</a>',
    'calendar_events_migrated'                   => 'Successfully migrated %count% events.',
    'calendar_cleanup_successful'                => 'Calendar 1.x data cleaned up successfully',

    // TODO: Maybe, we prevent the utilities sidebar thing from showing up if the migration is not possible?
    'calendar_migration_not_possible'            => 'Calendar migration not possible',
    'calendar_migration_not_possible_desc'       => 'Please be sure to follow the steps provided in the documentation.',

    // Calendar list
    'calendar_delete_calendars'                  => 'Submit',
    'calendar_remove'                            => 'Remove',
    'calendar_remove_calendars_plural'           => 'calendars',
    'calendar_no_calendars'                      => 'There are currently no calendars yet.',
    'calendar_create_calendar'                   => 'Create a calendar',
    'calendar_enable_ics'                        => 'Share this calendar',
    'calendar_disable_ics'                       => 'Stop sharing this calendar',
    'calendar_copy_ics_url'                      => 'Copy ICS url',

    // Calendar Form
    'calendar_name'                              => 'Name',
    'calendar_short_name'                        => 'Short name',
    'calendar_description'                       => 'Description',
    'calendar_description_desc'                  => 'A description or information about the calendar.',
    'calendar_color'                             => 'Color',
    'calendar_color_desc'                        => 'Enter a Hex color code or use the picker to select one.',
    'calendar_timeformat'                        => 'Time Format',
    'calendar_dateformat'                        => 'Date Format',
    'calendar_start_day_header'                  => 'Start day',
    'calendar_start_day'                         => 'First Day of Week',
    'calendar_start_day_desc'                    => 'Controls handling, output and display in control panel and EE template tags.',
    'calendar_clocktype'                         => 'ClockType',
    'calendar_24_hour'                           => '24-hour (HH:MM)',
    'calendar_12_hour'                           => '12-hour (hh:mm tt)',
    'calendar_delete'                            => 'Delete',
    'calendar_save'                              => 'Save',
    'calendar_event_form_confirm'                => 'Are you sure you want to delete this event?',
    'calendar_calendar_error'                    => 'There were errors',
    'calendar_calendar_success'                  => 'Your calendar has been saved',
    'calendar_calendar_confirm_delete'           => 'Are you sure you want to delete this calendar? This will remove all events, recurrences and even channel fields attached to this calendar/-s.',
    'calendar_calendar_deleted'                  => 'Calendar successfully deleted',
    'calendar_calendars_deleted'                 => '%count% calendars successfully deleted',
    'calendar_date_and_time_settings'            => 'Date & Time settings',
    'calendar_date_and_time_format'              => 'Date & Time format',
    'calendar_date_and_time_format_desc'         => 'Controls display and handling of date and time in control panel and Channel Form only.',
    'calendar_permissions'                       => 'Permissions',
    'calendar_allowed_member_groups'             => 'Allowed Member Groups',
    'calendar_allowed_member_groups_desc'        => 'These are the member groups allowed to assign events to this calendar. If a member group is not assigned to any calendars at all, they will not be able to see/use the Calendar widget in EE control panel or Channel Form at all. Super Admins are always allowed.',
    'calendar_allowed_member_groups_prefs'       => 'Default Allowed Member Groups for new Calendars',
    'calendar_allowed_member_groups_prefs_desc'  => 'This is a preference that allows you to control the default member groups assigned when creating new calendars. This is simply a feature that makes creating several calendars a bit faster. It is also not a retroactive setting.',


    /**
     * Error Messages
     */
    'calendar_date_error'                        => 'You\'ve entered an invalid date.',
    'calendar_date_difference_error'             => 'Your end date must be after the start date.',
    'calendar_time_difference_error'             => 'Your end time must be after the start time.',
    'calendar_end_day_is_missing'                => 'You need to select a valid end day.',
    'calendar_delete_trouble'                    => 'There was trouble deleting your event.',
    'calendar_required'                          => 'This field is required',

    // Tag errors
    'calendar_tag_date_format_incorrect'         => 'Could not parse the given date format: "%s". Please check the documentation for help.',

    // Recurrence errors\
    'calendar_frequency_is_invalid'              => 'You\'ve somehow managed to use an invalid frequency. Please select from the dropdown and try again.',
    'calendar_interval_required'                 => 'You must choose a numeric interval for the repeated event to occur.',
    'calendar_invalid_recursion'                 => 'Your start date must be on a day of your repeated events. For example, if you want something to repeat on every Tuesday and Saturday, your start date must be either a Tuesday or Saturday.',
    'calendar_byday_required'                    => 'You must choose a day of the week for the event to recur on.',
    'calendar_bymonth_required'                  => 'You must choose a month for the event to repeat on.',
    'calendar_bymonthday_required'               => 'You must choose a day of the month for the event to repeat on.',
    'calendar_daily_interval_incorrect'          => 'Your daily interval must be a number and greater than 0.',
    'calendar_weekly_interval_incorrect'         => 'Your weekly interval must be a number and greater than 0.',
    'calendar_monthly_interval_incorrect'        => 'Your monthly interval must be a number and greater than 0.',
    'calendar_yearly_interval_incorrect'         => 'Your yearly interval must be a number and greater than 0.',
    'calendar_byday_incorrect'                   => 'Please select the days you\'d like your event to repeat on.',
    'calendar_monthly_bymonthday_incorrect'      => 'Please select a correct day or day of the monthy for your monthly recurrence.',
    'calendar_yearly_bymonth_incorrect'          => 'Please select a correct month or months of the year for your yearly recurrence.',
    'calendar_monthly_bydayinterval_incorrect'   => 'Please reselect which period you\'d like your monthly recurrence to happen: e.g. "first", "second".',
    'calendar_until_required'                    => 'The \'Until\' field is required if your event repeats.',
    'calendar_until_is_date'                     => '\'Until\' field must have a valid date.',
    'calendar_until_before_start_date'           => '\'Until\' field date must be at least a day after the start date.',
    'calendar_until_empty'                       => 'You must choose a date for the \'Until\' field.',
    'calendar_submit_error'                      => 'We are unable to save your calendar.',

    // Calendar Errors
    'calendar_all_fields_required'               => '{field} is required.',
    'calendar_calendar_url_title_bad_characters' => 'No spaces. Underscores and dashes are allowed.',
    'calendar_color_not_in_colors'               => 'The color you selected is invalid.',
    'calendar_clock_type_incorrect'              => 'The clock type you\'ve chosen is incorrect.',
    'calendar_date_format_incorrect'             => 'The date format you\'ve chosen is incorrect.',
    'calendar_start_day_calendar_incorrect'      => 'The calendar start day you\'ve chosen is not a valid day of the week.',


    // Exclusion errors
    'calendar_exclusion_required'                => 'You must submit a date to exclude.',
    'calendar_exclusion_invalid_date'            => 'The date you\'ve excluded is invalid.',
    /**
     * Month and Days
     */
    'calendar_first_month'                       => 'January',
    'calendar_second_month'                      => 'February',
    'calendar_third_month'                       => 'March',
    'calendar_fourth_month'                      => 'April',
    'calendar_fifth_month'                       => 'May',
    'calendar_sixth_month'                       => 'June',
    'calendar_seventh_month'                     => 'July',
    'calendar_eighth_month'                      => 'August',
    'calendar_ninth_month'                       => 'September',
    'calendar_tenth_month'                       => 'October',
    'calendar_eleventh_month'                    => 'November',
    'calendar_twelfth_month'                     => 'December',

    'calendar_first_month_abbr'    => 'Jan',
    'calendar_second_month_abbr'   => 'Feb',
    'calendar_third_month_abbr'    => 'Mar',
    'calendar_fourth_month_abbr'   => 'Apr',
    'calendar_fifth_month_abbr'    => 'May',
    'calendar_sixth_month_abbr'    => 'Jun',
    'calendar_seventh_month_abbr'  => 'Jul',
    'calendar_eighth_month_abbr'   => 'Aug',
    'calendar_ninth_month_abbr'    => 'Sep',
    'calendar_tenth_month_abbr'    => 'Oct',
    'calendar_eleventh_month_abbr' => 'Nov',
    'calendar_twelfth_month_abbr'  => 'Dec',

    'calendar_day_one'   => 'Monday',
    'calendar_day_two'   => 'Tuesday',
    'calendar_day_three' => 'Wednesday',
    'calendar_day_four'  => 'Thursday',
    'calendar_day_five'  => 'Friday',
    'calendar_day_six'   => 'Saturday',
    'calendar_day_seven' => 'Sunday',

    'calendar_day_one_abbr'   => 'Mo',
    'calendar_day_two_abbr'   => 'Tu',
    'calendar_day_three_abbr' => 'We',
    'calendar_day_four_abbr'  => 'Th',
    'calendar_day_five_abbr'  => 'Fr',
    'calendar_day_six_abbr'   => 'Sa',
    'calendar_day_seven_abbr' => 'Su',

    'calendar_day_one_abbr_1'   => 'M',
    'calendar_day_two_abbr_1'   => 'T',
    'calendar_day_three_abbr_1' => 'W',
    'calendar_day_four_abbr_1'  => 'T',
    'calendar_day_five_abbr_1'  => 'F',
    'calendar_day_six_abbr_1'   => 'S',
    'calendar_day_seven_abbr_1' => 'S',

    'calendar_day_one_abbr_3'   => 'Mon',
    'calendar_day_two_abbr_3'   => 'Tue',
    'calendar_day_three_abbr_3' => 'Wed',
    'calendar_day_four_abbr_3'  => 'Thu',
    'calendar_day_five_abbr_3'  => 'Fri',
    'calendar_day_six_abbr_3'   => 'Sat',
    'calendar_day_seven_abbr_3' => 'Sun',


    // -------------------------------------
    //	demo install (code pack)
    // -------------------------------------

    'demo_description'                 => 'Install these templates to help you better understand how this add-on works.',
    'template_group_prefix'            => 'Template Group Prefix',
    'template_group_prefix_desc'       => 'Each template group installed will be prefixed with this variable in order to prevent a naming collision.',
    'groups_and_templates'             => "Template Groups and Templates to be Installed",
    'groups_and_templates_desc'        => "These template groups and their accompanying templates will be installed into your ExpressionEngine site.",
    'screenshot'                       => 'Screenshot',
    'install_demo_templates'           => 'Install Demo Templates',
    'prefix_error'                     => 'Prefixes, which are used for template groups, may only contain alpha-numeric characters, underscores, and dashes.',
    'demo_templates'                   => 'Demo Templates',

    //errors
    'ee_not_running'                   => 'ExpressionEngine 2.x does not appear to be running.',
    'invalid_code_pack_path'           => 'Invalid Demo Templates path',
    'invalid_code_pack_path_exp'       => 'No valid Demo Templates found at \'%path%\'.',
    'missing_code_pack'                => 'Demo Templates missing',
    'missing_code_pack_exp'            => 'You have chosen no Demo Templates to install.',
    'missing_prefix'                   => 'Prefix needed',
    'missing_prefix_exp'               => 'Please provide a prefix for the sample templates and data that will be created.',
    'invalid_prefix'                   => 'Invalid prefix',
    'invalid_prefix_exp'               => 'The prefix you provided was not valid.',
    'missing_theme_html'               => 'Missing folder',
    'missing_theme_html_exp'           => 'There should be a folder called \'html\' inside your site\'s \'/themes/solspace_themes/code_pack/%code_pack_name%\' folder. Make sure that it is in place and that it contains additional folders that represent the template groups that will be created by this code pack.',
    'missing_codepack_legacy'          => 'Missing the CodePack Legacy library needed to install this legacy codepack.',

    //@deprecated
    'missing_code_pack_theme'          => 'Demo Templates themes missing',
    'missing_code_pack_theme_exp'      => 'There should be at least one theme folder inside the folder \'%code_pack_name%\' located inside \'/themes/code_pack/\'. A theme is required to proceed.',

    //conflicts
    'conflicting_group_names'          => 'Conflicting template group names',
    'conflicting_group_names_exp'      => 'The following template group names already exist. Please choose a different prefix in order to avoid conflicts. %conflicting_groups%',
    'conflicting_global_var_names'     => 'Conflicting global variable names.',
    'conflicting_global_var_names_exp' => 'There were conflicts between global variables on your site and global variables in this code pack. Consider changing your prefix to resolve the following conflicts. %conflicting_global_vars%',

    //success messages
    'global_vars_added'                => 'Global variables added',
    'global_vars_added_exp'            => 'The following global template variables were successfully added. %global_vars%',
    'templates_added'                  => 'Templates were added',
    'templates_added_exp'              => '%template_count% templates were successfully added to your site as part of these Demo Templates.',
    "home_page"                        => "Home Page",
    "home_page_exp"                    => "View the home page for these Demo Templates.",
);
