<?php

namespace Adminerng\Core;

use Adminerng\Core\Utils\Filter;
use Closure;

class Column
{
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

    public function setIsFilterable($isFilterable, $filters = Filter::DEFAULT_FILTER_OPERATORS)
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
