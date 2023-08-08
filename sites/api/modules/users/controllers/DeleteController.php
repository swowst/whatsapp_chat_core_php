<?php

namespace Controllers;

use Custom\Models\Notes;
use Custom\Models\NoteUsers;
use Fogito\Lib\Auth;
use Fogito\Lib\Lang;
use Lib\Helpers;
use Fogito\Http\Request;
use Fogito\Http\Response;
use Lib\Permission;
use Models\Activities;

class DeleteController
{
    public function index()
    {
        $data = (array)Request::get('data');
        $id = (string)$data["id"];
        $removingUserId = (string)$data["user_id"];

        $noteData = Notes::findFirst([[
            "_id" => Notes::objectId($id),
            "is_deleted" => ['$ne' => 1],
        ]]);


        $data_for_edit = Notes::findFirst([
            [
                "_id" => Notes::objectId($id),
                "users" => Auth::getId(),
            ]
        ]);

        if ($data_for_edit) {
            $removingUserNoteAdmin = Notes::checkPermission($noteData, 'set_permission', $removingUserId);
            $myUserNoteAdmin = Notes::checkPermission($noteData, 'set_permission');
            $myUserCanRemoveUser = Notes::checkPermission($noteData, 'user_add');
            if (!$myUserCanRemoveUser && $removingUserId != Auth::getId()) {
                Response::error(Lang::get("PermissionNotAllowed"));
            } else if ($removingUserId == $noteData->creator_id && $removingUserId != Auth::getId()) {
                Response::error(Lang::get("YouCantRemoveCreator"));
            } else if ($removingUserNoteAdmin && !$myUserNoteAdmin) {
                Response::error(Lang::get("YouCantRemoveAdmin"));
            }
        } else {
            if(!Permission::allSelected('notes_update')) {
                if(!in_array(Auth::getId(),$noteData->owners)) {
                    if ($noteData->creator_id !== Auth::getId() || !Permission::check('notes_update')) {
                        Response::error(Lang::get("PermissionNotAllowed"));
                    }
                }
            }
        }

        $title = $noteData->content;


        $removingNoteUser = NoteUsers::findFirst([[
            'note_id' => $id,
            'user_id' => $removingUserId,
            'is_deleted' => ['$ne' => 1]
        ]]);

        if (!$noteData) {
            Response::error(Lang::get("NoInformation", "Information not found"));
        }else if (!$removingNoteUser) {
            Response::error(Lang::get("noUser", "User is not exists"));
        } elseif ((count($noteData->users) - 1) == 0 && !$noteData->folder_id) {
            Response::error(Lang::get("UserRequiredIfFolderNotSet"));
        }

        $newUsersArray = [];
        foreach ($noteData->users as $user){
            if ($user != $removingUserId && strlen($user) > 10){
                $newUsersArray[] = $user;
            }
        }

        NoteUsers::update(["_id" => $removingNoteUser->_id], [
            "is_deleted" => 1,
            "deleter_id" => Auth::getId(),
            "updated_at" => NoteUsers::getDate()
        ]);

        Notes::update(["_id" => $noteData->_id], [
            "users" => $newUsersArray,
            "updated_at" => Notes::getDate(),
        ]);

        // log
        Activities::log([
            'user_id'   => Auth::getId(),
            'section'   => 'notes',
            'operation' => 'notes_users_delete',
            'ids'    => [
                'user'     => (string) $removingUserId,
                'users' => $noteData->users,
                'notes' => (string) $id,
            ],
            'values' => [
                'keyword' => $title
            ],
            'status'    => 1,
        ]);

        Response::success([], Lang::get("DeletedSuccessfully", "Deleted successfully"));

    }
}