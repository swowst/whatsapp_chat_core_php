<?php

namespace Controllers;

use Custom\Models\Messages;
use Custom\Models\MessageStatus;
use Fogito\Lib\Lang;
use Fogito\Http\Request;
use Fogito\Http\Response;
use Fogito\Lib\Auth;

use Fogito\Models\CoreUsers;

class UpdatestatusController
{
    public function index()
    {
        $data = (array)Request::get('data');
        $text = $data["text"];
        $creator_id = Auth::getId();
        $message_id = $data['message_id'];
        $status = $data['status'];

        $messageStatus = MessageStatus::findFirst([
            'user_id' => $creator_id,
            'message_id' => $message_id
        ]);

        $data = [
            'status' => $status,
        ];

        if($status == 'received'){
            $data['reveived_at'] = MessageStatus::getDate();
        }else if($status == 'readed'){
            $data['readed_at'] = MessageStatus::getDate();
        }

        if($messageStatus){
            MessageStatus::update(
                ['_id' => $messageStatus->id],
                $data
            );
        }else{
            $data['message_id'] = $message_id;
            $data['user_id'] = $creator_id;
            $message_status_id = MessageStatus::insert($data);
        }


        Response::success([
            'id' => (string)$message_status_id ?? $messageStatus->id,
            'text' => (string)$text,
            "creator_id" => $creator_id,
        ], Lang::get("AddedSuccessfully"));

    }
}
