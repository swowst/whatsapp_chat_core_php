<?php
namespace Models;

use Fogito\Db\RemoteModelManager;
use Fogito\Lib\Auth;

class ProjectsRemote extends RemoteModelManager
{
    public static function getServer()
    {
        return API_S2S_TASK;
    }

    public static function getSource()
    {
        return "projects";
    }

    public static function hasAccess($projectId)
    {
        $params = [
            "id" => $projectId,
            "user_id" => Auth::getId(),
            "permission" => Auth::getPermissions(),
        ];

        self::init('hasaccess');
        $remoteRes = RemoteModelManager::request($params,['result' => true]);

        return $remoteRes['status'] == 'success';
    }

    public static function getListByIds($ids)
    {
        $params = [
            "project_ids"     => $ids,
        ];
        self::init('index');
        $response = RemoteModelManager::request($params);
        return $response;
    }

    public static function getListById($id)
    {
        $data = self::getListByIds([$id]);
        if($data)
            return $data[0];
        return false;
    }


    public static function findFirstAndSet($user, $params=[])
    {
        $data = self::findAndSet([$user], $params);
        return $data[0];
    }

    public static function findAndSet($list, $params=[])
    {
        $keyFrom    = $params["key_from"] ? $params["key_from"]: "board";
        $keyTo      = $params["key_to"] ? $params["key_to"]: "board";

        $ids = [];
        foreach ($list as $value)
            if(strlen($value[$keyFrom]) > 10)
                $ids[] = (string)$value[$keyFrom];


        // ########## start Fetch ############
        $dataById = [];
        if(count($ids) > 0)
        {
            $query = self::getListByIds($ids);
            foreach ($query as $value)
                $dataById[(string)$value["id"]] = [
                    "id"        => (string)$value["id"],
                    "title"     => (string)$value["title"],
                ];
        }
        // ########## end Fetch ############


        $data = [];
        foreach ($list as $value)
        {
            $value[$keyTo] = $dataById[$value[$keyFrom]] ? $dataById[$value[$keyFrom]] : false;
            $data[] = $value;
        }

        return $data;
    }

}
