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
 * The exclusion validator
 */
class ExclusionValidator extends BaseValidator
{
    /**
     * Checks to make sure all required exclusion data is sent
     *
     * @return array An array of errors or an empty array
     */
    public function validate()
    {
        $v = new Validator($this->formData);

        $v->rule('required', 'exclusion')
          ->message(lang('calendar_exclusion_required'));

        $v->rule('date', 'exclusion')
          ->message(lang('calendar_exclusion_invalid_date'));

        if (!$v->validate()) {
            return $v->errors();
        }

        return array();
    }
}
