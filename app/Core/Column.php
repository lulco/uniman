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
     * value is size and will be formatted as size
     * @var boolean
     */
    private $isSize = false;

    /**
     * value is time and will be formatted as time
     * @var boolean
     */
    private $isTime = false;

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

    /**
     * @param string $key key in item result array
     * @param string $title title (string is translated in template)
     */
    public function __construct($key, $title)
    {
        $this->key = $key;
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param boolean $isSortable
     * @return Column
     */
    public function setSortable($isSortable)
    {
        $this->isSortable = $isSortable;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isSortable()
    {
        return $this->isSortable;
    }

    /**
     * @param boolean $isFilterable
     * @param array $filters list of available filters for column
     * @return Column
     */
    public function setFilterable($isFilterable, array $filters = Filter::DEFAULT_FILTER_OPERATORS)
    {
        $this->isFilterable = $isFilterable;
        $this->filters = $filters;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isFilterable()
    {
        return $this->isFilterable;
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @param boolean $isNumeric
     * @return Column
     */
    public function setNumeric($isNumeric)
    {
        $this->isNumeric = $isNumeric;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isNumeric()
    {
        return $this->isNumeric;
    }

    /**
     * @param integer $decimals
     * @return Column
     */
    public function setDecimals($decimals)
    {
        $this->decimals = $decimals;
        return $this;
    }

    /**
     * @return integer
     */
    public function getDecimals()
    {
        return $this->decimals;
    }

    /**
     * @param boolean $isSize
     * @return Column
     */
    public function setSize($isSize)
    {
        $this->isSize = $isSize;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isSize()
    {
        return $this->isSize;
    }

    /**
     * @param boolean $isTime
     * @return Column
     */
    public function setTime($isTime)
    {
        $this->isTime = $isTime;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isTime()
    {
        return $this->isTime;
    }

    /**
     * @param string $info
     * @return Column
     */
    public function setInfo($info)
    {
        $this->info = $info;
        return $this;
    }

    /**
     * @return string
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * @param string $infoUrl
     * @return Column
     */
    public function setInfoUrl($infoUrl)
    {
        $this->infoUrl = $infoUrl;
        return $this;
    }

    /**
     * @return string
     */
    public function getInfoUrl()
    {
        return $this->infoUrl;
    }

    /**
     * @param string $database
     * @param string $table
     * @param Closure $callback function to create item identifier
     * @return Column
     */
    public function setExternal($database, $table, Closure $callback)
    {
        $this->external = [
            'database' => $database,
            'table' => $table,
            'callback' => $callback,
        ];
        return $this;
    }

    /**
     * @return array
     */
    public function getExternal()
    {
        return $this->external;
    }
}
