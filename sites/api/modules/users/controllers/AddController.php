<?php

namespace Controllers;

use Fogito\Lib\Auth;
use Fogito\Lib\Lang;
use Lib\Helpers;
use Fogito\Http\Request;
use Fogito\Http\Response;
use Fogito\Models\CoreUsers;
use Custom\Models\Notes;
use Custom\Models\NoteUsers;
use Lib\Permission;
use Models\Activities;

class AddController
{
    public function index()
    {
        $data = (array)Request::get('data');
        $noteId = (string)$data["id"];
        $userId = (string)$data["user_id"];

        $noteData = Notes::findFirst([[
            "_id" => Notes::objectId($noteId),
            "is_deleted" => ['$ne' => 1],
        ]]);


        $string = ltrim(str_replace('&nbsp;', '', $noteData->content));
        $titleEndPos = strpos($string, '<p>', 1) ?: 400;
        $title = strip_tags(substr($string, 0, $titleEndPos));

        if (!$noteData) {
            Response::error(Lang::get("NoInformation", "Information not found"));
        }

        $userData = CoreUsers::findFirst(["filter" => ["id" => $userId]]);

        $data_for_edit = Notes::findFirst([
            [
                "_id" => Notes::objectId($noteId),
                "users" => Auth::getId(),
            ]
        ]);

        if ($data_for_edit) {
            if(!Permission::allSelected('notes_update')) {
                if(!in_array(Auth::getId(),$noteData->owners)) {
                    $hasPermission = Notes::checkPermission($noteData, 'user_add', Auth::getId());
                    if (!$hasPermission) {
                        Response::error(Lang::get("PermissionNotAllowed"));
                    }
                }
            }

        } else {
            if (!Permission::allSelected('notes_update')) {
                if(!in_array(Auth::getId(),$noteData->owners)) {
                    if ($noteData->creator_id !== Auth::getId() || !Permission::check('notes_update')) {
                        Response::error(Lang::get("PermissionNotAllowed"));
                    }
                }
            }
        }

        if (!$userData) {
            Response::error(Lang::get("UserIsWrong", "User is wrong"));
        }

        $existUser = NoteUsers::findFirst([[
            'note_id' => $noteId,
            'user_id' => $userId,
            'is_deleted' => ['$ne' => 1]
        ]]);

        if ($existUser) {
            Response::error(Lang::get("UserExists", "User is exist"));
        }

        //INSERT NEW Note USER
        NoteUsers::insertDefaultUser($noteId, $userData);

        //Update Note users array
        $noteUsersArray = $noteData->users;
        if (!in_array($userId, $noteUsersArray)) {
            $noteUsersArray[] = $userId;
            Notes::update(["_id" => $noteData->_id], [
                "users" => $noteUsersArray,
                "updated_at" => Notes::getDate(),
            ]);
        }

        // log
        Activities::log([
            'user_id' => Auth::getId(),
            'section' => 'notes',
            'operation' => 'notes_users_add',
            'ids' => [
                'user' => (string)$userId,
                'users' => $noteUsersArray,
                'notes' => (string)$noteId,
            ],
            'values' => [
                'keyword' => $title
            ],
            'status' => 1,
        ]);

        Response::success([], Lang::get("AddedSuccessfully"));
    }
}
