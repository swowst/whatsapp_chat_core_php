<?php

namespace Controllers;

use Custom\Models\Messages;
use Fogito\Http\Request;
use Fogito\Http\Response;
use Fogito\Lib\Auth;
use Custom\Models\Labels;


class InfoController
{
    public function index()
    {
        $data = (array)Request::get('data');
        $dialog_id = $data['dialog_id'];
        $id = (string)$data["id"];

        $messageData = Messages::findFirst([[
            "_id" => Messages::objectId($id),
            "is_deleted" => 0
        ]]);


        $info = [
            "id" => (string)$messageData->_id,
            "text" => (string)$messageData->text,
            "created_at" => Messages::toSeconds($messageData->created_at),
        ];

        Messages::update(["_id" => Messages::objectId($dialog_id)]);
        Response::success($info);

    }
}
