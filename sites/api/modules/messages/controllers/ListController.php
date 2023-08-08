<?php

namespace Controllers;

use Custom\Models\Dialogues;
use Custom\Models\Messages;
use Fogito\Http\Request;
use Fogito\Http\Response;
use Fogito\Lib\Auth;
use Custom\Models\Cards;
use Custom\Models\Columns;
use Lib\Helpers;

class ListController
{
    public function index()
    {
        $data = (array) Request::get('data');
        $user_id = Auth::getid();
        $limit = Helpers::manageLimitRequest($data['limit']);
        $dialogId = (string)$data["dialogue_id"];
        $skip = (int) $data["skip"];
        $message_id = $data['message_id'];
        $hasPermission = true; // if I am admin

        $dialogue = Dialogues::findFirst([[
            "_id" => Dialogues::objectId($dialogId),
            ""
        ]]);

        $dialogue_users = $dialogue->users;

        $myJoinTime = false;
        foreach ($dialogue_users as $dialogue_user) {
            /*
             * $users = [{"id", "joined"}]
             */

            if ($dialogue_user["id"] === $user_id)
                $myJoinTime = $dialogue_user["joined"];
        }

        $messages = Messages::find([
            [
                "dialogue_id" => $dialogId,
                "created_at" => ['$gt' => $myJoinTime],
                "deleted_by" => ['$nin' => [$user_id]],
            ],
            "skip" => $skip,
            "limit" => $limit,
        ]);

        $filteredMessages = [];

        foreach ($messages as $message) {
            if (in_array($user_id, $message['deleted_by'] ?? [])) {
                continue;
            }

            $readBy = $message['read_by'] ?? [];

            if (!in_array($user_id, $readBy)) {
                $readBy[] = $user_id;
            }

            $update = [
                "read_by" => $readBy,
            ];

            Messages::update(
                ["_id" => Messages::objectId($message['_id'])],
                $update
            );

            $filteredMessages[] = $message;
        }

        Response::success($filteredMessages);
    }

}


