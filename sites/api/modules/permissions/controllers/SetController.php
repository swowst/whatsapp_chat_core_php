<?php

namespace Controllers;

use Custom\Models\Documents;
use Custom\Models\Notes;
use Custom\Models\NoteUsers;
use Fogito\Lib\Auth;
use Fogito\Lib\Lang;
use Fogito\Http\Request;
use Fogito\Http\Response;
use Lib\Permission;
use Models\Activities;

class SetController
{
    public function index()
    {
        $data = Request::get("data");
        $field = (string)$data["field"];
        $value = (int)$data["value"];
        $note_id = (string)$data["id"];
        $user_id = (string)$data["user_id"];


        if (!array_key_exists($field, Notes::permissionList())) {
            Response::error(Lang::get("NoInformation"));
        }

        $noteData = Notes::findFirst([[
            '_id' => Notes::objectId($note_id),
            "is_deleted" => ['$ne' => 1],
        ]]);

        $noteUser = false;
        if (strlen($user_id) > 5) {
            $noteUser = NoteUsers::findFirst([[
                'note_id' => $note_id,
                'user_id' => $user_id,
                'is_deleted' => 0,
            ]]);
        }

        $data_for_edit = Notes::findFirst([
            [
                "_id" => Notes::objectId($note_id),
                "users" => Auth::getId(),
            ]
        ]);

        if ($data_for_edit) {
            if (!Permission::allSelected('notes_update')) {
                if(!in_array(Auth::getId(),$noteData->owners)) {
                    $hasPermission = Notes::checkPermission($noteData, 'set_permission', Auth::getId());
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
            if(!Permission::allSelected('notes_update') && !in_array(Auth::getId(),$noteData->users)) {
                if(!in_array(Auth::getId(),$noteData->owners)) {
                    Response::error(Lang::get("PermissionNotAllowed"));
                }
            }
        }

        if (strlen($user_id) > 0) {
            if ($noteData->creator_id == $user_id) {
                Response::error(Lang::get("YouCantChangeYourPermission"));
            } else if (!$noteData) {
                Response::error(Lang::get("NoteRequired"));
            } elseif (strlen($user_id) > 5 && !$noteUser) {
                Response::error(Lang::get("noUser"));
            } elseif ($noteUser && $noteUser->custom_permission_status == 0) {
                Response::error(Lang::get("UserPermissionStatusDisabled"));
            } elseif ($noteUser && $noteData->creator_id == $user_id) {
                Response::error(Lang::get("YouCantChangeCreatorPermission"));
            }
        }


        $newPermissions = [];
        $newPermissions[$field] = $value == 1;

        if ($newPermissions['set_permission'] == true) {
            foreach (Notes::permissionList() as $key => $value) {
                $newPermissions[$key] = true;
            }
        }


        if (strlen($user_id) > 5) {
            $oldPermissions = $noteUser->permissions ? (array)$noteUser->permissions : [];
            NoteUsers::update(["_id" => $noteUser->_id], [
                "permissions" => array_merge($oldPermissions, $newPermissions)
            ]);
        } else {
            $oldPermissions = $noteData->permissions ? (array)$noteData->permissions : [];
            Notes::update(["_id" => $noteData->_id], [
                "permissions" => array_merge($oldPermissions, $newPermissions)
            ]);

        }

        // log
        Activities::log([
            'user_id' => Auth::getId(),
            'section' => 'notes',
            'operation' => 'notes_users_update',
            'ids' => [
                'user' => (string)$user_id,
                'users' => (array)$noteData->users,
                'notes' => (string)$note_id,
            ],
            'status' => 1,
        ]);

        Response::success([], Lang::get("UpdatedSuccessfully"));
    }
}
