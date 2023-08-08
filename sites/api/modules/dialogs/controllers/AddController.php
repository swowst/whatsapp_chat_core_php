<?php

namespace Controllers;

use Custom\Models\Dialogues;
use Fogito\Lib\Lang;
use Fogito\Http\Request;
use Fogito\Http\Response;
use Fogito\Lib\Auth;

use Fogito\Models\CoreUsers;

class AddController
{
    public function index()
    {
        $data = (array) Request::get('data');
        $title = htmlspecialchars_decode($data["title"]);
        $users = $data["users"];
        $creator_id = Auth::getId();
        $is_read = [];

        foreach ($users as $user) {
            $is_read[$user] = 0;
        }

        if (strlen($title) < 1) {
            Response::error(Lang::get("TitleError", "Title is required"));
        } elseif (strlen($title) > 60) {
            Response::error(str_replace("{count}", 60, Lang::get("TitleMax", "Title should be less than {count} letters")));
        }
        $userCount = count($users);
        if ($userCount === 0) {
            Response::error(Lang::get("UserError", "Select Users"));
        }

        $cacheKey = 'group_users_' . md5(serialize($users));

        $cachedUsers = Cache::get($cacheKey);

        if ($cachedUsers === null) {
            $slicedUsers = array_slice($users, 0, 50);
            Cache::put($cacheKey, $slicedUsers);
        } else {
            $slicedUsers = $cachedUsers;
        }

        $dialog_id = Dialogues::insert([
            "title" => $title,
            "users" => $slicedUsers,
            "creator_id" => $creator_id,
            "is_deleted" => 0,
            'is_read' => $is_read,
            "is_archived" => 0,
            "updated_at" => Dialogues::getDate(),
            "created_at" => Dialogues::getDate(),
        ]);



        foreach ($users as $user) {
            DialogUsers::insert([
                'user_id' => $user->id,
                'dialog_id' => $dialog_id
            ]);
        }



        Response::success([
            'id' => (string) $dialog_id,
            'title' => $title,
            'is_read' => $is_read,
            "creator_id" => $creator_id,
            "users" => $slicedUsers,
        ], Lang::get("AddedSuccessfully"));
    }

}
