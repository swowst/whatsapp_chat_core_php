<?php

namespace Controllers;

use Custom\Models\Notes;
use Custom\Models\NoteUsers;
use Fogito\Lib\Lang;
use Fogito\Http\Request;
use Fogito\Http\Response;

class ListController
{
    public function index()
    {
        $data = (array)Request::get('data');
        $note_id = (string)$data["id"];
        $user_id = (string)$data["user_id"];

        $data = false;
        if (strlen($note_id) > 0) {
            $data = Notes::findFirst([[
                '_id' => Notes::objectId($note_id),
                "is_deleted" => ['$ne' => 1]
            ]]);
        }

        $noteUser = false;
        if (strlen($user_id) > 5) {
            $noteUser = NoteUsers::findFirst([
                [
                    'note_id' => $note_id,
                    'user_id' => $user_id,
                    'is_deleted' => 0,
                ]
            ]);
        }


        if (strlen($user_id) > 5 && !$noteUser) {
            Response::error(Lang::get("NoInformation"));
        } elseif (strlen($note_id) > 0 && !$data) {
            Response::error(Lang::get("NoInformation"));
        }

        $permissionsFiltered = NoteUsers::getPermissions(Notes::permissionList(), $data, $noteUser, $user_id);
        $permissions = [];
        $construct = Notes::permissionList();

        foreach ($construct as $key => $value){
            $permissions[] = [
                "key" => $value["value"],
                "title" => $value["title"],
                "value" => @$permissionsFiltered[$key] === null ? $value['default'] : $permissionsFiltered[$key],
                "warning" => strlen($user_id) == 0 && $value["warning"] ? $value["warning"] : false,
            ];
        }

        $response = [
            "status" => "success",
            "data" => $permissions,
        ];
        if ($noteUser) {
            $response['custom_permission_status'] = (int)$noteUser->custom_permission_status == 1 ? 1 : 0;
        }
        Response::custom($response);
    }
}
