<?php

namespace Controllers;

use Custom\Models\Notes;
use Custom\Models\NoteUsers;
use Fogito\Lib\Auth;
use Fogito\Lib\Lang;
use Fogito\Http\Request;
use Fogito\Http\Response;
use Lib\Permission;
use Models\Activities;

class SetownerController
{
    public $cardData = [];
    public function index()
    {
        $data = (array)Request::get('data');
        $cardId = (string)$data["note_id"];
        $userId = (string)$data["user_id"];
        $value = (int)$data["value"] == 1 ? 1 : 0;

        $cardData = Notes::findById($cardId);

        $string = ltrim(str_replace('&nbsp;', '', $cardData->content));
        $titleEndPos = strpos($string, '<p>', 1) ?: 400;
        $title = strip_tags(substr($string, 0, $titleEndPos));


        $my_user_id = Auth::getId();

        $isModerator = Permission::allSelected('notes_view');
        if ($cardData->creator_id != $my_user_id && !$isModerator) {
            Response::error(Lang::get("PermissionNotAllowed"));
        }

        $editedCardUser = NoteUsers::findFirst([[
            'note_id' => $cardId,
            'user_id' => $userId,
            'is_deleted' => ['$ne' => 1]
        ]]);

        if (!$editedCardUser) {
            Response::error(Lang::get("UserNotFound"));
        }
        if ($editedCardUser->user_type == 'user') {
            Response::error(Lang::get("UserCantBeOwner"));
        }

        NoteUsers::update(['_id' => $editedCardUser->_id], [
            'owner' => $value,
            'updated_at' => NoteUsers::getDate(),
        ]);

        $ownerIds = (array)$cardData->owners;

        if ($value == 1) {
            if (!in_array($userId, $ownerIds)) {
                $ownerIds[] = $userId;
            }
        } else {
            $newOwnerIds = [];
            foreach ($ownerIds as $ownerId) {
                if ($ownerId != $userId)
                    $newOwnerIds[] = $ownerId;
            }
            $ownerIds = $newOwnerIds;
        }

        Notes::update(['_id' => $cardData->_id], [
            'owners' => $ownerIds
        ]);

        $operationName = $value == 1 ? "set_owner" : "unset_owner";

        Activities::log([
            'user_id' => Auth::getId(),
            "section" => "notes",
            "section_id" => $cardId,
            "operation" => $operationName,
            "ids" => [
                'user' => (string)$userId,
                'notes' => (string)$cardId,
            ],
            'values' => [
                'keyword' => $title
            ],
            'status' => 1,
        ]);

        Response::success([], Lang::get("UpdatedSuccessfully"));
    }
}
