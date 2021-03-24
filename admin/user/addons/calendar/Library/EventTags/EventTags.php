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

namespace Solspace\Addons\Calendar\Library\EventTags;

use Solspace\Addons\Calendar\Helpers\DateTimeHelper;

/**
 * Handles the creation of ExpressionEngine event template tags
 */
class EventTags
{
    /**
     * Prevent dates from being parsed by the EE template parser based
     * on per user or per site timezone so we can preserve
     * timezones set for each calendar. Injects our own placeholder
     * tags into the template containing the necessary date info and
     * parses them separately.
     *
     * @param array $vars - The array of filled out tags
     * @param bool  $tagdata
     *
     * @return array Tagdata with preserved dates formats
     */
    public static function parseTagdataPreservingDates($vars, $tagdata = false)
    {
        $tagdata = $tagdata ?: ee()->TMPL->tagdata;
        if (strpos($tagdata, ' format=') !== false) {
            preg_match_all(
                '/\\' . LD . '([a-zA-Z_:]+)\s+(format\s*=\s*[\'|\"](.*?)[\'|\"])\\' . RD . '/i',
                $tagdata,
                $matches
            );
        }

        if (empty($matches[0])) {
            $tagdata = ee()->TMPL->parse_variables($tagdata, $vars);
            return $tagdata;
        }

        $tagId = uniqid('cal', true);

        foreach ($matches[0] as $k => $tag) {
            $timestampTag = LD . $matches[1][$k] . RD;
            $tagdata      =
                str_replace(
                    $tag,
                    '[' . $tagId . ' time="' . $timestampTag . '" fmt="' . ($matches[3][$k]) . '" /' . $tagId . ']',
                    $tagdata
                );
        }

        $tagdata = ee()->TMPL->parse_variables($tagdata, $vars);

        preg_match_all(
            '/\[' . $tagId .
            '\s+(?:time\s*=\s*[\'|\"](.*?)[\'|\"])\s+(?:fmt\s*=\s*[\'|\"]' .
            '(.*?)[\'|\"])\s+\/'
            . $tagId
            . '\]/i',
            $tagdata,
            $result
        );

        if (empty($result[0])) {
            return $tagdata;
        }

        foreach ($result[0] as $i => $tag) {
            $timeData = explode(':', $result[1][$i]);
            if (count($timeData) === 1) {
                $timezone = DateTimeHelper::TIMEZONE_UTC;
                list($timestamp) = $timeData;
                if (!$timestamp) {
                    $tagdata = str_replace($tag, null, $tagdata);
                    continue;
                }
            } else {
                list($timestamp, $timezone) = $timeData;
            }

            $format = $result[2][$i];

            $formatted = ee()->localize->format_date($format, $timestamp, $timezone);
            $tagdata   = str_replace($tag, $formatted, $tagdata);
        }

        return $tagdata;
    }

    /**
     * Recursively prefixes all $variables keys with "addon_name:"
     * So a key "id" becomes "addon_name:id"
     *
     * @param array  $variables
     * @param string $prefix
     *
     * @return array
     */
    public static function prefixTemplateVariables(array $variables, $prefix)
    {
        $prefixed = array();
        foreach ($variables as $key => $variableRow) {
            $prefixedVariables = array();

            if (!is_array($variableRow)) {
                $prefixedVariables = $variableRow;
            } else {
                foreach ($variableRow as $subKey => $variable) {
                    if ($subKey === 'path_variable') {
                        $prefixedVariables[$subKey] = $variable;
                        continue;
                    }

                    if (is_array($variable)) {
                        $variable = self::prefixTemplateVariables($variable, $prefix);
                    }

                    self::parseFileDirInVariable($variable);

                    if (!is_numeric($subKey)) {
                        $prefixedVariables[$prefix . ':' . $subKey] = $variable;
                    } else {
                        $prefixedVariables[$subKey] = $variable;
                    }
                }
            }

            $prefixed[$key] = $prefixedVariables;
        }

        return $prefixed;
    }

    /**
     * If a variable is a string and it contains {filedir_x}
     * parse it to the respective file directory
     *
     * @param string $variable
     */
    public static function parseFileDirInVariable(&$variable)
    {
        if (is_string($variable) && strpos($variable, '{filedir_') !== false) {
            ee()->load->library('file_field');
            $variable = ee()->file_field->parse_string($variable);
        }
    }
}
