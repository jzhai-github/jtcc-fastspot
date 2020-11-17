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
 * Handles the validation for a recurrence with a frequency of Yearly
 */
class YearlyValidator extends RecurrenceValidator
{
    /**
     * Checks to make sure the interval is set and that bymonth is set and
     * within the correct range of numbers. If the isbyday box is checked
     * will validate those values.
     *
     * @return array An array of errors or an empty array
     */
    public function validate()
    {
        $v = new Validator($this->formData);

        $v->rule('required', 'interval')
          ->message(lang('calendar_interval_required'));

        $v->rule('integer', 'interval')
          ->message(lang('calendar_yearly_interval_incorrect'));

        if (isset($this->formData['isbyday'])) {

            $v->rule('required', 'bymonth')
              ->message(lang('calendar_bymonth_required'));

            $v->rule('range', 'bymonth', range(1, 12))
              ->message(lang('calendar_yearly_bymonth_incorrect'));

            $v->rule('in', 'bydayinterval', $this->bydayInterval)
              ->message(lang('calendar_byday_required'));

            $v->rule('required', 'byday')
              ->message(lang('calendar_byday_required'));

            $v->rule('range', 'byday', $this->bydayRange)
              ->message(lang('calendar_byday_incorrect'));

        }

        if (!$v->validate()) {
            return $v->errors();
        }

        return array();
    }
}
