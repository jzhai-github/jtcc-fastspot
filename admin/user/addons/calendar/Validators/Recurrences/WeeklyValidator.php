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

namespace Solspace\Addons\Calendar\Validators\Recurrences;

use Solspace\Addons\Calendar\Library\Valitron\Validator as Validator;
use Solspace\Addons\Calendar\Validators\RecurrenceValidator as RecurrenceValidator;

/**
 * Handles the validation for a recurrence with a frequency of Weekly
 */
class WeeklyValidator extends RecurrenceValidator
{
    /**
     * Makes sure the interval is set, and that byday is set and the integer
     * passed to us is valid for the days of the week
     *
     * @return array An array of errors or an empty array
     */
    public function validate()
    {
        $v = new Validator($this->formData);

        $v->rule('required', 'interval')
          ->message(lang('calendar_interval_required'));

        $v->rule('integer', 'interval')
          ->message(lang('calendar_weekly_interval_incorrect'));

        $v->rule('required', 'byday')
          ->message(lang('calendar_byday_required'));

        $v->rule('range', 'byday', $this->bydayRange)
          ->message(lang('calendar_byday_incorrect'));

        if (!$v->validate()) {
            return $v->errors();
        }

        return array();
    }
}
