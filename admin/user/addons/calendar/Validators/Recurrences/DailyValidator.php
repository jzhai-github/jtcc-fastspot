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
 * Handles recurrence validation for a frequency of daily
 */
class DailyValidator extends RecurrenceValidator
{
    /**
     * Makes sure that the interval is set
     *
     * @return array An array of errors or an empty array
     */
    public function validate()
    {
        $v = new Validator($this->formData);

        $v->rule('required', 'interval')
          ->message(lang('calendar_interval_required'));

        $v->rule('integer', 'interval')
          ->message(lang('calendar_daily_interval_incorrect'));

        if (!$v->validate()) {
            return $v->errors();
        }

        return array();
    }
}
