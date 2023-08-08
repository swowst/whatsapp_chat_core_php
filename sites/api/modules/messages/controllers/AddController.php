<?php

namespace Controllers;

use Custom\Models\Messages;
use Custom\Models\MessageStatus;
use Fogito\Lib\Lang;
use Fogito\Http\Request;
use Fogito\Http\Response;
use Fogito\Lib\Auth;

use Fogito\Models\CoreUsers;

class AddController
{
    public function index()
    {
        $data = (array)Request::get('data');
        $text = $data["text"];
        $creator_id = Auth::getId();
        $dialog_id = $data['dialog_id'];

        $messages_id = Messages::insert([
            "text" => $text,
            "creator_id" => $creator_id,
            "dialog_id" => $dialog_id,
            "is_deleted" => 0,
            "is_archived" => 0,
            "updated_at" => Messages::getDate(),
            "created_at" => Messages::getDate(),
        ]);

        $messages = MessageStatus::insert([
            "message_id" => $messages_id,
            "user_id" => $creator_id,
            "status" => "sended",
            "received_at" => null,
            "readed_at" => null,
            "sended_at" => MessageStatus::getDate()
        ]);

        Response::success([
            'id' => (string)$messages_id,
            'text' => (string)$text,
            "creator_id" => $creator_id,
        ], Lang::get("AddedSuccessfully"));

    }
}
