<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

class DBHelper
{
    public static function checkCanDeleteRow($schema, $table, $id, $pkColumn = 'id')
    {
        $from = (!empty($schema) ? "\"{$schema}\"." : '') . $table;
        $item = DB::select("
            SELECT *
            FROM {$from}
            WHERE $pkColumn::TEXT = '$id'::TEXT
            LIMIT 1
        ");

        if (!empty($item) && isset($item[0]->can_delete) && !$item[0]->can_delete) {
            return [
                'can_delete'    =>  false,
            ];
        }

        $tableReferences = DB::select("
            SELECT 
                r.*, fk.delete_rule
            FROM information_schema.constraint_column_usage       u
            INNER JOIN information_schema.referential_constraints fk
                    ON u.constraint_catalog = fk.unique_constraint_catalog
                        AND u.constraint_schema = fk.unique_constraint_schema
                        AND u.constraint_name = fk.unique_constraint_name
            INNER JOIN information_schema.key_column_usage        r
                    ON r.constraint_catalog = fk.constraint_catalog
                        AND r.constraint_schema = fk.constraint_schema
                        AND r.constraint_name = fk.constraint_name
            WHERE
                u.table_schema = '$schema' AND
                u.table_name = '$table'
        ");

        $response = [
            'tables'        =>  [],
        ];
        foreach ($tableReferences as $tableReference) {
            $inUse = DB::select("
                SELECT 't'::bool AS in_use
                FROM \"{$tableReference->table_schema}\".{$tableReference->table_name}
                WHERE {$tableReference->column_name}::TEXT = '{$id}'::TEXT
                LIMIT 1
            ");
            if ($inUse && mb_strtoupper($tableReference->delete_rule) != 'CASCADE') {
                $response['tables'][] = "{$tableReference->table_schema}.{$tableReference->table_name}";
            }
        }

        return empty($response['tables']) ? true : $response;
    }

    public static function enumAsSqlTableRecordset($enum)
    {
        $sql = '';
        foreach ($enum::getInstances() as $column) {
            $sql .= ($sql ? ', ' : '') . "('{$column->value['value']}','{$column->value['label']}')";
        }
        return "SELECT * FROM (VALUES $sql) as t(id,value) ORDER BY value ASC";
    }

    public static function temp($value)
    {
        DB::beginTransaction();
        DB::table('debug.temp')->insert([
            'value' => (is_array($value) || is_object($value) ? json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : $value)
        ]);
        DB::commit();
    }
}
