<?php

namespace Adminerng\Core\Permissions;

interface PermissionsInterface
{
    public function canCreateItem($database, $type, $table);

    public function canEditItem($database, $type, $table, $item);

    public function canDeleteItem($database, $type, $table, $item);

    public function canCreateTable($database, $type);

    public function canEditTable($database, $type, $table);

    public function canDeleteTable($database, $type, $table);

    public function canCreateDatabase();

    public function canEditDatabase($database);

    public function canDeleteDatabase($database);
}
