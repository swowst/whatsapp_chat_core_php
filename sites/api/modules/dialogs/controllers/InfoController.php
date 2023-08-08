<?php

namespace Controllers;

use Fogito\Lib\Lang;
use Fogito\Http\Request;
use Fogito\Http\Response;
use Fogito\Lib\Auth;
use Lib\Helpers;

use Custom\Models\Dialogues;
use Custom\Models\BoardsUsers;
use Custom\Models\Labels;
use Lib\Permission;
use Models\Users;

class InfoController
{
    public function index()
    {
        $data = (array)Request::get('data');
        $id = (string)$data["id"];
        $creator_id = Auth::getId();

        $messageData = Dialogues::findFirst([[
            "_id" => Dialogues::objectId($id),
            "is_deleted" => 0
        ]]);

        $is_read = $messageData->is_read;

        if ($messageData) {
            $creator_id = $messageData->$creator_id;
            if (array_key_exists($creator_id, $is_read)) {
                $is_read[$creator_id] = 1;
            }
        }

        $info = [
            "id" => (string)$messageData->_id,
            "title" => (string)$messageData->title,
            "created_at" => Dialogues::toSeconds($messageData->created_at),
            "user_ids" => (array)$messageData->users,
        ];


        Response::success($info);

    }
}
