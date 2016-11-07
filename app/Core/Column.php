<?php

namespace Adminerng\Core;

use Closure;

class Column
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

    /**
     * key in item result array
     * @var string
     */
    private $key;

    /**
     * title (string is translated in template)
     * @var string
     */
    private $title;

    /**
     * result can be orderer by this column
     * @var boolean
     */
    private $isSortable = false;

    /**
     * result can be filtered by this column
     * @var boolean
     */
    private $isFilterable = false;

    /**
     * list of filters for column
     * @var array
     */
    private $filters = [];

    /**
     * value is number and should be align to right
     * @var boolean
     */
    private $isNumeric = false;

    /**
     * number of decimals in numeric value
     * @var integer
     */
    private $decimals = 0;

    /**
     * extended info about column
     * @var string
     */
    private $info;

    /**
     * link to extended info about column
     * @var string
     */
    private $infoUrl;

    /**
     * external url settings for related items
     * consists of 3 parts:
     *  - database - database name
     *  - table - table name
     *  - callback - function to create item identifier
     * @var array
     */
    private $external = [];

    public function setKey($key)
    {
        $this->key = $key;
        return $this;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setIsSortable($isSortable)
    {
        $this->isSortable = $isSortable;
        return $this;
    }

    public function isSortable()
    {
        return $this->isSortable;
    }

    public function setIsFilterable($isFilterable, $filters = self::DEFAULT_FILTER_OPERATORS)
    {
        $this->isFilterable = $isFilterable;
        $this->filters = $filters;
        return $this;
    }

    public function isFilterable()
    {
        return $this->isFilterable;
    }

    public function getFilters()
    {
        return $this->filters;
    }

    public function setIsNumeric($isNumeric)
    {
        $this->isNumeric = $isNumeric;
        return $this;
    }

    public function isNumeric()
    {
        return $this->isNumeric;
    }

    public function setDecimals($decimals)
    {
        $this->decimals = $decimals;
        return $this;
    }

    public function getDecimals()
    {
        return $this->decimals;
    }

    public function setInfo($info)
    {
        $this->info = $info;
        return $this;
    }

    public function getInfo()
    {
        return $this->info;
    }

    public function setInfoUrl($infoUrl)
    {
        $this->infoUrl = $infoUrl;
        return $this;
    }

    public function getInfoUrl()
    {
        return $this->infoUrl;
    }

    public function setExternal($database, $table, Closure $callback)
    {
        $this->external = [
            'database' => $database,
            'table' => $table,
            'callback' => $callback,
        ];
        return $this;
    }

    public function getExternal()
    {
        return $this->external;
    }
}
