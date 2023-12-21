<?php

namespace App\Helpers;

class CrudHelper
{
    public static function getFormDataStructureWithValues($structure, array $values)
    {
        foreach ($structure as $key => $node) {
            if ($node['type'] == 'group') {
                $structure[$key]['children'] = self::getFormDataStructureWithValues($node['children'], $values);
                if (empty($structure[$key]['children'])) {
                    unset($structure[$key]);
                }
                continue;
            }
            if ($node['type'] == 'input' && in_array($node['id'], array_keys($values))) {
                $structure[$key]['value'] = $values[$node['id']];
            }
            if ($node['type'] == 'input' && !in_array($node['id'], array_keys($values))) {
                unset($structure[$key]);
            }
        }
        return $structure;
    }

    public static function getFormDataSaveJsonStructure($structure)
    {
        $returnStructure = [];
        foreach ($structure as $node) {
            if ($node['type'] == 'group') {
                $returnStructure = array_merge($returnStructure, self::getFormDataSaveJsonStructure($node['children']));
                continue;
            }
            if ($node['type'] == 'input') {
                $returnStructure[] = $node['id'];
            }
        }
        return $returnStructure;
    }

    public static function getSaveDataFormDataJson($structure, $data = [])
    {
        $formDataStructure = self::getFormDataSaveJsonStructure($structure);
        $inputsStructure = self::getInputsStructure($structure);
        $returnSaveData = [];
        foreach ($formDataStructure as $column) {
            $returnSaveData[$column] = $data[$column] ?? null;
            if (isset($inputsStructure[$column]['store_format'])) {
                $returnSaveData[$column] = $inputsStructure[$column]['store_format']($returnSaveData[$column]);
            }
        }
        return $returnSaveData;
    }

    public static function getFormDataValidation($structure)
    {
        $returnStructure = [
            'rules'         =>  [],
            'attributes'    =>  [],
        ];
        foreach ($structure as $node) {
            if ($node['type'] == 'group') {
                $nodeStructure = self::getFormDataValidation($node['children']);
                $returnStructure['rules'] = array_merge($returnStructure['rules'], $nodeStructure['rules']);
                $returnStructure['attributes'] = array_merge($returnStructure['attributes'], $nodeStructure['attributes']);
                continue;
            }
            if ($node['type'] == 'input' && isset($node['validate'])) {
                $returnStructure['rules'][$node['id']] = $node['validate'];
                $returnStructure['attributes'][$node['id']] = $node['label'];
            }
        }

        return $returnStructure;
    }

    public static function getValuesDifference(array $oldValues, array $newValues)
    {
        $differences = array();

        foreach ($oldValues as $key => $value) {
            if (array_key_exists($key, $newValues)) {
                if (is_array($value) && is_array($newValues[$key])) {
                    $subDifferences = self::getValuesDifference($value, $newValues[$key]);
                    if (!empty($subDifferences)) {
                        $differences[$key] = $subDifferences;
                    }
                } elseif ($value !== $newValues[$key]) {
                    $differences[$key] = $newValues[$key];
                }
            } else {
                $differences[$key] = $value;
            }
        }

        foreach ($newValues as $key => $value) {
            if (!array_key_exists($key, $oldValues)) {
                $differences[$key] = $value;
            }
        }

        return $differences;
    }

    public static function getHelperViewFilters($filters)
    {
        return array_filter($filters, function ($filter) {
            return (empty($filter['hide_on_view']) || $filter['hide_on_view'] != true);
        });
    }

    private static function getInputsStructure($structure)
    {
        $returnStructure = [];
        foreach ($structure as $node) {
            if ($node['type'] == 'group') {
                $returnStructure = array_merge($returnStructure, self::getInputsStructure($node['children']));
                continue;
            }
            if ($node['type'] == 'input') {
                $returnStructure[$node['id']] = $node;
            }
        }
        return $returnStructure;
    }
}
