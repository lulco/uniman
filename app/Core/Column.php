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
}
