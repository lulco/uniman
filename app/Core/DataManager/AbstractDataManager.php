<?php

namespace UniMan\Core\DataManager;

abstract class AbstractDataManager implements DataManagerInterface
{
    /**
     * @var array list of messages in format flash message => type of flash message (info, success, warning, danger)
     */
    protected $messages = [];

    /**
     * @param array $sorting
     * @return array
     */
    public function databasesKeyValue(array $sorting = [])
    {
        $databases = array_map(function ($database) {
            return $database[$this->getDatabaseNameColumn()];
        }, $this->databases($sorting));
        return $databases;
    }

    /**
     * @param string $identifier
     * @return string
     */
    public function databaseName($identifier)
    {
        $databases = $this->databasesKeyValue();
        return isset($databases[$identifier]) ? $databases[$identifier] : $identifier;
    }

    /**
     * Implement this method if permission canDeleteItem is true
     * @param string $type
     * @param string $table
     * @param string $item
     * @return boolean|null
     * @see DataManagerInterface
     */
    public function deleteItem($type, $table, $item)
    {
        return null;
    }

    /**
     * Implement this method if permission canDeleteTable is true
     * @param string $type
     * @param string $table
     * @return boolean|null
     * @see DataManagerInterface
     */
    public function deleteTable($type, $table)
    {
        return null;
    }

    /**
     * Implement this method if permission canDeleteDatabase is true
     * @param string $database
     * @return boolean|null
     * @see DataManagerInterface
     */
    public function deleteDatabase($database)
    {
        return null;
    }

    /**
     * Implement this method if permission canExecuteCommands is true
     * @param string $commands
     * @return array|null
     * @see DataManagerInterface
     */
    public function execute($commands)
    {
        return null;
    }

    /**
     * @return array
     * @see DataManagerInterface
     */
    final public function getMessages()
    {
        return $this->messages;
    }

    /**
     * add message to list of messages
     * @param string $message
     * @param string $type
     */
    final protected function addMessage($message, $type = 'info')
    {
        $this->messages[$message] = $type;
    }

    /**
     * @return string Name of column where database name is stored
     */
    abstract protected function getDatabaseNameColumn();
}
