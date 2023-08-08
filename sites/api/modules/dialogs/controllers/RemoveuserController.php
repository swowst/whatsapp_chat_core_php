<?php

namespace Controllers;

use Custom\Models\Dialogues;
use Fogito\Lib\Lang;
use Fogito\Http\Request;
use Fogito\Http\Response;
use Fogito\Lib\Auth;

class RemoveUserController
{

    public function index()
    {
        $data = (array)Request::get('data');
        $dialogId = (string)$data["dialog_id"];
        $userIds = $data["users"];
        $loggedInUserId = Auth::getId();

        $dialogue = Dialogues::findFirst([
            "_id" => Dialogues::objectId($dialogId),
            "is_deleted" => 0
        ]);

        if (!$dialogue) {
            Response::error(Lang::get("DialogueNotFound", "Dialogue not found"));
        }

        $creatorId = $dialogue->creator_id;

        if ($creatorId !== $loggedInUserId) {
            Response::error(Lang::get("Unauthorized", "You are not authorized to perform this action"));
        }

        $users = $dialogue->users;

        foreach ($userIds as $userId) {
            $key = array_search($userId, $users);
            if ($key !== false) {
                unset($users[$key]);
            }
        }

        Dialogues::update([
            "_id" => Dialogues::objectId($dialogId),

        ],[
            "users" => $users,
            "updated_at" => Dialogues::getDate(),
        ]);

        Response::success([
            'dialog_id' => $dialogId,
            'users' => $users,
        ], Lang::get("UserRemovedSuccessfully", "User removed from group successfully"));
    }
}
