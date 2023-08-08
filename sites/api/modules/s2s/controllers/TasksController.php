<?php
namespace Controllers;

use Fogito\Lib\Lang;
use Fogito\Http\Request;
use Fogito\Http\Response;
use Fogito\Models\CoreUsers;
use Custom\Models\Dialogues;
use Custom\Models\Cards;
use Custom\Models\S2s;
use Lib\Permission;


class TasksController
{
    public function list()
    {
        $response = [];
        $req = (array)Request::get();
        $server_token = (string)$req["server_token"];
        $appId = $req["app_id"];

        if(strlen($server_token) == 0){
            Response::error("ServerTokenIsWrong", "Server token is wrong");
        }else if(strlen($appId) == 0){
            Response::error("AppIdWrong");
        }else if ($server_token != S2s::getServerToken($appId)) {
            Response::error("ServerTokenNotMatch");
        }

        $data = $req["data"];

        $cardsObjIds = [];
        foreach ((array)$data["task_ids"] as $card) {
            $card = (string)$card;
            if (strlen($card) > 5) {
                $cardsObjIds[] = Dialogues::objectId($card);
            }
        }

        $binds = [
            "_id" => ['$in' => $cardsObjIds ],
//            "is_deleted" => ['$ne' => 1]
        ];

        $cardsQuery = Cards::find([
            $binds,
            "sort" => ["_id" => 1]
        ]);

        $cardsTotalCount = Cards::count([$binds]);

        foreach ($cardsQuery as $card_item){
            $response[] = [
                "id" => (string)$card_item->_id,
                "column" => (string)$card_item->column,
                "title" => (string)$card_item->title,
                "position" => (int)$card_item->position,
            ];
        }
        Response::custom([
            'status' => 'success',
            'data' => $response,
            'count' => $cardsTotalCount,
        ]);
    }


    public function check()
    {
        $req = (array)Request::get();
        $server_token = (string)$req["server_token"];
        $appId = $req["app_id"];

        if (strlen($server_token) == 0) {
            Response::error("ServerTokenIsWrong", "Server token is wrong");
        } else if (strlen($appId) == 0) {
            Response::error("AppIdWrong");
        } else if ($server_token != S2s::getServerToken($appId)) {
            Response::error("ServerTokenNotMatch");
        }

        $data = $req['data'];
        $cardId = $data['id'];

        $cardData = Cards::findFirst([[
            "_id" => Cards::objectId($cardId),
//            "is_deleted" => 0
        ]]);

        if (!$cardData) {
            Response::error(Lang::get("NoData", "Object doesn't exist"));
        } else {
            $info = [
                "card" => (string)$cardData->_id,
                "column" => (string)$cardData->column,
                "board" => (string)$cardData->board,
            ];
            Response::success($info, Lang::get("UserIsAllowed", "User is allowed"));
        }
    }


    public function update()
    {
        $req = (array)Request::get();
        $server_token = (string)$req["server_token"];
        $appId = $req["app_id"];

        if (strlen($server_token) == 0) {
            Response::error("ServerTokenIsWrong", "Server token is wrong");
        } else if (strlen($appId) == 0) {
            Response::error("AppIdWrong");
        } else if ($server_token != S2s::getServerToken($appId)) {
            Response::error("ServerTokenNotMatch");
        }

        $data = $req['data'];
        $filters = (array)$data["filter"];
        $updateData = (array)$data["update"];


        if(count($filters) == 0){
            Response::error(Lang::get("FiltersNotSet"));
        }
        if(count($updateData) == 0){
            Response::error(Lang::get("UpdateNotSet"));
        }

        $validFilters = [];
        $allowedFilters = [
            '_id',
            'id',
            'is_deleted',
        ];

        foreach ($filters as $key => $val){
            if (in_array($key,$allowedFilters)){
                $validFilters[$key] = $val;

                if ($key == 'id'){
                    unset($validFilters['id']);
                    if(is_array($val)){
                        $validFilters['_id'] = [
                            $val[0] => array_map(function ($id) {
                                return Cards::objectId($id);
                            }, (array)$val[1])
                        ];
                    }else{
                        $validFilters['_id'] = Cards::objectId($val);
                    }
                }
            }
        }

        $allowedFields = [
            'duration_total',
        ];

        $validUpdateData = [];
        foreach ($updateData as $key=>$value){
            if(in_array($key,$allowedFields)){
                $validUpdateData[$key] = $value;
            }
        }

        if(count($validUpdateData) == 0){
            Response::error(Lang::get("ValidUpdateError"));
        }
        if(count($validFilters) == 0){
            Response::error(Lang::get("ValidFilterError"));
        }

        $validUpdateData['updated_at'] = Cards::getDate();

        $result = Cards::update($validFilters,$validUpdateData);

        if ($result){
            Response::success([], Lang::get("CardUpdated"));
        }else{
            Response::error(Lang::get("NotUpdated"));
        }
    }


    public function Editinvoice()
    {
        $req = (array)Request::get(false,false);

        $hash = (string)$req["hash"];

        if ($hash != INVOICE_TOKEN) {
            Response::error("TokenNotCorrect");
        }

        $data = $req['data'];
        $invoiceId = (string)$data["id"];
        $status = (string)$data["status"];
        $redirectLink = $data["redirect_link"];
        $invNumber = $data["invoice_number"];
        $businessType = $req["business_type"];
        $companyId = $req["company_id"];


        if(strlen($invoiceId) < 10){
            Response::error(Lang::get("InvoiceIdNotCorrect"));
        }

        $result = Cards::update([
            'invoice.id' => $invoiceId,
            'business_type' => $businessType,
            'company_id' => $companyId,
        ],[
            'invoice.redirect_link' => $redirectLink,
            'invoice.number' => $invNumber,
            'invoice.status' => $status,
            'updated_at' => Cards::getDate(),
        ]);

        if ($result){
            Response::success([], Lang::get("CardUpdated"));
        }else{
            Response::error(Lang::get("NotUpdated"));
        }
    }


    public function hasaccess()
    {
        $response = [];
        $req = (array)Request::get();
        $server_token = (string)$req["server_token"];
        $appId = $req["app_id"];

        if (strlen($server_token) == 0) {
            Response::error("ServerTokenIsWrong", "Server token is wrong");
        } else if (strlen($appId) == 0) {
            Response::error("AppIdWrong");
        } else if ($server_token != S2s::getServerToken($appId)) {
            Response::error("ServerTokenNotMatch");
        }

        $data = $req["data"];
        $cardId = $data['id'];
        $userId = $data['user_id'];
        $myPermissions = $data['permission'];

        $userData = CoreUsers::findFirst([["id" => (string)$userId]]);

        if (!$userData) {
            Response::error(Lang::get("UserNotFound"));
        }

        $cardData = Cards::findFirst([[
            "_id" => Cards::objectId($cardId),
            "is_deleted" => ['$ne' => 1],
        ]]);

        if (!$cardData) {
            Response::error(Lang::get("NoData", "Object doesn't exist"));
        }

        $boardPermissions = Permission::manualCheck('board_view', $myPermissions);

        if (!$boardPermissions) {
            Response::error(Lang::get("PermissionNotAllowed"));
        }

        $isModerator = in_array('all', $boardPermissions["selected"]);
        $isCardUser = in_array($userId, $cardData->users);

        if (!$isModerator && !$isCardUser) {
            if (strlen($cardData->board) > 10) {
                $boardData = Dialogues::findFirst([[
                    "_id" => Dialogues::objectId($cardData->board),
                    "is_deleted" => ['$ne' => 1],
                ]]);

                if (!$boardData) {
                    Response::error(Lang::get("PermissionNotAllowed"));
                }

                $isBoardUser = in_array($userId, $boardData->users);

                if (!$isBoardUser) {
                    Response::error(Lang::get("PermissionNotAllowed"));
                }
            }else{
                Response::error(Lang::get("PermissionNotAllowed"));
            }
        }

        Response::success(true, Lang::get("UserIsAllowed", "User is allowed"));
    }


}
