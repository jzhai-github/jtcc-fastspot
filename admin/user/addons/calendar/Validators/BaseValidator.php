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
 * All validators inherit from this
 */
class BaseValidator
{
    protected $formData = array();
    protected $lang     = array();

    /**
     * To validate something you need the form data and an array of error
     * messages
     *
     * @param array $formData Submitted form data
     * @param array $language An array of language key:value pairs
     */
    public function __construct(array $formData, array $language)
    {
        Validator::langDir(PATH_THIRD . 'calendar/Library/Valitron/lang');
        ee()->lang->loadfile('calendar');
        $this->formData = $formData;
        $this->lang     = $language;
    }
}
