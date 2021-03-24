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
 * Handles the validation for a recurrence with a frequency of Monthly
 */
class MonthlyValidator extends RecurrenceValidator
{
    /**
     * Makes sure that the interval is set, then checks if it's a bymonthday or
     * by day recurrence, validating that data is set for those fields
     *
     * @return array An array of errors or an empty array
     */
    public function validate()
    {
        $v = new Validator($this->formData);

        $v->rule('required', 'interval')
          ->message(lang('calendar_interval_required'));

        $v->rule('integer', 'interval')
          ->message(lang('calendar_monthly_interval_incorrect'));

        $byMonthDayOrByDay = isset($this->formData['bymonthdayorbyday']) ? $this->formData['bymonthdayorbyday'] : null;
        if (isset($byMonthDayOrByDay) && $byMonthDayOrByDay === 'bymonthday') {
            $v->rule('required', 'bymonthday')
              ->message(lang('calendar_bymonthday_required'));

            $v->rule('range', 'bymonthday', range(1, 31))
              ->message(
                  lang('calendar_monthly_bymonthday_incorrect')
              );
        } else {
            $v->rule('required', 'byday')
              ->message(lang('calendar_byday_required'));

            $v->rule('range', 'byday', $this->bydayRange)
              ->message(lang('calendar_byday_incorrect'));

            $v->rule('in', 'bydayinterval', $this->bydayInterval)
              ->message(
                  lang('calendar_monthly_bydayinterval_incorrect')
              );
        }

        if (!$v->validate()) {
            return $v->errors();
        }

        return array();
    }
}
