<?php

namespace UniMan\Tests\Core\Utils;

use PHPUnit\Framework\TestCase;
use UniMan\Core\Utils\Filter;

class FilterTest extends TestCase
{
    public function testNoFilter()
    {
        $item = [
            'key' => 'first_key',
            'title' => 'First key',
            'sorting' => 100,
        ];

        $filter = [];
        self::assertTrue(Filter::apply($item, $filter));
    }

    public function testFilterUnknownColumn()
    {
        $item = [
            'key' => 'first_key',
            'title' => 'First key',
            'sorting' => 100,
        ];

        $filter = [['unknown_column' => [Filter::OPERATOR_EQUAL => 'first_key']]];
        self::assertTrue(Filter::apply($item, $filter));
    }

    public function testFilterUnknownOperator()
    {
        $item = [
            'key' => 'first_key',
            'title' => 'First key',
            'sorting' => 100,
        ];

        $filter = [['key' => ['uknown_operator' => 'value']]];
        self::assertFalse(Filter::apply($item, $filter));
    }

    public function testFilterEqual()
    {
        $item = [
            'key' => 'first_key',
            'title' => 'First key',
            'sorting' => 100,
        ];

        $filter = [['key' => [Filter::OPERATOR_EQUAL => 'first_key']]];
        self::assertTrue(Filter::apply($item, $filter));

        $filter = [['key' => [Filter::OPERATOR_EQUAL => 'first_key ']]];
        self::assertFalse(Filter::apply($item, $filter));

        $filter = [['key' => [Filter::OPERATOR_EQUAL => 'some_other_key']]];
        self::assertFalse(Filter::apply($item, $filter));
    }

    public function testFilterNotEqual()
    {
        $item = [
            'key' => 'first_key',
            'title' => 'First key',
            'sorting' => 100,
        ];

        $filter = [['key' => [Filter::OPERATOR_NOT_EQUAL => 'some_other_key']]];
        self::assertTrue(Filter::apply($item, $filter));

        $filter = [['key' => [Filter::OPERATOR_NOT_EQUAL => 'first_key ']]];
        self::assertTrue(Filter::apply($item, $filter));

        $filter = [['key' => [Filter::OPERATOR_NOT_EQUAL => 'first_key']]];
        self::assertFalse(Filter::apply($item, $filter));
    }

    public function testFilterGreaterThan()
    {
        $item = [
            'key' => 'first_key',
            'title' => 'First key',
            'sorting' => 100,
        ];

        $filter = [['sorting' => [Filter::OPERATOR_GREATER_THAN => 0]]];
        self::assertTrue(Filter::apply($item, $filter));

        $filter = [['sorting' => [Filter::OPERATOR_GREATER_THAN => 100]]];
        self::assertFalse(Filter::apply($item, $filter));

        $filter = [['sorting' => [Filter::OPERATOR_GREATER_THAN => 1000]]];
        self::assertFalse(Filter::apply($item, $filter));
    }

    public function testFilterGreaterThanOrEqual()
    {
        $item = [
            'key' => 'first_key',
            'title' => 'First key',
            'sorting' => 100,
        ];

        $filter = [['sorting' => [Filter::OPERATOR_GREATER_THAN_OR_EQUAL => 0]]];
        self::assertTrue(Filter::apply($item, $filter));

        $filter = [['sorting' => [Filter::OPERATOR_GREATER_THAN_OR_EQUAL => 100]]];
        self::assertTrue(Filter::apply($item, $filter));

        $filter = [['sorting' => [Filter::OPERATOR_GREATER_THAN_OR_EQUAL => 1000]]];
        self::assertFalse(Filter::apply($item, $filter));
    }

    public function testFilterLessThan()
    {
        $item = [
            'key' => 'first_key',
            'title' => 'First key',
            'sorting' => 100,
        ];

        $filter = [['sorting' => [Filter::OPERATOR_LESS_THAN => 1000]]];
        self::assertTrue(Filter::apply($item, $filter));

        $filter = [['sorting' => [Filter::OPERATOR_LESS_THAN => 100]]];
        self::assertFalse(Filter::apply($item, $filter));

        $filter = [['sorting' => [Filter::OPERATOR_LESS_THAN => 0]]];
        self::assertFalse(Filter::apply($item, $filter));
    }

    public function testFilterLessThanOrEqual()
    {
        $item = [
            'key' => 'first_key',
            'title' => 'First key',
            'sorting' => 100,
        ];

        $filter = [['sorting' => [Filter::OPERATOR_LESS_THAN_OR_EQUAL => 1000]]];
        self::assertTrue(Filter::apply($item, $filter));

        $filter = [['sorting' => [Filter::OPERATOR_LESS_THAN_OR_EQUAL => 100]]];
        self::assertTrue(Filter::apply($item, $filter));

        $filter = [['sorting' => [Filter::OPERATOR_LESS_THAN_OR_EQUAL => 0]]];
        self::assertFalse(Filter::apply($item, $filter));
    }

    public function testFilterContains()
    {
        $item = [
            'key' => 'first_key',
            'title' => 'First key',
            'sorting' => 100,
        ];

        $filter = [['title' => [Filter::OPERATOR_CONTAINS => 'key']]];
        self::assertTrue(Filter::apply($item, $filter));

        $filter = [['title' => [Filter::OPERATOR_CONTAINS => 'First']]];
        self::assertTrue(Filter::apply($item, $filter));

        $filter = [['title' => [Filter::OPERATOR_CONTAINS => 'something']]];
        self::assertFalse(Filter::apply($item, $filter));
    }

    public function testFilterNotContains()
    {
        $item = [
            'key' => 'first_key',
            'title' => 'First key',
            'sorting' => 100,
        ];

        $filter = [['title' => [Filter::OPERATOR_NOT_CONTAINS => 'something']]];
        self::assertTrue(Filter::apply($item, $filter));

        $filter = [['title' => [Filter::OPERATOR_NOT_CONTAINS => 'else']]];
        self::assertTrue(Filter::apply($item, $filter));

        $filter = [['title' => [Filter::OPERATOR_NOT_CONTAINS => 'First']]];
        self::assertFalse(Filter::apply($item, $filter));
    }

    public function testFilterStartsWith()
    {
        $item = [
            'key' => 'first_key',
            'title' => 'First key',
            'sorting' => 100,
        ];

        $filter = [['title' => [Filter::OPERATOR_STARTS_WITH => 'F']]];
        self::assertTrue(Filter::apply($item, $filter));

        $filter = [['title' => [Filter::OPERATOR_STARTS_WITH => 'First']]];
        self::assertTrue(Filter::apply($item, $filter));

        $filter = [['title' => [Filter::OPERATOR_STARTS_WITH => 'key']]];
        self::assertFalse(Filter::apply($item, $filter));
    }

    public function testFilterEndsWith()
    {
        $item = [
            'key' => 'first_key',
            'title' => 'First key',
            'sorting' => 100,
        ];

        $filter = [['title' => [Filter::OPERATOR_ENDS_WITH => 'y']]];
        self::assertTrue(Filter::apply($item, $filter));

        $filter = [['title' => [Filter::OPERATOR_ENDS_WITH => 'key']]];
        self::assertTrue(Filter::apply($item, $filter));

        $filter = [['title' => [Filter::OPERATOR_ENDS_WITH => 'First']]];
        self::assertFalse(Filter::apply($item, $filter));
    }

    public function testFilterIsNull()
    {
        $item = [
            'key' => 'null',
            'title' => 'First key',
            'sorting' => 100,
            'nullable' => null,
        ];

        $filter = [['nullable' => [Filter::OPERATOR_IS_NULL => true]]];
        self::assertTrue(Filter::apply($item, $filter));

        $filter = [['key' => [Filter::OPERATOR_IS_NULL => true]]];
        self::assertFalse(Filter::apply($item, $filter));

        $filter = [['title' => [Filter::OPERATOR_IS_NULL => true]]];
        self::assertFalse(Filter::apply($item, $filter));
    }

    public function testFilterIsNotNull()
    {
        $item = [
            'key' => 'null',
            'title' => 'First key',
            'sorting' => 100,
            'nullable' => null,
        ];

        $filter = [['key' => [Filter::OPERATOR_IS_NOT_NULL => true]]];
        self::assertTrue(Filter::apply($item, $filter));

        $filter = [['title' => [Filter::OPERATOR_IS_NOT_NULL => true]]];
        self::assertTrue(Filter::apply($item, $filter));

        $filter = [['nullable' => [Filter::OPERATOR_IS_NOT_NULL => true]]];
        self::assertFalse(Filter::apply($item, $filter));
    }

    public function testFilterIsIn()
    {
        $item = [
            'key' => 'key',
            'title' => 'First key',
            'sorting' => 100,
        ];

        $filter = [['key' => [Filter::OPERATOR_IS_IN => 'key']]];
        self::assertTrue(Filter::apply($item, $filter));

        $filter = [['key' => [Filter::OPERATOR_IS_IN => 'test,key,keys']]];
        self::assertTrue(Filter::apply($item, $filter));

        $filter = [['key' => [Filter::OPERATOR_IS_IN => '']]];
        self::assertFalse(Filter::apply($item, $filter));

        $filter = [['key' => [Filter::OPERATOR_IS_IN => 'some,other,keys']]];
        self::assertFalse(Filter::apply($item, $filter));
    }

    public function testFilterIsNotIn()
    {
        $item = [
            'key' => 'key',
            'title' => 'First key',
            'sorting' => 100,
        ];

        $filter = [['key' => [Filter::OPERATOR_IS_NOT_IN => '']]];
        self::assertTrue(Filter::apply($item, $filter));

        $filter = [['key' => [Filter::OPERATOR_IS_NOT_IN => 'some,other,keys']]];
        self::assertTrue(Filter::apply($item, $filter));

        $filter = [['key' => [Filter::OPERATOR_IS_NOT_IN => 'key']]];
        self::assertFalse(Filter::apply($item, $filter));

        $filter = [['key' => [Filter::OPERATOR_IS_NOT_IN => 'test,key,keys']]];
        self::assertFalse(Filter::apply($item, $filter));
    }
}
