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

use Solspace\Addons\Calendar\Library\RRule\RRule as RRule;
use Solspace\Addons\Calendar\Library\Valitron\Validator as Validator;
use Solspace\Addons\Calendar\Library\When\InvalidStartDate as InvalidStartDate;
use Solspace\Addons\Calendar\Library\When\When as When;

/**
 * Loads the complex recurrence validation information and dynamically
 * loads a recurrence type (monthly, weekly, etc) validator
 */
class RecurrenceValidator extends BaseValidator
{
    protected $bydayRange    = array('MO', 'TU', 'WE', 'TH', 'FR', 'SA', 'SU');
    protected $frequencies   = array('daily', 'weekly', 'monthly', 'yearly');
    protected $bydayInterval = array(1, 2, 3, 4, -1);

    private $errors = array();

    public function __construct(array $formData, array $language)
    {
        parent::__construct($formData, $language);
        $this->addRangeRule();
    }

    /**
     * Loads the required validator class for the recursion frequency and
     * checks standard issue stuff in recurrences such as a Until, etc.
     *
     * @return array An array of errors or an empty array
     */
    public function validate()
    {
        $this->errors = $this->validateGlobalRecurrenceFields();
        $this->errors += $this->validateFrequency();

        if (isset($this->formData['repeats']) && empty($this->errors)) {
            $this->errors += $this->validateRecurrenceStartDate();
        }

        return $this->errors;
    }

    /**
     * Makes sure that a frequency validator exists then instantiates it and
     * returns errors
     *
     * @return array Errors or an empty array if none
     */
    public function validateFrequency()
    {
        $v = new Validator($this->formData);

        $v->rule('in', 'freq', array('daily', 'weekly', 'monthly', 'yearly', 'dates'))
          ->message(lang('calendar_frequency_is_invalid'));

        $freq      = $this->formData['freq'];
        $freqClass = 'Solspace\Addons\Calendar\Validators\Recurrences\\'
            . ucwords($freq) . "Validator";

        if (!isset($this->formData[$freq])) {
            $this->formData[$freq] = array();
        }

        $this->formData[$freq]['interval'] = $this->formData['interval'];

        if ($v->validate()) {
            $formData  = $this->formData[$freq];
            $validator = new $freqClass(
                $formData,
                $this->lang
            );

            return $validator->validate();
        } else {
            return $v->errors();
        }
    }

    /**
     * Handles the global recurrence field validation such as Until, etc.
     *
     * @return array An array of errors or an empty array
     */
    public function validateGlobalRecurrenceFields()
    {
        $v = new Validator($this->formData);

        $v->rule('required', 'until')
          ->message(lang('calendar_until_required'));

        $v->rule('date', 'until')
          ->message(lang('calendar_until_is_date'));

        $v->rule('dateAfter', 'until', $this->formData['start_date'])
          ->message(lang('calendar_until_before_start_date'));

        if (!$v->validate()) {
            return $v->errors();
        }

        return array();
    }

    /**
     * Makes sure the start date and the recurrence are in congruence. That,
     * for example, the start date is on a Tuesday that the recurrence rule has
     * a Tuesday included.
     *
     * @return array An array of errors or an empty array
     */
    private function validateRecurrenceStartDate()
    {
        try {
            $rrule = RRule::create($this->formData);

            $r = new When();

            $r->startDate(new \DateTime($this->formData['start_date']))
              ->rrule($rrule->getRRule())
              ->generateOccurrences();
        } catch (InvalidStartDate $e) {
            return array(
                'rrule_error' =>
                    array(lang('calendar_invalid_recursion')),
            );
        }

        return array();
    }

    /**
     * A recurrence-specific rule that we add to the validator which allows us
     * to check if a field with an array for values, such as byday, matches up
     * to a given range or other array
     *
     * This is untested
     */
    private function addRangeRule()
    {
        $rangeValidator = function ($field, $values, $list) {
            $noErrors = true;

            if (!is_array($values)) {
                $values = array($values);
            }

            foreach ($values as $value) {

                if (is_numeric($value)) {
                    $value = (int)$value;
                }

                if (!in_array($value, $list[0], true)) {
                    $noErrors = false;
                }
            }

            return $noErrors;
        };

        Validator::addRule(
            'range',
            $rangeValidator,
            '{{ name }} is not an option {{ field }}'
        );
    }
}
