<?php

namespace Adminerng\Core;

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
     * value is number and should be align to right
     * @var boolean
     */
    private $isNumeric = false;

    /**
     * number of decimals in numeric value
     * @var integer
     */
    private $decimals = 0;

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
}
