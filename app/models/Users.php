<?php
namespace Models;

use Fogito\Http\Response;
use Fogito\Models\CoreUsers;
use Lib\Helpers;

class Users extends CoreUsers
{
    public static function findWithProfessions($filter, $debug = false)
    {
        if (count(@$filter['columns']) > 0) {
            $filter['columns'][] = 'positions';
        }
        $users = self::find($filter, $debug);
        $newUserArray = [];

        if (count($users) > 0) {
            $positions = WorkPositions::getPositions();

            foreach ($positions as $position) {
                $positionsByKey[$position['id']] = $position['title'];
            }

            foreach ($users as $user) {
                $positionsNameArray = [];
                if (@$user['positions']) {
                    foreach (@$user['positions'] as $position) {
                        if ($positionsByKey[$position]) {
                            $positionsNameArray[] = $positionsByKey[$position];
                        }
                    }
                }
                unset($user['positions']);
//                $user['professions'] = $positionsNameArray;
                $user['position'] = join(", ", $positionsNameArray);
                $newUserArray[] = $user;
            }
        }

        return $newUserArray;
    }


    public static function findAndSet($list, $params=[])
    {
        $keyFrom    = $params["key_from"] ? $params["key_from"]: "user";
        $keyTo      = $params["key_to"] ? $params["key_to"]: "user";
        $columns    = $params["columns"] && count($params["columns"]) > 0 ? $params["columns"]: ["id", "fullname", "avatar_tiny"];

        $ids = [];
        foreach ($list as $value)
            $ids[] = (string)$value[$keyFrom];


        // ########## start Fetch ############
        $dataById = [];
        if(count($ids) > 0)
        {

            $query = self::findWithProfessions([
                "filter" => [
                    "id"    => [
                        'in'   => $ids,
                    ]
                ],
                "columns" => $columns,
            ]);
            foreach ($query as $value)
                $dataById[(string)$value["id"]] = $value;
        }
        // ########## end Fetch ############


        $data = [];
        foreach ($list as $value)
        {
            $value[$keyTo] = $dataById[$value[$keyFrom]] ? $dataById[$value[$keyFrom]] : false;
            $data[] = $value;
        }

        return $data;
    }


    public static function getRecommendationList($params)
    {
        $query = $params;
        $query['token'] = TOKEN;
        $query = self::filterSendParams($query);
        $url = CRM_URL . "/users/recommendations/list";

        $response = Helpers::inputJson($url, $query );

        $result = json_decode($response, true);

        if ($result && $result["status"] === "success") {
            return $result;
        } elseif ($result && $result["status"] === "error") {
            Response::error("status error", 1004);
        } else {
            Response::error("Connection error.", 1004);
        }

        exit('end');
    }



}
