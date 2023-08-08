<?php

namespace Controllers;

use Custom\Models\Dialogues;
use Fogito\Lib\Lang;
use Fogito\Http\Request;
use Fogito\Http\Response;

class GroupInfoController
{
    public function showGroupInfo()
    {
        $data = (array) Request::get('data');
        $dialogId = (string) $data["dialog_id"];

        $dialogue = Dialogues::findFirst([
            "_id" => Dialogues::objectId($dialogId),
            "is_deleted" => 0
        ]);

        if (!$dialogue) {
            Response::error(Lang::get("DialogueNotFound", "Dialogue not found"));
        }

        $creationTime = $dialogue->created_at;
        $endTime = $dialogue->end_time;
        $userCount = count($dialogue->users);

        Response::success([
            'dialog_id' => $dialogId,
            'creation_time' => $creationTime,
            'end_time' => $endTime,
            'user_count' => $userCount,
        ], Lang::get("GroupInfoRetrieved", "Group information retrieved successfully"));
    }
}
