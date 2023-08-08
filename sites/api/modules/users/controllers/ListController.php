<?php

namespace Controllers;

use Fogito\Lib\Lang;
use Lib\Helpers;
use Fogito\Http\Request;
use Fogito\Http\Response;
use Custom\Models\NoteUsers;
use Custom\Models\Notes;
use Models\Users;

class ListController
{
    public function index()
    {
        $data = (array)Request::get('data');
        $noteId = (string)$data["id"];
        $query = $data["query"];
        $userType = $data["user_type"];
        $skip = (int)$data["skip"];
        $limit = Helpers::manageLimitRequest($data['limit']);

        $noteData = Notes::findFirst([[
            "_id" => Notes::objectId($noteId),
            "is_deleted" => ['$ne' => 1],
        ]]);

        if (!$noteData) {
            Response::error(Lang::get("NoInformation", "Information not found"));
        }

        $bind = [
            "note_id" => $noteId,
            'is_deleted' => ['$ne' => 1]
        ];

        $userFilter = '';
        if ($userType) {
            if ($userType == 'user') {
                $userFilter = 'user';
                $bind['user_type'] = 'user';
            }
            elseif ($userType == '') {
                    $userFilter = ['in' => ['user', 'moderator','employee']];
            }
            else {
                $userFilter = ['in' => ['employee', 'moderator']];
                $bind['user_type'] = ['$ne' => 'user'];
            }
        }

        $noteUsers = NoteUsers::find([$bind]);

        $response = [];
        $usersData = [];
        if (count($noteUsers) > 0) {
            $ids = [];
            $noteUsersByKey = [];
            foreach ($noteUsers as $noteUser) {
                $noteUsersByKey[(string)$noteUser->user_id] = $noteUser;
                $ids[] = (string)$noteUser->user_id;
            }

            $userRemoteBind = [ "id" => ['in' => $ids] ];

            if ($userFilter){
                $userRemoteBind['type'] = $userFilter;
            }
            if ($query){
                $userRemoteBind['fullname'] = ['regex' => $query];
            }

            $usersData = Users::findWithProfessions([
                "filter" => $userRemoteBind,
                "columns" => ["id","avatar_tiny","avatars","fullname","type","is_deleted","positions","position","company","gender","firstname","lastname"],
                "skip" => $skip,
                "limit" => $limit,
            ]);

            foreach ($usersData as $userData) {
                $currentUserData = $noteUsersByKey[$userData['id']];
                $userData['custom_permission_status'] = (int)$currentUserData->custom_permission_status == 1 ? 1 : 0;
                $response[] = $userData;
            }
        }

        Response::custom([
            'status' => 'success',
            'data' => $response,
            'count' => count($usersData),
            "skip" => $skip,
            "limit" => $limit,
        ]);
    }
}
