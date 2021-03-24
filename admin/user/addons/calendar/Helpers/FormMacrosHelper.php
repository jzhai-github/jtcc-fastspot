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

namespace Solspace\Addons\Calendar\Helpers;

class FormMacrosHelper
{
    const BUTTON_TYPE_RADIO    = 'radio';
    const BUTTON_TYPE_CHECKBOX = 'checkbox';

    /**
     *
     *
     * @var \stdClass
     */
    private $options;

    /**
     * Returns a formatted <label> tag
     *
     * @param array $options
     *
     * @return string
     */
    public function label($options)
    {
        $this->setOptions($options);

        $output = '<label';
        $output .= $this->getAttribute('for');
        $output .= $this->getAttribute('class', 'className');
        $output .= '>';

        $output .= $this->getOptionValue('text');

        $output .= '</label>';

        return $output;
    }

    /**
     * Returns a formatted <input /> tag
     *
     * @param array $options
     *
     * @return string
     */
    public function input($options)
    {
        $this->setOptions($options);

        $output = '<input';
        $output .= $this->getAttribute('type', null, 'text');
        $output .= $this->getAttribute('name');
        $output .= $this->getAttribute('placeholder');
        $output .= $this->getAttribute('class', 'className');
        $output .= $this->getAttribute('value');
        $output .= $this->getAttribute('rel');
        $output .= $this->getProperty('checked');
        $output .= $this->getAttribute('id');

        if ($this->getOptionValue('type') == "text") {
            $output .= $this->getAttribute('autocomplete', null, 'off');
            $output .= $this->getAttribute('autocapitalize', null, 'off');
            $output .= $this->getAttribute('spellcheck', null, 'false');
        }

        if (isset($this->options->data) && is_array($this->options->data)) {
            foreach ($this->options->data as $key => $value) {
                $output .= ' data-' . $key . '="' . $value . '"';
            }
        }

        $value      = $this->getOptionValue('value');
        $radioValue = $this->getOptionValue('radioValue');
        if (!is_null($radioValue) && $radioValue == $value) {
            $output .= ' checked';
        }

        $output .= $this->getProperty('checked');
        $output .= $this->getProperty('required');

        $output .= ' />';

        $output .= $this->getOptionValue('label');

        return $output;
    }

    /**
     * Returns an html block used for defining intervals
     *
     * @param array $options
     *
     * @return string
     */
    public function interval($options)
    {
        $this->setOptions($options);

        $output = '<div class="calendarIntervalWrapper">';
        $output .= '<span class="calendarText">' . lang('calendar_repeats_every') . '</span>';
        $output .= $this->input(
            array(
                "name"      => $this->getOptionValue('name'),
                "value"     => $this->getOptionValue('value') ?: 1,
                "className" => "calendarInterval",
            )
        );

        // The class options var get reset when calling other methods.
        // So we just take a peek in the $options
        // Should refactor this to something more reasonable, like buildable objects for each type
        // And then compile output when calling a render method, or something
        //$output .= '<span class="calendarText">' . isset($options['text']) ? $options['text'] : '' . '</span>';
        $output .= '</div>';

        return $output;
    }

    /**
     * Returns an html block of radio buttons
     * All radio buttons must be listed as an array of assoc arrays under the $options "items" key
     * Each item has a "label", "value" and "labelClass" property
     *
     * "labelClass" is optional
     *
     * @param array  $options
     * @param string $type "radio" or "checkbox"
     *
     * @return string
     */
    public function button_list($options, $type = self::BUTTON_TYPE_RADIO)
    {
        $this->setOptions($options);

        $name   = $this->getOptionValue('name');
        $value  = $this->getOptionValue('value');
        $values = $this->getOptionValue('values');
        $class  = $this->getOptionValue('className');
        $id     = $this->getOptionValue('id');

        $output = '';
        foreach ($options['items'] as $index => $radioData) {
            if (is_array($radioData)) {
                $labelClass = isset($radioData['labelClass']) ? $radioData['labelClass'] : '';
                $inputValue = $radioData['value'];
                $label      = $radioData['label'];
                $checked    = !is_null($value) && $value == $inputValue;
            } else {
                $labelClass = '';
                $inputValue = $index;
                $label      = $radioData;
                $checked    = in_array($inputValue, $values ?: array());
            }


            if ($type == "checkbox") {
                $labelClass .= ' calendar-checkbox-list';
            }

            $output .= '<label class="choice mr ' . $labelClass . ($checked ? ' chosen' : '') . '">';
            $output .= $this->input(
                array(
                    "type"       => $type,
                    "name"       => $name,
                    "value"      => $inputValue,
                    "radioValue" => $value,
                    "className"  => $class,
                    "checked"    => $checked,
                    "id"         => $id ? ($id . $inputValue) : null,
                )
            );
            $output .= ' ' . $label;
            $output .= '</label>';

            $index++;
        }

        return $output;
    }

    /**
     * An alias for self::button_list()
     *
     * @param array $options
     *
     * @return string
     */
    public function checkbox_list($options)
    {
        return $this->button_list($options, self::BUTTON_TYPE_CHECKBOX);
    }

    /**
     * Returns a title <span> with a checkbox
     *
     * @param array $options
     *
     * @return string
     */
    public function switch_checkbox($options)
    {
        $this->setOptions($options);

        $name  = $this->getOptionValue('name');
        $value = $this->getOptionValue('value');
        $class = $this->getOptionValue('className');
        $id    = $this->getOptionValue('id');

        $output = '<a href="#" class="toggle-btn ' . ($value ? 'on' : 'off') . ' ' . $class . '">';
            $output .= $this->input(
                array(
                    'type'      => 'hidden',
                    'name'      => $name,
                    'value'     => $value,
                    'id'        => $id,
                    'className' => $class,
                )
            );
            $output .= '<span class="slider"></span>';
            $output .= '<span class="option"></span>';
        $output .= '</a>';

        return $output;
    }


    /**
     * Returns a month day button set html
     *
     * @param array $options
     *
     * @return string
     */
    public function month_day_buttons($options)
    {
        $this->setOptions($options);

        $output = '<span class="calendarAlternateButtonSet clearfix calendarMonthlyButtonsetFirst">';

        $name   = $this->getOptionValue('name');
        $id     = $this->getOptionValue('id');
        $values = $this->getOptionValue('values') ?: array();

        for ($day = 1; $day <= 31; $day++) {
            $checked = in_array($day, $values);

            $output .= '<label class="choice mr calendar-checkbox-list calendarByMonthDay' . ($checked ? ' chosen' : '') . '">';
            $output .= $this->input(
                array(
                    "type"    => "checkbox",
                    "name"    => $name,
                    "value"   => $day,
                    "id"      => $id . $day,
                    "checked" => $checked,
                )
            );
            $output .= ' ' . $day;
            $output .= '</label>';

            if ($day % 8 == 0) {
                $output .= '</span><span class="calendarAlternateButtonSet clearfix">';
            }
        }

        $output .= '</span>';

        return $output;
    }


    /**
     * Returns a month name button set html
     *
     * @param array $options
     *
     * @return string
     */
    public function month_name_buttons($options)
    {
        $this->setOptions($options);

        $output = '<span class="calendarAlternateButton calendarMonthButtons calendarMonthButtonsFirst clearfix">';
        $months = DateTimeHelper::getTranslatedMonths();

        $name   = $this->getOptionValue('name');
        $id     = $this->getOptionValue('id');
        $class  = $this->getOptionValue('className');
        $values = $this->getOptionValue('values') ?: array();

        $i = 0;
        foreach ($months as $index => $month) {
            if ($i % 3 == 0 && $i > 0) {
                $output .= '</span><span class="calendarAlternateButton calendarMonthButtons clearfix">';
            }

            $checked = in_array($index, $values);

            $output .= '<label class="choice mr calendar-checkbox-list calendarByMonth' . ($checked ? ' chosen' : '') . '">';
            $output .= $this->input(
                array(
                    "type"      => "checkbox",
                    "name"      => $name,
                    "value"     => $index,
                    "checked"   => $checked,
                    "className" => $class,
                    "id"        => $id . $index,
                )
            );
            $output .= ' ' . $month;
            $output .= '</label>';

            $i++;
        }

        $output .= '</span>';

        return $output;
    }

    /**
     * Returns a button set html
     *
     * @param array $options
     *
     * @return string
     */
    public function buttonset($options)
    {
        $this->setOptions($options);

        $output = '<span class="calendarButtonset">';

        $optionName      = $this->getOptionValue('name');
        $optionClassName = $this->getOptionValue('className');
        $optionId        = $this->getOptionValue('id');

        if (isset($options['buttons']) && is_array($options['buttons'])) {
            foreach ($options['buttons'] as $buttonTitle) {
                $optionValue = strtoupper($buttonTitle);

                $checked = false;
                if (isset($options->values) && is_array($options->values)) {
                    $checked = in_array($optionValue, $options->values, true);
                }

                $output .= $this->input(
                    array(
                        "type"      => "checkbox",
                        "name"      => $optionName,
                        "value"     => $optionValue,
                        "checked"   => $checked,
                        "className" => $optionClassName,
                        "id"        => $optionId . $buttonTitle,
                        "label"     => $this->label(
                            array(
                                "text" => $buttonTitle,
                                "for"  => $optionId . $buttonTitle,
                            )
                        ),
                    )
                );
            }
        }

        $output .= '</span>';

        return $output;
    }


    /**
     * Returns a formatted <select> tag
     *
     * @param array $options
     *
     * @return string
     */
    public function dropdown($options)
    {
        $this->setOptions($options);

        $values = $this->getOptionValue('values');

        $output = '<select';
        $output .= $this->getAttribute('name');
        $output .= $this->getAttribute('id');
        $output .= $this->getAttribute('class', 'className');
        $output .= $this->getProperty('multiple', 'multi');

        if (isset($this->options->data) && is_array($this->options->data)) {
            foreach ($this->options->data as $key => $value) {
                $output .= ' data-' . $key . '="' . $value . '"';
            }
        }

        $output .= $this->getAttribute('placeholder');
        $output .= '>';

        // Options
        // ==================
        foreach ($options['options'] as $id => $title) {
            $isSelected = false;
            if (!empty($values)) {
                if (is_array($values)) {
                    $isSelected = in_array($id, $values);
                } else {
                    $isSelected = $id == $values;
                }
            }

            $output .= '<option value="' . $id . '"' . ($isSelected ? ' selected="selected"' : '') . '>';
            $output .= $title;
            $output .= '</option>';
        }

        $output .= '</select>';

        return $output;
    }


    /**
     * Converts $options to an \stdClass
     * Stores it in the self::$options property
     *
     * !This property changes every time a method gets called!
     *
     * @param array|\stdClass $options
     *
     * @return $this
     */
    private function setOptions($options)
    {
        if (is_array($options)) {
            $options = (object)$options;
        }

        if (!is_object($options)) {
            return new \stdClass();
        }

        $this->options = $options;

        return $this;
    }

    /**
     * If $optionName exists in self::$options, an html tag attribute gets returned
     * Otherwise - an empty string
     *
     * E.g. - if the $attributeName is "class" and $optionName is "className"
     * self::$options->className exists and its value is "super-cool-class"
     * ' class="super-cool-class"' will be returned
     *
     * If $optionName is null, $attributeName will be used to check self::$options name
     *
     * @param string $attributeName
     * @param string $optionName
     * @param mixed  $defaultValue - if provided and no value found in self::$options, this
     *                             gets provided instead
     *
     * @return string
     */
    private function getAttribute($attributeName, $optionName = null, $defaultValue = null)
    {
        if (is_null($optionName)) {
            $optionName = $attributeName;
        }

        $output      = '';
        $optionValue = $this->getOptionValue($optionName);

        // If $optionValue is empty and a $defaultValue exists - use that
        if (!is_null($optionValue) && empty($optionValue) && !is_null($defaultValue)) {
            $optionValue = $defaultValue;
        }

        // Builds the attribute
        if (!is_null($optionValue)) {
            $output = ' ' . $attributeName . '="' . $optionValue . '"';
        }

        return $output;
    }

    /**
     * If $optionName exists in self:$options, an html tag property gets returned
     * Otherwise - an empty string
     *
     * E.g. - if the $propertyName is "checked" and $optionName is "isChecked"
     * and self::$options->isChecked exists and is a boolean value of TRUE
     * ' checked' will be returned
     *
     * If $optionName is null, $propertyName will be used to check self::$options name
     *
     * @param string $propertyName
     * @param string $optionName
     *
     * @return string
     */
    private function getProperty($propertyName, $optionName = null)
    {
        if (is_null($optionName)) {
            $optionName = $propertyName;
        }

        $optionValue = $this->getOptionValue($optionName);

        // Return the property only if $optionValue is TRUE
        if (!is_null($optionValue) && (bool)$optionValue) {
            return ' ' . $propertyName;
        }

        return '';
    }

    /**
     * Gets the value of an option if it exists
     * Otherwise returns null
     *
     * @param string $optionName
     *
     * @return string|null
     */
    private function getOptionValue($optionName)
    {
        if (isset($this->options->{$optionName})) {
            return $this->options->{$optionName};
        }

        return null;
    }
}
