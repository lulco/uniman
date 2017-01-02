<?php

namespace Adminerng\Tests;

use Adminerng\Core\Utils\Multisort;
use PHPUnit_Framework_TestCase;

class MultisortTest extends PHPUnit_Framework_TestCase
{
    public function testNoSort()
    {
        $original = [
            0 => [
                'key' => 'first_key',
                'title' => 'First key',
                'sorting' => 100,
            ],
            1 => [
                'key' => 'second_key',
                'title' => 'Second key',
                'sorting' => 50,
            ],
        ];

        $sorting = [];
        self::assertEquals($original, Multisort::sort($original, $sorting));
    }

    public function testSingleSort()
    {
        $original = [
            0 => [
                'key' => 'first_key',
                'title' => 'First key',
                'sorting' => 100,
            ],
            1 => [
                'key' => 'second_key',
                'title' => 'Second key',
                'sorting' => 50,
            ],
        ];

        $sorting = [0 => ['sorting' => 'asc']];

        $expected = [
            1 => [
                'key' => 'second_key',
                'title' => 'Second key',
                'sorting' => 50,
            ],
            0 => [
                'key' => 'first_key',
                'title' => 'First key',
                'sorting' => 100,
            ],
        ];

        self::assertEquals($expected, Multisort::sort($original, $sorting));
    }

    public function testSingleSortUnknownColumn()
    {
        $original = [
            0 => [
                'key' => 'first_key',
                'title' => 'First key',
                'sorting' => 100,
            ],
            1 => [
                'key' => 'second_key',
                'title' => 'Second key',
                'sorting' => 50,
            ],
        ];

        $sorting = [0 => ['unknown' => 'asc']];
        self::assertEquals($original, Multisort::sort($original, $sorting));
    }

    public function testMultipleSort()
    {
        $original = [
            0 => [
                'key' => 'first_key',
                'title' => 'First key',
                'sorting' => 100,
            ],
            1 => [
                'key' => 'second_key',
                'title' => 'Second key',
                'sorting' => 50,
            ],
            2 => [
                'key' => 'third_key',
                'title' => 'Third key',
                'sorting' => 50,
            ],
        ];

        $sorting = [0 => ['sorting' => 'asc'], 1 => ['key' => 'desc']];

        $expected = [
            2 => [
                'key' => 'third_key',
                'title' => 'Third key',
                'sorting' => 50,
            ],
            1 => [
                'key' => 'second_key',
                'title' => 'Second key',
                'sorting' => 50,
            ],
            0 => [
                'key' => 'first_key',
                'title' => 'First key',
                'sorting' => 100,
            ],
        ];

        self::assertEquals($expected, Multisort::sort($original, $sorting));
    }

    public function testMultipleSortWithOneUnknownColumn()
    {
        $original = [
            0 => [
                'key' => 'first_key',
                'title' => 'First key',
                'sorting' => 100,
            ],
            1 => [
                'key' => 'second_key',
                'title' => 'Second key',
                'sorting' => 50,
            ],
            2 => [
                'key' => 'third_key',
                'title' => 'Third key',
                'sorting' => 50,
            ],
        ];

        $sorting = [0 => ['sorting' => 'asc'], 1 => ['unknown' => 'desc']];

        $expected = [
            1 => [
                'key' => 'second_key',
                'title' => 'Second key',
                'sorting' => 50,
            ],
            2 => [
                'key' => 'third_key',
                'title' => 'Third key',
                'sorting' => 50,
            ],
            0 => [
                'key' => 'first_key',
                'title' => 'First key',
                'sorting' => 100,
            ],
        ];

        self::assertEquals($expected, Multisort::sort($original, $sorting));
    }
}
