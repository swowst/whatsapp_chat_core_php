<?php

namespace Controllers;

use Fogito\Lib\Lang;
use Fogito\Http\Request;
use Fogito\Http\Response;
use Fogito\Models\CoreUsers;
use Custom\Models\Dialogues;
use Custom\Models\S2s;
use Lib\Permission;


class ProjectsController
{
    public function index()
    {
        $response = [];
        $req = (array)Request::get();
        $server_token = (string)$req["server_token"];
        $appId = $req["app_id"];

        if (strlen($server_token) == 0) {
            Response::error("ServerTokenIsWrong", "Server token is wrong");
        } else if (strlen($appId) == 0) {
            Response::error("AppIdWrong");
        } else if ($server_token != S2s::getServerToken($appId)) {
            Response::error("ServerTokenNotMatch");
        }

        $data = $req["data"];

        $boardObjIds = [];
        foreach ((array)$data["project_ids"] as $board) {
            $board = (string)$board;
            if (strlen($board) > 0) {
                $boardObjIds[] = Dialogues::objectId($board);
            }
        }

        $binds = [
            "_id" => ['$in' => $boardObjIds],
//            "is_deleted" => ['$ne' => 1]
        ];

        $boardsData = Dialogues::find([
            $binds,
            "sort" => ["_id" => 1]
        ]);
        $boardsTotalCount = Dialogues::count([$binds]);

        foreach ($boardsData as $value) {
            $response[] = [
                "id" => (string)$value->_id,
                "title" => (string)$value->title,
            ];
        }

        Response::custom([
            'status' => 'success',
            'data' => $response,
            'count' => $boardsTotalCount,
        ]);
    }


    public function hasaccess()
    {
        $response = [];
        $req = (array)Request::get();
        $server_token = (string)$req["server_token"];
        $appId = $req["app_id"];

        if(strlen($server_token) == 0){
            Response::error("ServerTokenIsWrong", "Server token is wrong");
        }else if(strlen($appId) == 0){
            Response::error("AppIdWrong");
        }else if ($server_token != S2s::getServerToken($appId)) {
            Response::error("ServerTokenNotMatch");
        }

        $data = $req["data"];
        $boardId = $data['id'];
        $userId = $data['user_id'];
        $myPermissions = $data['permission'];

        $userData = CoreUsers::findFirst([["id" => (string)$userId]]);

        if (!$userData){
            Response::error(Lang::get("UserNotFound"));
        }

        $boardData = Dialogues::findFirst([[
            "_id" => Dialogues::objectId($boardId),
            "is_deleted" => ['$ne' => 1],
        ]]);

        if (!$boardData) {
            Response::error(Lang::get("NoData", "Object doesn't exist"));
        }

        $boardPermissions = Permission::manualCheck('board_view',$myPermissions);

        if (!$boardPermissions){
            Response::error(Lang::get("PermissionNotAllowed"));
        }

        $isModerator = in_array('all', $boardPermissions["selected"]);
        $isBoardUser = in_array($userId,$boardData->users);

        if (!$isModerator && !$isBoardUser){
            Response::error(Lang::get("PermissionNotAllowed"));
        }

        Response::success(true, Lang::get("UserIsAllowed", "User is allowed"));
    }



    public function update()
    {
        $req = (array)Request::get();
        $server_token = (string)$req["server_token"];
        $appId = $req["app_id"];

        if (strlen($server_token) == 0) {
            Response::error("ServerTokenIsWrong", "Server token is wrong");
        } else if (strlen($appId) == 0) {
            Response::error("AppIdWrong");
        } else if ($server_token != S2s::getServerToken($appId)) {
            Response::error("ServerTokenNotMatch");
        }

        $data = $req['data'];
        $filters = (array)$data["filter"];
        $updateData = (array)$data["update"];


        if(count($filters) == 0){
            Response::error(Lang::get("FiltersNotSet"));
        }
        if(count($updateData) == 0){
            Response::error(Lang::get("UpdateNotSet"));
        }

        $validFilters = [];
        $allowedFilters = [
            '_id',
            'id',
            'is_deleted',
        ];

        foreach ($filters as $key => $val){
            if (in_array($key,$allowedFilters)){
                $validFilters[$key] = $val;

                if ($key == 'id'){
                    unset($validFilters['id']);
                    if(is_array($val)){
                        $validFilters['_id'] = [
                            $val[0] => array_map(function ($id) {
                                return Dialogues::objectId($id);
                            }, (array)$val[1])
                        ];
                    }else{
                        $validFilters['_id'] = Dialogues::objectId($val);
                    }
                }
            }
        }

        $allowedFields = [
            'duration_total',
        ];

        $validUpdateData = [];
        foreach ($updateData as $key=>$value){
            if(in_array($key,$allowedFields)){
                $validUpdateData[$key] = $value;
            }
        }

        if(count($validUpdateData) == 0){
            Response::error(Lang::get("ValidUpdateError"));
        }
        if(count($validFilters) == 0){
            Response::error(Lang::get("ValidFilterError"));
        }

        $validUpdateData['updated_at'] = Dialogues::getDate();

        $result = Dialogues::update($validFilters,$validUpdateData);

        if ($result){
            Response::success([], Lang::get("ProjectUpdated"));
        }else{
            Response::error(Lang::get("NotUpdated"));
        }
    }

}
