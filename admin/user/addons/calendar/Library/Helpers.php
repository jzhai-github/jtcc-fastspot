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

use Solspace\Addons\Calendar\Library\ColorJizz\Formats\Hex;

class Helpers
{
    /**
     * Generates a random HEX color code
     *
     * @return string
     */
    public static function randomColor()
    {
        return sprintf('#%06X', mt_rand(0, 0xFFFFFF));
    }

    /**
     * Truncates a given string if it exceeds the length of $truncateLength and $truncator length combined
     * Returns the truncated string
     *
     * @param int    $truncateLength
     * @param string $truncator
     *
     * @return string
     */
    public static function truncateString($string, $truncateLength = 30, $truncator = '...')
    {
        $string       = trim($string);
        $stringLength = strlen($string);

        if ($stringLength > $truncateLength + strlen($truncator)) {
            $string = trim(substr($string, 0, $truncateLength)) . $truncator;
        }

        return $string;
    }

    /**
     * Lightens/darkens a given colour (hex format), returning the altered colour in hex format.7
     *
     * @param string $hexString Colour as hexadecimal (with or without hash);
     * @param float  $percent   Decimal (0.2 = lighten by 20%, -0.4 = darken by 40%)
     *
     * @return string Lightened/Darkend colour as hexadecimal (with hash);
     */
    public static function lightenDarkenColour($hexString, $percent)
    {
        return '#' . Hex::fromString($hexString)->brightness($percent * 100);
    }

    /**
     * Determines if the contrasting color to be used based on a HEX color code
     *
     * @param string $hexColor
     *
     * @return string
     */
    public static function getContrastYIQ($hexColor)
    {
        $hexColor = str_replace('#', '', $hexColor);

        $r   = hexdec(substr($hexColor, 0, 2));
        $g   = hexdec(substr($hexColor, 2, 2));
        $b   = hexdec(substr($hexColor, 4, 2));
        $yiq = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;

        return ($yiq >= 128) ? 'black' : 'white';
    }
}
