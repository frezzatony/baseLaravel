<?php

namespace App\Lib;

use Illuminate\Contracts\Validation\Validator;

class SearchFilters
{
    private static $operators = [
        'after'                     =>  ['condicao'  =>  "{coluna}::DATE > '{value}'::DATE", 'valor_obrigatorio' => true, 'label' => 'depois de'],
        'after_or_equal'            =>  ['condicao'  =>  "{coluna}::DATE >= '{value}'::DATE", 'valor_obrigatorio' => true, 'label' => 'depois ou igual a'],
        'after_timestamp'           =>  ['condicao'  =>  "{coluna}::TIMESTAMP > '{value}'::TIMESTAMP", 'valor_obrigatorio' => true, 'label' => 'depois de'],
        'after_or_equal_timestamp'  =>  ['condicao'  =>  "{coluna}::DATE >= '{value}'::DATE", 'valor_obrigatorio' => true, 'label' => 'depois ou igual a'],
        'equal'                     =>  ['condicao'  =>  "lower(unaccent({coluna}::TEXT)) = lower(unaccent('{value}'::TEXT))", 'label' => 'igual a'],
        'equal_integer'             =>  ['condicao'  =>  "{coluna} = {value}", 'label' => 'igual a'],
        'equal_date'                =>  ['condicao'  =>  "DATE({coluna})::DATE = DATE('{value}')::DATE", 'valor_obrigatorio' => true, 'label' => 'igual a'],
        'before'                    =>  ['condicao'  =>  "{coluna}::DATE < '{value}'::DATE", 'valor_obrigatorio' => true, 'label' => 'antes de'],
        'before_or_equal'           =>  ['condicao'  =>  "{coluna}::DATE <= '{value}'::DATE", 'valor_obrigatorio' => true, 'label' => 'antes ou igual a'],
        'before_timestamp'          =>  ['condicao'  =>  "{coluna}::TIMESTAMP < '{value}'::TIMESTAMP", 'valor_obrigatorio' => true, 'label' => 'antes de'],
        'before_or_equal_timestamp' =>  ['condicao'  =>  "{coluna}::TIMESTAMP <= '{value}'::TIMESTAMP", 'valor_obrigatorio' => true, 'label' => 'antes ou igual a'],
        'begins_with'               =>  ['condicao'  =>  "unaccent({coluna}::TEXT) ILIKE unaccent('{value}%'::TEXT)", 'label' => 'iniciando com'],
        'ends_with'                 =>  ['condicao'  =>  "unaccent({coluna}::TEXT) ILIKE unaccent('%{value}'::TEXT)", 'label' => 'terminando com'],
        'greater'                   =>  ['condicao'  =>  "{coluna} > {value}", 'valor_obrigatorio' => true, 'label' => 'maior que'],
        'greater_or_equal'          =>  ['condicao'  =>  "{coluna} >= {value}", 'valor_obrigatorio' => true, 'label' => 'maior ou igual a'],
        'is_not_null'               =>  ['condicao'  =>  "{coluna}::TEXT <> '' OR {coluna} NOTNULL", 'operador' => 'OR', 'label' => 'não é nulo/vazio'],
        'is_null'                   =>  ['condicao'  =>  "{coluna}::TEXT = '' OR {coluna} IS NULL", 'operador' => 'OR', 'label' => 'é nulo/vazio'],
        'less'                      =>  ['condicao'  =>  "{coluna} < {value} OR {coluna} IS NULL", 'valor_obrigatorio' => true, 'label' => 'menor que'],
        'less_or_equal'             =>  ['condicao'  =>  "{coluna} <= {value} OR {coluna} IS NULL", 'valor_obrigatorio' => true, 'label' => 'menor ou igual a'],
        'not_begins_with'           =>  ['condicao'  =>  "lower(unaccent({coluna}::TEXT)) NOT ILIKE lower(unaccent('{value}%'::TEXT))", 'operador' => 'AND', 'label' => 'não iniciando com'],
        'not_ends_with'             =>  ['condicao'  =>  "lower(unaccent({coluna}::TEXT)) NOT ILIKE lower(unaccent('{value}%'::TEXT))", 'operador' => 'AND', 'label' => 'não terminando com'],
        'not_equal'                 =>  ['condicao'  =>  "lower(unaccent({coluna}::TEXT)) <> lower(unaccent('{value}'::TEXT))", 'operador' => 'AND', 'label' => 'diferente'],
    ];

    private static $operatorsExceptions = [
        'between'       =>  ['label' => 'entre'],
        'contains'      =>  ['label' => 'contém'],
        'not_contains'  =>  ['label' => 'não contém'],
        'in'            =>  ['label' => 'contido em'],
        'not_in'        =>  ['label' => 'não contido em']
    ];

    public static function getStrWherere($rules = [], $filters = [], $parent = null)
    {
        $strWhere = '';

        foreach ($filters as $keyFilter => $filter) {
            if (
                !empty($filter) && !empty($rules[$keyFilter]) &&
                (
                    (!$parent && empty($rules[$keyFilter]['parent'])) ||
                    ($parent && !empty($rules[$keyFilter]['parent']) && $rules[$keyFilter]['parent'] == $parent)
                )
            ) {
                if (!empty($rules[$keyFilter]['rules'])) {
                    if (!is_string($filter)) {
                        $validator = Validator::make(['filter' => $filter], ['filter' => $rules[$keyFilter]['rules']]);
                    }
                    if (is_array($filter)) {
                        $filterData = [];
                        $filterRules = [];
                        foreach ($filter as $keyitemFilter => $itemFilter) {
                            $filterData["{$keyitemFilter}_{$keyFilter}"] = $itemFiltro['value'] ?? $itemFilter;
                            $filterRules["{$keyitemFilter}_{$keyFilter}"] = $rules[$keyFilter]['rules'];
                        }
                        $validator = Validator::make($filterData, $filterRules);
                    }

                    if (!$validator->passes()) {
                        continue;
                    }
                }

                if (!is_array($filter)) {
                    $strWhereTempFilter = '';
                    if (!empty($rules[$keyFilter]['where'])) {
                        $strWhereTempFilter = str_replace('{filter}', $filter, $rules[$keyFilter]['where']);
                    }
                    if (empty($strWhereTempFilter) && !empty($rules[$keyFilter]['columns'])) {
                        $strWhereTempFilter =  self::getFiltroDinamicoPesquisa([
                            'operator'  =>  'equal',
                            'value'     =>  $filter,
                        ], $rules[$keyFilter]);
                    }
                    $strWhere .= $strWhereTempFilter ? (($strWhere ? ' AND ' : '') . " ($strWhereTempFilter) ") : '';
                }

                if (is_array($filter)) {
                    $strWhereTempFilter = '';
                    foreach ($filter as $key => $tempSearchFilter) {
                        if (is_string($tempSearchFilter)) {
                            $strWhereTempFilter .= $strWhereTempFilter ? ' OR ' : '';
                            if (!empty($rules[$keyFilter]['where'])) {
                                $strWhereTempFilter .= str_replace('{filtro}', $tempSearchFilter, $rules[$keyFilter]['where']);
                            }
                            if (empty($strWhereTempFilter) && !empty($rules[$keyFilter]['columns'])) {
                                $strWhereTempFilter .=  self::getFiltroDinamicoPesquisa([
                                    'operator'  =>  'equal',
                                    'value'     =>  $tempSearchFilter,
                                ], $rules[$keyFilter]);
                            }
                        }
                        if (is_array($tempSearchFilter) && !empty($tempSearchFilter['operator'])) {
                            $strWhereFiltroDinamico = self::getFiltroDinamicoPesquisa($tempSearchFilter, $rules[$keyFilter]);
                            if ($strWhereFiltroDinamico) {
                                $strWhereTempFilter .= ($strWhereTempFilter ? ' OR ' : '') . $strWhereFiltroDinamico;
                            }
                        }
                    }
                    $strWhere .= $strWhereTempFilter ? (($strWhere ? ' AND ' : '') . " ($strWhereTempFilter) ") : '';
                }
            }
        }
        return $strWhere;
    }

    public function getFiltrosFormatados($filtersPesquisa, $filtersAplicados)
    {
        $dados = [];
        foreach ($filtersAplicados as $keyFilter => $valorFiltro) {
            if (in_array($keyFilter, array_keys($filtersPesquisa))) {
                if (!is_array($valorFiltro)) {
                    $dados[] = "{$filtersPesquisa[$keyFilter]['label']} " . self::$operators['equal']['label'] . " $valorFiltro";
                }
                if (is_array($valorFiltro)) {
                    foreach ($valorFiltro as $rowValorFiltro) {
                        $labelOperador = in_array($rowValorFiltro['operator'], array_keys(self::$operators))
                            ? self::$operators[$rowValorFiltro['operator']]['label']
                            : (in_array($rowValorFiltro['operator'], array_keys(self::$operatorsExceptions)) ? self::$operatorsExceptions[$rowValorFiltro['operator']]['label'] : null);
                        $dados[] = "{$filtersPesquisa[$keyFilter]['label']} $labelOperador {$rowValorFiltro['value']}";
                    }
                }
            }
        }
        return $dados;
    }

    private static function between($filterDinamico, $column)
    {
        $datas = is_array($filterDinamico['value']) ? $filterDinamico['value'] : array_map(function ($data) {
            return verificaData($data) ? $data : '';
        }, explode(',', $filterDinamico['value']));

        if (!empty($datas[0] || !empty($datas[1]))) {
            $strWhere = '';
            if (!empty($datas[1])) {
                $filterDinamico['value'] = $datas[1];
                $strWhere = self::getByoperators('before_or_equal', $filterDinamico, $column)['where'];

                if (empty($datas[0])) {
                    $strWhere .= ' OR ' . self::getByoperators('is_null', $filterDinamico, $column)['where'];
                }
            }

            if (!empty($datas[0])) {
                $filterDinamico['value'] = $datas[0];
                $strWhere .= ($strWhere ? ' AND ' : '') . self::getByoperators('after_or_equal', $filterDinamico, $column)['where'];
            }
            return ['where' =>  "($strWhere)"];
        }
        return null;
    }

    private static function contains($filterDinamico, $column)
    {
        $valorFiltro = self::replaceValueToDB($filterDinamico['value'] ?? '');
        if ($valorFiltro) {
            return [
                'where' =>  "
                    (
                        COALESCE(unaccent($column::TEXT),'')
                    )
                    ILIKE ALL (
                        string_to_array(
                            '%' ||
                            regexp_replace(
                                (unaccent('$valorFiltro')::TEXT),
                                '\s+', '% %', 'g'
                            )
                            || '%', ' '
                        )
                    )
                "
            ];
        }
        return null;
    }

    private static function not_contains($filterDinamico, $column)
    {
        $valorFiltro = self::replaceValueToDB($filterDinamico['value'] ?? '');
        if ($valorFiltro) {
            return [
                'where' =>  "(
                    (
                        COALESCE(unaccent($column),'')
                    )
                    NOT ILIKE ALL (
                        string_to_array(
                            '%' ||
                            regexp_replace(
                                (unaccent('$valorFiltro')),
                                '\s+', '% %', 'g'
                            )
                            || '%', ' '
                        )
                    )
                )"
            ];
        }
        return null;
    }

    private static function in($filterDinamico, $column)
    {
        $valorFiltro = self::replaceValueToDB($filterDinamico['value'] ?? '');
        if ($valorFiltro) {
            $arrValoresFiltro = explode(',', $valorFiltro);
            $strFiltro = '';
            foreach ($arrValoresFiltro as $filter) {
                $strFiltro .= $strFiltro ? ',' : '';
                $strFiltro .= "unaccent('$filter'::TEXT)";
            }
            return [
                'where' =>  "
                    (
                        COALESCE(unaccent($column::TEXT),'')::TEXT
                    )
                    IN ($strFiltro)
                "
            ];
        }
        return null;
    }

    private static function not_in($filterDinamico, $column)
    {
        $valorFiltro = self::replaceValueToDB($filterDinamico['value'] ?? '');
        if ($valorFiltro) {
            $arrValoresFiltro = explode(',', $valorFiltro);
            $strFiltro = '';
            foreach ($arrValoresFiltro as $filter) {
                $strFiltro .= $strFiltro ? ',' : '';
                $strFiltro .= "'$filter'::TEXT";
            }
            return [
                'where' =>  "
                    (
                        COALESCE(unaccent($column::TEXT),'')::TEXT
                    )
                    NOT IN ($strFiltro)
                "
            ];
        }
        return null;
    }

    private static function getByoperators($operador, $filterDinamico, $column)
    {
        return [
            'where'     =>  '(' . str_replace(['{coluna}', '{value}'], [$column, self::replaceValueToDB($filterDinamico['value'])], self::$operators[$operador]['condicao']) . ')',
            'operador'  =>  !empty(self::$operators[$operador]['operador']) ? self::$operators[$operador]['operador'] : null,
        ];
    }

    private static function getFiltroDinamicoPesquisa($filterDinamico, $regraFiltro)
    {
        $strWhereFiltroDinamico = '';
        foreach ($regraFiltro['columns'] as $key => $column) {
            if (
                in_array($filterDinamico['operator'], array_keys(self::$operators)) &&
                (empty(self::$operators[$filterDinamico['operator']]['valor_obrigatorio']) || (self::$operators[$filterDinamico['operator']]['valor_obrigatorio'] == true && $filterDinamico['value']))
            ) {
                $dadosWhereFiltroDinamico =  self::getByoperators($filterDinamico['operator'], $filterDinamico, $column);
            }
            if (method_exists(SearchFilters::class, $filterDinamico['operator'])) {
                $dadosWhereFiltroDinamico =  self::{$filterDinamico['operator']}($filterDinamico, $column);
            }

            $strWhereFiltroDinamico .= (!empty($dadosWhereFiltroDinamico) && $strWhereFiltroDinamico) ? (!empty($dadosWhereFiltroDinamico['operador']) ? $dadosWhereFiltroDinamico['operador'] : ' OR ') : '';
            $strWhereFiltroDinamico .= $dadosWhereFiltroDinamico['where'] ?? '';
        }
        return $strWhereFiltroDinamico;
    }

    private static function replaceValueToDB($value)
    {
        $arrSource = array(
            '\\', '\'', '%'
        );
        $arrReplace = array(
            '\\\\', '\'\'', '\\%'
        );
        return str_replace($arrSource, $arrReplace, $value);
    }
}
