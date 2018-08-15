<?php

namespace UniMan\Core\Utils;

class Filter
{
    const OPERATOR_EQUAL = 'equal';
    const OPERATOR_GREATER_THAN = 'greater_than';
    const OPERATOR_GREATER_THAN_OR_EQUAL = 'greater_than_or_equal';
    const OPERATOR_LESS_THAN = 'less_than';
    const OPERATOR_LESS_THAN_OR_EQUAL = 'less_than_or_equal';
    const OPERATOR_NOT_EQUAL = 'not_equal';
    const OPERATOR_CONTAINS = 'contains';
    const OPERATOR_NOT_CONTAINS = 'not_contains';
    const OPERATOR_STARTS_WITH = 'starts_with';
    const OPERATOR_ENDS_WITH = 'ends_with';
    const OPERATOR_IS_NULL = 'is_null';
    const OPERATOR_IS_NOT_NULL = 'is_not_null';
    const OPERATOR_IS_IN = 'is_in';
    const OPERATOR_IS_NOT_IN = 'is_not_in';

    const DEFAULT_FILTER_OPERATORS = [
        self::OPERATOR_EQUAL,
        self::OPERATOR_GREATER_THAN,
        self::OPERATOR_GREATER_THAN_OR_EQUAL,
        self::OPERATOR_LESS_THAN,
        self::OPERATOR_LESS_THAN_OR_EQUAL,
        self::OPERATOR_NOT_EQUAL,
        self::OPERATOR_CONTAINS,
        self::OPERATOR_NOT_CONTAINS,
        self::OPERATOR_STARTS_WITH,
        self::OPERATOR_ENDS_WITH,
        self::OPERATOR_IS_NULL,
        self::OPERATOR_IS_NOT_NULL,
        self::OPERATOR_IS_IN,
        self::OPERATOR_IS_NOT_IN,
    ];

    public static function apply(array $item, array $filter): bool
    {
        foreach ($filter as $filterPart) {
            foreach ($filterPart as $key => $filterSettings) {
                foreach ($filterSettings as $operator => $value) {
                    if (!array_key_exists($key, $item)) {
                        continue;
                    }
                    if (!self::checkFilter($operator, $item[$key], $value)) {
                        return false;
                    }
                }
            }
        }
        return true;
    }

    private static function checkFilter(string $operator, ?string $actualValue, ?string $expectedValue): bool
    {
        if ($operator === self::OPERATOR_EQUAL) {
            return $actualValue === $expectedValue;
        } elseif ($operator === self::OPERATOR_GREATER_THAN) {
            return $actualValue > $expectedValue;
        } elseif ($operator === self::OPERATOR_GREATER_THAN_OR_EQUAL) {
            return $actualValue >= $expectedValue;
        } elseif ($operator === self::OPERATOR_LESS_THAN) {
            return $actualValue < $expectedValue;
        } elseif ($operator === self::OPERATOR_LESS_THAN_OR_EQUAL) {
            return $actualValue <= $expectedValue;
        } elseif ($operator === self::OPERATOR_NOT_EQUAL) {
            return $actualValue !== $expectedValue;
        } elseif ($operator === self::OPERATOR_CONTAINS) {
            return strpos($actualValue, $expectedValue) !== false;
        } elseif ($operator === self::OPERATOR_NOT_CONTAINS) {
            return strpos($actualValue, $expectedValue) === false;
        } elseif ($operator === self::OPERATOR_STARTS_WITH) {
            return strpos($actualValue, $expectedValue) === 0;
        } elseif ($operator === self::OPERATOR_ENDS_WITH) {
            return substr($actualValue, -strlen($expectedValue)) === $expectedValue;
        } elseif ($operator === self::OPERATOR_IS_NULL) {
            return $actualValue === null;
        } elseif ($operator === self::OPERATOR_IS_NOT_NULL) {
            return $actualValue !== null;
        } elseif ($operator === self::OPERATOR_IS_IN) {
            $expectedValues = array_map('trim', explode(',', $expectedValue));
            return in_array($actualValue, $expectedValues);
        } elseif ($operator === self::OPERATOR_IS_NOT_IN) {
            $expectedValues = array_map('trim', explode(',', $expectedValue));
            return !in_array($actualValue, $expectedValues);
        }
        return false;
    }
}
