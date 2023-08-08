<?php

namespace Controllers;

use Custom\Models\Dialogues;
use Fogito\Lib\Auth;
use Fogito\Lib\Lang;
use Fogito\Http\Request;
use Fogito\Http\Response;


class UsersController
{

    public function add()
    {
        $data = (array) Request::get('data');
        $dialogId = (string) $data["dialog_id"];
        $selectedUser = (string) $data["selected_user"];

        $dialogData = Dialogues::findFirst([
            [
                "_id" => Dialogues::objectId($dialogId),
                "is_deleted" => 0
            ]
        ]);

        $hasPermission = Dialogues::checkPermission($dialogData, 'user_add');
        if (!$hasPermission) {
            Response::error(Lang::get("PermissionNotAllowed"));
        }

        if (!$dialogData) {
            Response::error(Lang::get("DialogNotFound", "Dialog not found"));
        }

        $users = $dialogData->users;
        $userJoinedAt = Dialogues::getDate();

        $modifiedUsers = [];
        foreach ($users as $user){
            $modifiedUsers[] = [
                "id" => $user,
                "joined" => $userJoinedAt
            ];
        }

        if (!in_array($selectedUser, $users)) {
            $visibleMessages = [];

            foreach ($dialogData->messages as $message) {
                $messageTime = $message->created_at;
                $messageSender = $message->user_id;

                if (in_array($messageSender, $users)) {
                    if ($messageTime >= $userJoinedAt) {
                        $visibleMessages[] = $message;
                    }
                }
            }

            $users[] = [
                "user_id" => $selectedUser,
                "joined_at" => $userJoinedAt
            ];
        }

        $dialogData->users = $users;

        Dialogues::update([
            "_id" => Dialogues::objectId($dialogId),
        ], [
            "users" => $modifiedUsers,
            "updated_at" => Dialogues::getDate(),
        ]);

        Response::success([
            'dialog_id' => $dialogId,
            'users' => $users,
            'visible_messages' => $visibleMessages
        ], Lang::get("UserAddedSuccessfully", "User added to group message successfully"));
    }



    public function remove()
    {
        $data = (array) Request::get('data');
        $dialogId = (string) $data["dialog_id"];
        $selectedUser = (string) $data["selected_user"];

        $dialogData = Dialogues::findFirst([[
            "_id" => Dialogues::objectId($dialogId),
            "is_deleted" => 0
        ]]);

        $hasPermission = Dialogues::checkPermission($dialogData,'user_remove');
        if (!$hasPermission) {
            Response::error(Lang::get("PermissionNotAllowed"));
        }

        if (!$dialogData) {
            Response::error(Lang::get("DialogNotFound", "Dialog not found"));
        }

        $users = $dialogData->users;

        if (($key = array_search($selectedUser, $users)) !== false) {
            unset($users[$key]);
        } else {
            Response::error(Lang::get("UserNotFound", "User not found"));
        }

        $dialogData->users = $users;

        Dialogues::update([
            "_id" => Dialogues::objectId($dialogId),

        ],[
            "users" => $users,
            "updated_at" => Dialogues::getDate()
        ]);

        Response::success([
            'dialog_id' => $dialogId,
            'users' => $users,
        ], Lang::get("UserRemovedSuccessfully", "User removed from group message successfully"));
    }
}

