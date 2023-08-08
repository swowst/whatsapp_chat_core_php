<?php

namespace Controllers;

use Custom\Models\Notes;
use Fogito\Http\Request;
use Fogito\Http\Response;
use Fogito\Models\CoreUsers;
use Models\Users;


class SearchController
{
    public function index()
    {
        $response = [];
        $data = (array)Request::get('data');
        $selected = $data['selected'];
        $cardId = $data['id'];
        $query = $data['query'];
        $user_type = $data['user_type']; // delete later
        $skip = (int)$data["skip"];
        $limit = $data['limit'] ? (int)$data['limit'] : 50;
        $limit = $limit < 5 ? 5 : ($limit > 100 ? 100 : (int)$limit);
        $type = $data['type'] ? $data['type'] : $user_type; // delete later

        $cardData = false;
        $selectedUserIds = [];
        if (strlen($cardId) > 5) {
            $cardData = Notes::findFirst([[
                "_id" => Notes::objectId($cardId),
                'is_deleted' => ['$ne' => 1]
            ]]);
            $selectedUserIds = $cardData->users;
        }

        if ($type == 'employee') {
            $type = ['in' => ['employee', 'moderator']];
        }

        $bindUsersRemote = [
            'type' => $type,
            'is_deleted' => 0,
        ];

        if (strlen($query) > 0) {
            $bindUsersRemote['fullname'] = ['$regex' => $query];
        }

        if ($selected) {
            unset($bindUsersRemote['is_deleted']);
            $bindUsersRemote['id'] = ['$in' => $selectedUserIds];
        }

        $usersResponse = CoreUsers::find([
            $bindUsersRemote,
            "columns" => ["id", "avatar_tiny", "avatars", "avatar_custom", "fullname", "type", "is_deleted", "positions", "position", "company"],
            "skip" => $skip,
            "limit" => $limit,
        ]);

        $usersCount = CoreUsers::count([$bindUsersRemote]);

        foreach ($usersResponse as $user) {
            if ($cardData) {
                $user['selected'] = in_array($user['id'], $cardData->users);
                $user['owner'] = in_array($user['id'], $cardData->owners) ? 1 : 0;
            }

            if ($selected == true && !$user['selected']) {
                continue;
            }
            $response[] = $user;
        }

        $response = CoreUsers::mergePositions($response);


        $res = [
            'status' => 'success',
            'data' => $response,
            'count' => $usersCount,
            'skip' => $skip,
            'limit' => $limit,
        ];

        $userId = $data['selected_id'];
        if (strlen($userId) > 10) {
            $userData = CoreUsers::findFirst([["id" => $userId], 'columns' => ['id', 'fullname']]);
            if ($userData) {
                $res['selected'] = [
                    'value' => $userData['id'],
                    'label' => $userData['fullname'],
                ];
            }
        }



        Response::custom($res);
    }
}
