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

namespace Solspace\Addons\Calendar\Library;

class EEDateLangShim
{
	static $loaded = false;

	// --------------------------------------------------------------------

	/**
	 *
	 * @access	public
	 * @return	boolean		success
	 */

	public static function loadLang()
	{
		//cannot do anything without this
		if (static::$loaded || ! function_exists('ee'))
		{
			return false;
		}

		$object = null;

		if (isset(ee()->session))
		{
			$object =& ee()->session;
		}

		if (is_object($object) AND
			//because some addon makers don't check to see if
			//ee()->session is set yet before setting cache
			//vars on it and it auto instantiates an blank
			//object -_-
			strtolower(get_class($object)) == 'session' AND
			$object->userdata['language'] != '')
		{
			$user_lang = $object->userdata['language'];
		}
		else
		{
			if (ee()->input->cookie('language'))
			{
				$user_lang = ee()->input->cookie('language');
			}
			elseif (ee()->config->item('deft_lang') != '')
			{
				$user_lang = ee()->config->item('deft_lang');
			}
			else
			{
				$user_lang = 'english';
			}
		}

		//no BS
		$user_lang	= ee()->security->sanitize_filename($user_lang);

		$path = SYSPATH . 'ee/legacy/language/';

		$options = array(
			$path . $user_lang . '/calendar_lang.php',
			$path . 'english/calendar_lang.php'
		);

		$success = false;

		foreach($options as $path)
		{
			//includes the file and sets $lang (array);
			if ( file_exists($path) AND include $path)
			{
				$success = true;
				break;
			}
		}

		// -------------------------------------
		//	if we get this far, we should say
		//	we've already done our best
		//	and things wont benefit from
		//	running again
		// -------------------------------------

		static::$loaded = true;

		if ($success == false)
		{
			return false;
		}

		if (isset($lang) && isset(ee()->lang->language))
		{
			ee()->lang->language = array_merge(
				ee()->lang->language,
				$lang
			);

			unset($lang);

			return true;
		}

		return false;
	}
	// END loadLang
}
//END EEDateLangShim