<?php

namespace Controllers;

use Custom\Models\MessageDeleted;
use Custom\Models\Messages;
use Fogito\Lib\Lang;
use Fogito\Http\Request;
use Fogito\Http\Response;
use Fogito\Lib\Auth;
use Custom\Models\Cards;
use Custom\Models\Columns;
use http\Message;

class DeleteController
{
    public function index()
    {
        $data = (array)Request::get('data');
        $dialog_id = $data['dialog_id'];
        $type = $data['type'] =='all' ? "all" : 'self';
        $user_id = Auth::getId();
        $message_id = $data["message_id"];

        $messageData = Messages::findFirst([[
            "_id" => Messages::objectId($dialog_id),
            "is_deleted" => 0
        ]]);

        MessageDeleted::insert([
            "user_id" => $user_id,
            "message_id" => $message_id
        ]);

        $hasPermission = Messages::checkPermission($messageData,'message_delete');

        if (!$hasPermission) {
            Response::error(Lang::get("PermissionNotAllowed"));
        } elseif (!$messageData ) {
            Response::error(Lang::get("NoInformation", "No information found"));
        } else {
            $update = [
                "is_deleted" => 1,
                "deleted_at" => Messages::getDate(),
                "deleter_id" => Auth::getid()
            ];



           if ($type == "all"){
               $update = [
                   "is_deleted" => 1,
                   "deleted_at" => Messages::getDate(),
                   "deleter_id" => Auth::getid()
               ];
           }else{
               $deletedBy = $messageData['deleted_by'] ?? [];

               if (!in_array($user_id, $deletedBy)) {
                   $deletedBy[] = $user_id;
               }

               $update = [
                   "deleted_by" => $deletedBy,
                   "updated_at" => Messages::getDate(),
               ];
           }


            Messages::update(["_id" => Messages::objectId($dialog_id)], $update);

            Response::success([], Lang::get("DeletedSuccessfully", "Deleted successfully"));
        }
    }
}
