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

use EllisLab\ExpressionEngine\Service\Validation\Result;
use Solspace\Addons\Calendar\Model\Exclusion;
use Solspace\Addons\Calendar\Model\Preference;
use Solspace\Addons\Calendar\Repositories\PreferenceRepository;

/**
 * The exclusion controller handles all the fetching and mingling of
 * ExpressionEngine data with our model objects
 */
class PreferenceController
{
    /**
     * Returns a formatted array of form sections for rendering a shared EE form
     *
     * @param Preference $preference
     *
     * @return array
     */
    public function getFormVariables(Preference $preference)
    {
        $preferenceMemberGroups = PreferenceRepository::getInstance()->getMemberGroupIds($preference);
        $memberGroups           = ee('Model')
            ->get('MemberGroup')
            ->fields('group_id', 'group_title')
            ->filter('group_id', '!=', 1)
            ->all()
            ->getDictionary('group_id', 'group_title');

        $vars['sections'] = array(
            array(
                array(
                    'title'  => lang('calendar_date_and_time_format'),
                    'desc'   => lang('calendar_date_and_time_format_desc'),
                    'fields' => array(
                        'date_format' => array(
                            'type'     => 'select',
                            'choices'  => Preference::getDateFormats(),
                            'value'    => $preference->date_format,
                            'required' => true,
                        ),
                        'time_format' => array(
                            'type'     => 'select',
                            'choices'  => Preference::getTranslatedTimeFormats(),
                            'value'    => $preference->time_format,
                            'required' => true,
                        ),
                    ),
                ),
                array(
                    'title'  => 'calendar_start_day',
                    'desc'   => 'calendar_start_day_desc',
                    'fields' => array(
                        'start_day' => array(
                            'type'     => 'select',
                            'choices'  => array(
                                0 => lang('calendar_day_seven'),
                                1 => lang('calendar_day_one'),
                                2 => lang('calendar_day_two'),
                                3 => lang('calendar_day_three'),
                                4 => lang('calendar_day_four'),
                                5 => lang('calendar_day_five'),
                                6 => lang('calendar_day_six'),
                            ),
                            'value'    => $preference->start_day,
                            'required' => true,
                        ),
                    ),
                ),
                array(
                    'title'  => lang('calendar_overlap_threshold'),
                    'desc'   => lang('calendar_overlap_threshold_desc'),
                    'fields' => array(
                        'overlap_threshold' => array(
                            'type'     => 'select',
                            'choices'  => array(0, 1, 2, 3, 4, 5),
                            'value'    => $preference->overlap_threshold,
                            'required' => true,
                        ),
                    ),
                ),
                array(
                    'title'  => lang('calendar_time_interval'),
                    'desc'   => lang('calendar_time_interval_desc'),
                    'fields' => array(
                        'time_interval' => array(
                            'type'     => 'select',
                            'choices'  => array(15 => 15, 30 => 30, 60 => 60),
                            'value'    => $preference->time_interval,
                            'required' => true,
                        ),
                    ),
                ),
                array(
                    'title'  => lang('calendar_preference_duration'),
                    'desc'   => lang('calendar_preference_duration_desc'),
                    'fields' => array(
                        'duration' => array(
                            'type'     => 'select',
                            'choices'  => array(30 => 30, 60 => 60, 120 => 120),
                            'value'    => $preference->duration,
                            'required' => true,
                        ),
                    ),
                ),
                array(
                    'title'  => lang('calendar_preference_all_day'),
                    'desc'   => lang('calendar_preference_all_day_desc'),
                    'fields' => array(
                        'all_day' => array(
                            'type'     => 'yes_no',
                            'value'    => $preference->all_day ? 'y' : 'n',
                            'required' => true,
                        ),
                    ),
                ),
            ),
            'calendar_permissions' => array(
                array(
                    'title'   => 'calendar_allowed_member_groups_prefs',
                    'desc'    => 'calendar_allowed_member_groups_prefs_desc',
                    'caution' => true,
                    'fields'  => array(
                        'allowed_member_groups' => array(
                            'type'    => 'checkbox',
                            'choices' => $memberGroups,
                            'value'   => $preferenceMemberGroups,
                        ),
                    ),
                ),
            ),
        );

        // Final view variables we need to render the form
        $vars += array(
            'base_url'              => ee('CP/URL', 'addons/settings/calendar/preferences'),
            'cp_page_title'         => sprintf(
                '%s<br><i>%s</i>',
                lang('calendar_preferences'),
                lang('calendar_preferences_desc')
            ),
            'save_btn_text'         => 'btn_save_settings',
            'save_btn_text_working' => 'btn_saving',
        );

        return $vars;
    }

    /**
     * Saves posted data to Preference
     *
     * @param Preference $preference
     *
     * @return Exclusion[]
     */
    public function savePostedData(Preference $preference)
    {
        /** @var \EE_Input $input */
        $input = ee()->input;

        $dateFormat          = $input->post('date_format', true);
        $timeFormat          = $input->post('time_format', true);
        $startDay            = $input->post('start_day', true);
        $overlapThreshold    = $input->post('overlap_threshold', true);
        $timeInterval        = $input->post('time_interval', true);
        $duration            = $input->post('duration', true);
        $allDay              = $input->post('all_day', true);
        $allowedMemberGroups = $input->post('allowed_member_groups', true);

        if ($this->validatePostedData() === true) {
            $preference->date_format       = $dateFormat;
            $preference->time_format       = $timeFormat;
            $preference->start_day         = (int)$startDay;
            $preference->overlap_threshold = (int)$overlapThreshold;
            $preference->time_interval     = (int)$timeInterval;
            $preference->duration          = (int)$duration;
            $preference->all_day           = $allDay == "y";
            $preference->save();

            $allowedMemberGroups = ee('Model')
                ->get('MemberGroup')
                ->filter('group_id', 'IN', $allowedMemberGroups)
                ->all()
                ->getIds();

            ee()->db->delete('calendar_calendar_member_groups', array('id' => -($preference->id)));
            foreach ($allowedMemberGroups as $memberGroupId) {
                ee()->db->insert(
                    'calendar_calendar_member_groups',
                    array(
                        'group_id' => $memberGroupId,
                        'id'       => -($preference->id),
                    )
                );
            }

            ee('CP/Alert')
                ->makeInline('shared-form')
                ->asSuccess()
                ->withTitle(lang('success'))
                ->defer();

            ee()->functions->redirect(ee('CP/URL', 'addons/settings/calendar/preferences'));
        }
    }

    /**
     * Validates the posted data, returns the boolean result
     * Attaches alerts based on validation results
     *
     * @return bool
     */
    private function validatePostedData()
    {
        if (empty($_POST)) {
            return null;
        }

        $dateFormats = array_keys(Preference::getDateFormats());
        $timeFormats = array_keys(Preference::getTimeFormats());

        $rules = array(
            'date_format'       => 'required|enum[' . implode(',', $dateFormats) . ']',
            'time_format'       => 'required|enum[' . implode(',', $timeFormats) . ']',
            'overlap_threshold' => 'required|numeric|integer|isNatural|enum[0,1,2,3,4,5]',
        );

        /** @var Result $result */
        $result = ee('Validation')->make($rules)->validate($_POST);

        if ($result->failed()) {
            $errors = $result->getAllErrors();
            foreach ($errors as $field => $messages) {
                ee('CP/Alert')
                    ->makeInline('shared-form')
                    ->asIssue()
                    ->withTitle(lang('calendar_' . $field))
                    ->addToBody(implode("<br>", $messages))
                    ->now();
            }
        } else {
            ee('CP/Alert')
                ->makeInline('shared-form')
                ->asIssue()
                ->addToBody(lang('calendar_saved'))
                ->now();
        }

        return $result->isValid();
    }
}
