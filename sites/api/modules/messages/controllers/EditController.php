<?php

namespace Controllers;

use Custom\Models\Messages;
use Fogito\Lib\Lang;
use Fogito\Http\Request;
use Fogito\Http\Response;
use Custom\Models\Cards;

class EditController
{
    public function index()
    {
        $data = (array)Request::get('data');
        $dialog_id = $data['dialog_id'];

        $messageData = Messages::findFirst([[
            "_id" => Messages::objectId($dialog_id),
            "is_deleted" => ['$ne' => 1]
        ]]);

        $hasPermission = Messages::checkPermission($messageData, 'message_modify');

        if (!$hasPermission) {
            Response::error(Lang::get("PermissionNotAllowed"));
        } elseif (!$messageData) {
            Response::error(Lang::get("MessageRequired", "Message doesn't exist"));
        }

        $updateResult = Messages::update(["_id" => Messages::objectId($dialog_id)], [
            'updated_at' => Messages::getDate(),
            "text" => $data["text"]
        ]);

        if (!$updateResult)
            Response::error(Lang::get("TechnicalError"), 1202);

        Response::success([], Lang::get("UpdatedSuccessfully"));
    }
}
