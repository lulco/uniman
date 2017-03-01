<?php

namespace UniMan\Core\Utils;

class Multisort
{
    public static function sort($data, array $sorting = [])
    {
        if (empty($sorting)) {
            return $data;
        }

        foreach ($data as $key => $row) {
            foreach ($row as $column => $value) {
                $col = '__' . $column . '__';
                if (!isset($$col)) {
                    $$col = [];
                }
                ${$col}[$key] = $value;
            }
        }

        $args = [];
        foreach ($sorting as $sort) {
            foreach ($sort as $key => $direction) {
                $col = '__' . $key . '__';
                if (!isset($$col)) {
                    continue;
                }
                $args[] = $$col;
                $args[] = strtolower($direction) == 'asc' ? SORT_ASC : SORT_DESC;
            }
        }

        if (empty($args)) {
            return $data;
        }

        $keys = array_keys($data);
        $args[] = &$data;
        $args[] = &$keys;

        call_user_func_array('array_multisort', $args);
        return array_combine($keys, $data);
    }
}
