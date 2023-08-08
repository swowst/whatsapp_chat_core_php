<?php

namespace Controllers;

use Custom\Models\Documents;
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

class EditController
{
    public function index()
    {
        $data = (array)Request::get('data');
        $noteId = (string)$data["id"];
        $userId = (string)$data["user_id"];
        $field = (string)$data["field"];
        $value = $data["value"];

        $noteData = Notes::findFirst([[
            "_id" => Notes::objectId($noteId),
            "is_deleted" => ['$ne' => 1],
        ]]);

        if (!$noteData) {
            Response::error(Lang::get("NoInformation", "Information not found"));
        }

        $data_for_edit = Notes::findFirst([
            [
                "_id" => Notes::objectId($noteId),
                "users" => Auth::getId(),
            ]
        ]);

        if (!is_null($data_for_edit)) {
            if(!Permission::allSelected('notes_update')) {
                if (!in_array(Auth::getId(), $noteData->owners)) {
                    $hasPermission = Notes::checkPermission($noteData, 'set_permission', Auth::getId());
                    if (!$hasPermission) {
                        Response::error(Lang::get("PermissionNotAllowed"));
                    }
                }
            }
        } else {
            if (!Permission::allSelected('notes_update')) {
                if (!in_array(Auth::getId(), $noteData->owners)) {
                    if ($noteData->creator_id !== Auth::getId() || !Permission::check('notes_update')) {
                        Response::error(Lang::get("PermissionNotAllowed"));
                    }
                }
            }
        }

        $editedNoteUser = NoteUsers::findFirst([[
            'note_id' => $noteId,
            'user_id' => $userId,
            'is_deleted' => ['$ne' => 1]
        ]]);

        if (!$editedNoteUser) {
            Response::error(Lang::get("noUser"));
        } elseif (!in_array($field, ['permission'])) {
            Response::error(Lang::get("FieldNotFound"));
        }

        if ($field == 'permission') {
            $value = (int)$value == 1 ? 1 : 0;
            $newPermissions = [];
            $currentNotePermissions = NoteUsers::getPermissions(Notes::permissionList(), $noteData);
            foreach ($currentNotePermissions as $key => $v) {
                $newPermissions[$key] = $v;
            }

            NoteUsers::update(["_id" => $editedNoteUser->_id], [
                "permissions" => $value == 1 ? $newPermissions : [],
                'custom_permission_status' => $value
            ]);
        }

        // log
        Activities::log([
            'user_id' => Auth::getId(),
            'section' => 'notes',
            'operation' => 'notes_users_update',
            'ids' => [
                'user' => (string)$userId,
                'users' => (array)$noteData->users,
                'notes' => (string)$noteId,
            ],
            'status' => 1,
        ]);

        Response::success([], Lang::get("UpdatedSuccessfully"));
    }
}
