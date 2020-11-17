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

namespace Solspace\Addons\Calendar\Validators;

use Solspace\Addons\Calendar\Library\Valitron\Validator as Validator;

/**
 * Handles submitted event form data
 */
class EventValidator extends BaseValidator
{
    /**
     * Checks to make sure all required event data is sent
     *
     * @return array An array of errors or an empty array
     */
    public function validate()
    {
        $v = new Validator($this->formData);

        $v->rule('required', 'end_day')
          ->message(lang('calendar_end_day_is_missing'));

        $v->rule('date', array('start_date', 'end_date'))
          ->message(lang('calendar_date_error'));

        $v->rule('dateBefore', 'start_date', $this->formData['end_date'])
          ->message(lang('calendar_date_difference_error'));

        if (!$v->validate()) {
            return $v->errors();
        }

        return array();
    }
}
