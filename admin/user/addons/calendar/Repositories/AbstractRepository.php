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

namespace Solspace\Addons\Calendar\Repositories;

use EllisLab\ExpressionEngine\Service\Database\Query;
use EllisLab\ExpressionEngine\Service\Model\Query\Builder;

abstract class AbstractRepository
{
    /** @var AbstractRepository[] */
    protected static $instances;

    protected function __construct()
    {
    }

    final private function __clone()
    {
    }

    public static function getInstance()
    {
        $class = get_called_class();

        if (!isset(self::$instances[$class])) {
            self::$instances[$class] = new $class;
        }

        return self::$instances[$class];
    }

    /**
     * @param Builder $builder
     * @param string  $parameterName
     * @param string  $value
     */
    protected static final function attachBuilderParameter(Builder $builder, $parameterName, $value)
    {
        list($values, $isIn) = self::explodeParameter($value);
        if ($isIn) {
            $builder->filter($parameterName, 'IN', $values);
        } else {
            $builder->filter($parameterName, 'NOT IN', $values);
        }
    }

    /**
     * @param Query  $builder
     * @param string $parameterName
     * @param string $value
     * @param bool   $attachOrIsNull
     */
    protected static final function attachQueryParameter(
        Query $builder,
        $parameterName,
        $value,
        $attachOrIsNull = false
    ) {
        list($values, $isIn) = self::explodeParameter($value);

        if (!$isIn && $attachOrIsNull) {
            $implodedValues = self::implodeValues($values, $builder);

            $notInKeyword = $isIn ? "" : "NOT";

            $queryString = "$parameterName $notInKeyword IN ($implodedValues)";

            if ($attachOrIsNull) {
                $queryString .= " OR $parameterName IS NULL";
            }

            $queryString = '(' . $queryString . ')';

            $builder->where($queryString, null, false);
        } else {
            if ($isIn) {
                $builder->where_in($parameterName, $values);
            } else {
                $builder->where_not_in($parameterName, $values);
            }
        }
    }

    /**
     * Takes a Template param and explodes it into an array
     * Checks for a "not " occurrence at the beginning of the string
     * Returns the query selection method (true - IN, false - NOT IN) as the second array element
     *
     * @param string $parameterData
     *
     * @return array
     */
    protected static function explodeParameter($parameterData)
    {
        $in = true;

        if (strtolower(substr($parameterData, 0, 4)) == 'not ') {
            $in = false;

            $parameterData = substr($parameterData, 4);
        }

        return array(
            preg_split('/[&\|]/', $parameterData, -1, PREG_SPLIT_NO_EMPTY),
            $in,
        );
    }

    /**
     * Check if the $string has an ampersand in it
     *
     * @param string $string
     *
     * @return bool
     */
    protected static function isAmpersand($string)
    {
        return strpos($string, '&') !== false;
    }

    /**
     * Combines
     *
     * @param array $values
     * @param Query $builder
     *
     * @return array
     */
    private static function implodeValues(array $values, Query $builder)
    {
        $parsedList = self::buildAndEscapeValues($values, $builder);

        return implode(', ', $parsedList);
    }

    /**
     * @param array $values
     * @param Query $builder
     *
     * @return array
     */
    private static function buildAndEscapeValues(array $values, Query $builder)
    {
        $parsedList = array();

        foreach ($values as $value) {
            if (is_array($value)) {
                $parsedList = array_merge($parsedList, self::buildAndEscapeValues($value));
            } else {
                if (is_numeric($value)) {
                    $parsedList[] = (int)$value;
                } else {
                    $value = $builder->escape($value);
                    $parsedList[] = $value;
                }
            }
        }

        return $parsedList;
    }
}
