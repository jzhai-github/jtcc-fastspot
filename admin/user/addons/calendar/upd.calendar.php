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

use Solspace\Addons\Calendar\Library\AddonBuilder;

class Calendar_upd extends AddonBuilder
{
    private static $legacyTablePrefix     = 'calendar_';
    private static $legacyTableSuffix     = '_legacy';
    private static $legacyTables          = array(
        'calendars',
        'events',
        'events_exceptions',
        'events_rules',
        'preferences',
    );
    private static $removableLegacyTables = array(
        'events_imports',
        'events_occurrences',
        'permissions_groups',
        'permissions_preferences',
        'permissions_users',
        'reminders',
        'reminders_templates',
    );

    public $hookList = array(
        'before_channel_entry_delete',
    );

    public $module_actions = array(
        'createOrUpdateCalendar',
        'deleteCalendar',
        'showCalendarForm',
        'listCalendars',
    );

    public $public_actions = array(
        'icsSubscription',
    );

    /**
     * @return array
     */
    public static function getLegacyTables()
    {
        return self::$legacyTables;
    }

    /**
     * Prefixes and suffixes all table names and returns the list
     * @return array
     */
    public static function getLegacyTablesRealNames()
    {
        $tables = self::$legacyTables;

        $dbPrefix = ee()->db->dbprefix;
        foreach ($tables as &$table) {
            $table = $dbPrefix . self::$legacyTablePrefix . $table . self::$legacyTableSuffix;
        }

        return $tables;
    }

    /**
     * @return string
     */
    public static function getLegacyTableSuffix()
    {
        return self::$legacyTableSuffix;
    }

    /**
     * @return bool Whether it installed
     */
    public function install()
    {
        // Already installed, let's not install again.
        $existingVersion = $this->database_version();
        if ($existingVersion !== false && version_compare($existingVersion, '2.0.0', '>=')) {
            return false;
        }

        if ($this->default_module_install() == false) {
            return false;
        }

        $data = array(
            'module_name'        => $this->class_name,
            'module_version'     => $this->version,
            'has_cp_backend'     => 'y',
            'has_publish_fields' => 'n',
        );

        ee()->db->insert('modules', $data);

        foreach ($this->hookList as $hook) {
            ee()->db->insert(
                'extensions',
                array(
                    'class'    => $this->class_name . '_ext',
                    'method'   => $hook,
                    'hook'     => $hook,
                    'settings' => '',
                    'priority' => 5,
                    'version'  => $this->version,
                    'enabled'  => 'y',
                )
            );
        }

        foreach ($this->module_actions as $action) {
            ee()->db->insert(
                'actions',
                array(
                    'method' => $action,
                    'class'  => 'Calendar_mcp',
                )
            );
        }

        foreach ($this->public_actions as $action) {
            $csrfExempt = 0;
            if ($action === 'icsSubscription') {
                $csrfExempt = 1;
            }

            ee()->db->insert(
                'actions',
                array(
                    'method' => $action,
                    'class'  => 'Calendar',
                    'csrf_exempt' => $csrfExempt,
                )
            );
        }

        return true;
    }

    /**
     * Does nothing currently
     *
     * @param  string $current_version The current version #
     *
     * @return Bool Did the update work?
     */
    public function update($current_version = null)
    {
        if ($current_version == $this->version) {
            return false;
        }

        // If the old version is older than v2
        // We rename the old tables to legacy tables
        if (version_compare($current_version, 2, '<')) {
            $dbPrefix = ee()->db->dbprefix;

            $alteration = '';
            foreach (self::$legacyTables as $tableName) {
                $alteration .= sprintf(
                    "ALTER TABLE %s RENAME TO %s;\n",
                    $dbPrefix . self::$legacyTablePrefix . $tableName,
                    $dbPrefix . self::$legacyTablePrefix . $tableName . self::$legacyTableSuffix
                );
            }

            foreach (self::$removableLegacyTables as $tableName) {
                $alteration .= sprintf(
                    "DROP TABLE %s;\n",
                    $dbPrefix . self::$legacyTablePrefix . $tableName
                );
            }

            ee()->db->query($alteration);

            $this->install();
        } else {

            if (version_compare($current_version, '2.3.0', '<')) {
                $sql = 'ALTER TABLE exp_calendar_calendars ADD COLUMN `ics_hash` VARCHAR(100) DEFAULT NULL AFTER `description`';
                ee()->db->query($sql);

                $sql = 'ALTER TABLE exp_calendar_calendars ADD INDEX `ics_hash` (`ics_hash`)';
                ee()->db->query($sql);

                ee()->db->insert(
                    'actions',
                    array(
                        'method'      => 'icsSubscription',
                        'class'       => 'Calendar',
                        'csrf_exempt' => 1,
                    )
                );
            }

            if (version_compare($current_version, '3.1.0', '<')) {
                $sql = 'CREATE TABLE IF NOT EXISTS `exp_calendar_select_dates` (
                            `id`       INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                            `event_id` INT(10) UNSIGNED          DEFAULT NULL,
                            `date`     DATE             NOT NULL,
                            PRIMARY KEY (`id`),
                            UNIQUE KEY `event_id_date_index` (`event_id`, `date`),
                            KEY `event_id` (`event_id`)
                        ) CHARACTER SET utf8 COLLATE utf8_general_ci ;;';
                ee()->db->query($sql);

                $sql = "ALTER TABLE exp_calendar_events ADD COLUMN `select_dates` TINYINT(1) DEFAULT '0' AFTER `all_day`";
                ee()->db->query($sql);
            }

        }

        ee()->db->update(
            'modules',
            array('module_version' => $this->version),
            array('module_name' => $this->class_name)
        );

        return true;
    }

    /**
     * Removes and drops all tables
     * @return bool Did the uninstall work?
     */
    public function uninstall()
    {
        // Cannot uninstall what does not exist, right?
        if ($this->database_version() === false) {
            return false;
        }

        if ($this->default_module_uninstall() == false) {
            return false;
        }

        return true;
    }
}
