<?php

namespace App\Services\System;

use App\Services\CrudService;

class ModuleMenuService extends CrudService
{
    public static function getModuleMenuByModuleIdAndUserId(int $idModule, int $idUser)
    {
        $sql = "
            WITH RECURSIVE
                treeMenu AS (
                    SELECT
                        *,
                        1 AS LVL,
                        ARRAY [TMenus.id] AS ancestry
                    FROM
                        modules_menus TMenus
                    WHERE
                        TMenus.modules_menus_id_parent ISNULL
                    UNION ALL
                    SELECT
                        TMenus.*,
                        treeMenu.LVL + 1,
                        array_append(treeMenu.ancestry, TMenus.id) AS ancestry
                    FROM
                        modules_menus TMenus
                    JOIN treeMenu ON
                        treeMenu.id = TMenus.modules_menus_id_parent
                )            
            SELECT DISTINCT 
                treeMenu.*, array_to_json(ancestry)::jsonb AS ancestry,
                array_length(ancestry,1)
            FROM
                treeMenu
            LEFT JOIN routines_actions ON routines_actions.id = treeMenu.routines_actions_id
            LEFT JOIN routines ON routines.id = routines_actions.routines_id
            LEFT JOIN profile_routines_actions ON profile_routines_actions.routines_actions_id = routines_actions.id 
            LEFT JOIN users_profiles ON users_profiles.profiles_id = profile_routines_actions.profiles_id
            LEFT JOIN users ON users.id = {$idUser} AND users.is_master = 't'
            WHERE
                treeMenu.modules_id = {$idModule} AND (
                    (treeMenu.attributes->>'href' ISNULL AND routines_actions.id IS NULL) OR
                    (treeMenu.attributes->>'href' IS NOT NULL AND routines_actions.id IS NOT NULL AND routines.id IS NOT NULL AND routines.is_active = 't' AND users_profiles.users_id = {$idUser}) OR
                    (users.id IS NOT NULL)
                )
            ORDER BY array_length(ancestry,1),list_order
        ";


        $menu = parent::genLazyCollectionFromSql($sql);
        return self::buildValidTree($menu->toArray());
    }

    private static function buildValidTree($items, $parentId = null)
    {
        $tree = [];
        foreach ($items as $item) {
            $item = (array)$item;
            if ($item['modules_menus_id_parent'] == $parentId) {
                $children = self::buildValidTree($items, $item['id']);
                if ($children) {
                    $item['children'] = $children;
                }
                if ($children || $item['attributes']->get('href')) {
                    $tree[] = $item;
                }
            }
        }
        return $tree;
    }
}
