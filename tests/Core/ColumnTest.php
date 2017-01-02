<?php

namespace Adminerng\Tests\Core;

use Adminerng\Core\Column;
use Adminerng\Core\Utils\Filter;
use Closure;
use PHPUnit_Framework_TestCase;

class ColumnTest extends PHPUnit_Framework_TestCase
{
    public function testDefaults()
    {
        $column = new Column('key', 'title');
        self::assertEquals('key', $column->getKey());
        self::assertEquals('title', $column->getTitle());
        self::assertFalse($column->isSortable());
        self::assertFalse($column->isFilterable());
        self::assertTrue(is_array($column->getFilters()));
        self::assertTrue(empty($column->getFilters()));
        self::assertFalse($column->isNumeric());
        self::assertEquals(0, $column->getDecimals());
        self::assertFalse($column->isSize());
        self::assertFalse($column->isTime());
        self::assertNull($column->getInfo());
        self::assertNull($column->getInfoUrl());
        self::assertTrue(is_array($column->getExternal()));
        self::assertTrue(empty($column->getExternal()));
    }

    public function testSetters()
    {
        $column = new Column('key', 'title');
        self::assertInstanceOf(Column::class, $column->setSortable(true));
        self::assertInstanceOf(Column::class, $column->setFilterable(true));
        self::assertInstanceOf(Column::class, $column->setNumeric(true));
        self::assertInstanceOf(Column::class, $column->setDecimals(2));
        self::assertInstanceOf(Column::class, $column->setSize(true));
        self::assertInstanceOf(Column::class, $column->setTime(true));
        self::assertInstanceOf(Column::class, $column->setInfo('info'));
        self::assertInstanceOf(Column::class, $column->setInfoUrl('info_url'));
        self::assertInstanceOf(Column::class, $column->setExternal('database_name', 'table_name', function ($value) {
            return $value;
        }));
        self::assertEquals('key', $column->getKey());
        self::assertEquals('title', $column->getTitle());
        self::assertTrue($column->isSortable());
        self::assertTrue($column->isFilterable());
        self::assertTrue(is_array($column->getFilters()));
        self::assertEquals(Filter::DEFAULT_FILTER_OPERATORS, $column->getFilters());
        self::assertTrue($column->isNumeric());
        self::assertEquals(2, $column->getDecimals());
        self::assertTrue($column->isSize());
        self::assertTrue($column->isTime());
        self::assertEquals('info', $column->getInfo());
        self::assertEquals('info_url', $column->getInfoUrl());
        self::assertTrue(is_array($column->getExternal()));
        self::assertTrue(!empty($column->getExternal()));
        self::assertEquals('database_name', $column->getExternal()['database']);
        self::assertEquals('table_name', $column->getExternal()['table']);
        self::assertInstanceOf(Closure::class, $column->getExternal()['callback']);
    }
}
