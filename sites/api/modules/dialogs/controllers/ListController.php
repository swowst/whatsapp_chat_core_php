<?php

namespace Controllers;

use Fogito\Lib\Lang;
use Fogito\Http\Request;
use Fogito\Http\Response;
use Fogito\Lib\Auth;
use Lib\Helpers;
use Custom\Models\Dialogues;
use Custom\Models\BoardsUsers;
use Custom\Models\Cards;
use Custom\Models\Labels;
use Lib\Permission;
use Models\Users;

class ListController
{
    public function index()
    {
        $data = (array)Request::get('data');
        $title = $data["title"];
        $users = $data["users"];
        $id = (string)$data["id"];


        $response = [
            "id" => $id,
            "title" => (string)$title,
            "created_at" => Dialogues::toSeconds($data->created_at),
            "users" => $users
        ];


        Response::custom([
            'status' => 'success',
            'data' => $response,
        ]);
    }
}
