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

require_once __DIR__ . '/helper_functions.php';
require_once __DIR__ . '/Library/EEDateLangShim.php';

//fixes name collision on EE's built in lang files
if (class_exists('Solspace\Addons\Calendar\Library\EEDateLangShim'))
{
	Solspace\Addons\Calendar\Library\EEDateLangShim::loadLang();
}

return array(
    'author'              => 'Solspace',
    'author_url'          => 'https://docs.solspace.com/expressionengine/calendar/',
    'docs_url'            => 'https://docs.solspace.com/expressionengine/calendar/v3/',
    'name'                => 'Calendar',
    'module_name'         => 'Calendar',
    'description'         => 'Create full-featured calendars and recurring events.',
    'version'             => '3.2.1',
    'namespace'           => 'Solspace\Addons\Calendar',
    'settings_exist'      => true,
    'models'              => array(
        'Calendar'      => 'Model\CalendarModel',
        'Event'         => 'Model\Event',
        'Exclusion'     => 'Model\Exclusion',
        'SelectDate'    => 'Model\SelectDate',
        'Preference'    => 'Model\Preference',
    ),
    'models.dependencies' => array(
        'Event' => array('ee:ChannelEntry'),
    ),
);
