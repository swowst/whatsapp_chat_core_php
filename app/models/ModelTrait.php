<?php

namespace Models;

trait ModelTrait {

    public static function findAndReturnByKey($array = []){
        $arrayList = self::find($array);
        $listByKey = [];
        foreach ($arrayList as $list){
            $listByKey[(string)$list->_id] = $list;
        }

        return $listByKey;
    }



}
