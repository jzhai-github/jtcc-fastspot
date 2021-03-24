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
 * Handles submitted data from a calendar form
 */
class CalendarValidator extends BaseValidator
{
	/**
	 * Checks to make sure that the calendar's requirements are met
	 * @return array An array of errors or an empty array
	 */
	public function validate()
	{
		$v = new Validator($this->formData);
		$v->rule('required', array(
			'site_id',
			'name',
			'url_title',
			'description',
			'color',
			'time_format',
			'date_format',
			'start_day'
		))
		->message(lang('calendar_all_fields_required'));

		$v->rule('slug', 'url_title')
			->message(
				lang('calendar_calendar_url_title_bad_characters')
			);
		$v->rule('in', 'color', array(
			"#7bd148",
			"#5484ed",
			"#0099ff",
			"#46d6db",
			"#7ae7bf",
			"#51b749",
			"#fbd75b",
			"#ffb878",
			"#ff887c",
			"#dc2127",
			"#dbadff",
			"#e1de1e1"
		))->message(lang('calendar_color_not_in_colors'));

		$v->rule('in', 'time_format', array("g:i a", "G:i"))
			->message(lang('calendar_clock_type_incorrect'));
		$v->rule('in', 'date_format', array('m/d/Y', 'd/m/Y', 'Y/m/d'))
			->message(lang('calendar_date_format_incorrect'));
		$v->rule('in', 'start_day', range(0, 6))
			->message(lang('calendar_start_day_calendar_incorrect'));

		if (!$v->validate()) {
			return $v->errors();
		}

		return array();
	}
}
