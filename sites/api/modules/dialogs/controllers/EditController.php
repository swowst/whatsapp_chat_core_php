<?php

namespace Controllers;

use Custom\Models\Dialogues;
use Fogito\Lib\Lang;
use Fogito\Http\Request;
use Fogito\Http\Response;
use Custom\Models\Cards;

class EditController
{
    public function index()
    {
        $data = (array)Request::get('data');
        $title = htmlspecialchars_decode($data["title"]);
        $users = $data["users"];
        $dialogId = (string)$data["id"];
        $is_read = [];

        foreach ($users as $user){
            $is_read[$user] = 0;
        }

        $dialogData = Dialogues::findFirst([[
            "_id" => Dialogues::objectId($dialogId),
            "is_deleted" => ['$ne' => 1]
        ]]);

        $hasPermission = Dialogues::checkPermission($dialogData, 'dialog_modify');

        if (!$hasPermission) {
            Response::error(Lang::get("PermissionNotAllowed"));
        } elseif (!$dialogData) {
            Response::error(Lang::get("DialogRequired", "Dialog doesn't exist"));
        }



        $updateResult = Dialogues::update(["_id" => Dialogues::objectId($dialogId)], [
            'updated_at' => Dialogues::getDate(),
            "is_read" => $is_read,
            "title" => $title
        ]);

        if (!$updateResult)
            Response::error(Lang::get("TechnicalError"), 1202);

        Response::success([], Lang::get("UpdatedSuccessfully"));
    }
}
