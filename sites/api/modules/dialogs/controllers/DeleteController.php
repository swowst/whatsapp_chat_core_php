<?php

namespace Controllers;

use Custom\Models\Dialogues;
use Fogito\Lib\Lang;
use Fogito\Http\Request;
use Fogito\Http\Response;
use Fogito\Lib\Auth;
use Custom\Models\Cards;
use Custom\Models\Columns;

class DeleteController
{

    public function index()
    {
        $data = (array)Request::get('data');
        $dialogdId = (string)$data["id"];
        $title = htmlspecialchars_decode($data["title"]);
        $users = $data["users"];
        $is_read = [];

        foreach ($users as $user){
            $is_read[$user] = 0;
        }

        $dialogData = Dialogues::findFirst([[
            "_id" => Dialogues::objectId($dialogdId),
            "is_deleted" => 0
        ]]);

        $hasPermission = Dialogues::checkPermission($dialogData,'dialog_delete');

        if (!$hasPermission) {
            Response::error(Lang::get("PermissionNotAllowed"));
        } elseif (!$dialogData ) {
            Response::error(Lang::get("NoInformation", "No information found"));
        } else {
            $update = [
                "is_deleted" => 1,
                "deleted_at" => Dialogues::getDate(),
                "deleter_id" => Auth::getid(),
                "title" => $title
            ];

            Dialogues::update(["_id" => Dialogues::objectId($dialogdId)], $update);

            Response::success([], Lang::get("DeletedSuccessfully", "Deleted successfully"));
        }
    }
}
