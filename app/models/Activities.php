<?php

namespace Models;

use Custom\Models\Categories;
use Custom\Models\Notes;
use Custom\Models\NoteUsers;
use Custom\Models\Sessions;
use Fogito\Http\Response;
use Fogito\Lib\Auth;
use Fogito\Lib\Lang;
use Fogito\Models\CoreEmails;
use Fogito\Models\CoreUsers;


class Activities extends \Fogito\Db\ModelManager
{
    public static function getSource()
    {
        return "activities";
    }

    /*
    status defination
    0-deleted
    1-created.
    2-notified
    */
    public $creator_id;
    public $section;
    public $section_id;
    public $operation;
    public $ids;
    public $values;
    public $changes;
    public $status;
    public $created_at;
    public $updated_at;


    public static function getOperations()
    {
        return [
            //notes
            "notes_add" => ["key" => "NotesAdd", "priority" => 6, "title" => Lang::get("Document add", "{who} created {notes}")],
            "notes_update" => ["key" => "NotesUpdate", "priority" => 7, "title" => Lang::get("Document update", "{who} edited {notes}")],
            "notes_delete" => ["key" => "NotesDelete", "priority" => 9, "title" => Lang::get("Document delete", "{who} deleted {title}")],

            //users
            "notes_users_add" => ["key" => "NotesUsersAdd", "priority" => 4, "title" => Lang::get("User add", "{who} added {user} in {notes}")],
            "notes_users_update" => ["key" => "NotesUsersUpdate", "priority" => 10, "title" => Lang::get("User update", "{who} changed {user} permission in {notes}")],
            "notes_users_delete" => ["key" => "NotesUsersDelete", "priority" => 8, "title" => Lang::get("User delete", "{who} deleted {user} in {notes}")],

            "set_owner" => ["key" => "NotesSetOwner", "priority" => 10, "title" => Lang::get("Set owner", "{who} set {user} as owner in {notes} note")],
            "unset_owner" => ["key" => "NotesUnsetOwner", "priority" => 10, "title" => Lang::get("Unset owner", "{who} deleted {user} in {notes} note")],
        ];
    }

    public static function log($params)
    {
        $changes = [];
        $insert = false;
        if (substr($params["operation"], -6) == "updates") {
            foreach ($params["newObject"] as $key => $value) {
                if (!in_array($key, ["updated_at"]) && $value != $params["oldObject"]->{$key}) {
                    $changes[$key] = [
                        'from' => $params["oldObject"]->{$key},
                        'to' => $value,
                    ];
                }
            }
            if (count($changes) > 0) {
                $insert = true;
            }
        } else {
            $insert = true;
        }


        if ($insert) {
            $insertId = self::insert([
                "creator_id" => (string)Auth::getData()->id,
                "section" => (string)$params["section"],
                "section_id" => (string)$params["section_id"],
                "operation" => (string)$params["operation"],
                "ids" => (array)$params["ids"],
                "values" => (array)$params["values"],
//                "changes" => (array)$changes,
                "notified" => 0,
                "status" => 1,
                "is_sent" => 0,
                "created_at" => self::getDate(),
                "updated_at" => self::getDate(),
            ]);
            if ($insertId) {
////
////                $userId = $params['values']['user_id'];
////
////                if ($userId){
////                    $data = CoreEmails::insert([
////                        "user_id"             => $userId,
////                        "subject"             => "Drive updates",
////                        "from"                => DEFAULT_EMAIL, // OPTIONAL, default: noprely@fogito.com
////                        "from_title"          => DEFAULT_FROM_NAME, // OPTIONAL
////                        "body"                => "Hello world!", // TEXT or HTML
////                        "expire"              => 120, // seconds, 0 is no expiration
////                    ]);
////
////                }
////
//
                return $insertId;
            }
        }
        return false;
    }

    public static function collectLogDatas($logsData)
    {
        $userIds = [];
        $noteIds = [];
        $sessionIds = [];
        foreach ($logsData as $log) {
            if ($log->creator_id) {
                $userIds[] = $log->creator_id;
            }
            if ($log->ids->user) {
                $userIds[] = $log->ids->user;
            }
            if ($log->ids->notes) {
                $noteIds[] = Notes::objectId($log->ids->notes);
            }
            if ($log->ids->session) {
                $sessionIds[] = self::objectId($log->ids->session);
            }
        }

        $userIds = array_values(array_unique($userIds));
        $noteIds = array_values(array_unique($noteIds));
        $sessionIds = array_values(array_unique($sessionIds));

        $usersByKey = [];
        $notesByKey = [];
        $sessionsByKey = [];

        if (count($noteIds) > 0) {
            $notesList = Notes::find([[
                '_id' => ['$in' => $noteIds],
                'is_deleted' => ['$ne' => 1]
            ]]);

            foreach ($notesList as $list){
                $notesByKey[(string)$list->_id] = $list;
            }
        }

        if (count($userIds) > 0) {
            $usersRemoteData = CoreUsers::find([
                "filter" => ["id" => ['in' => $userIds]],
                "columns" => ["id", "fullname", "avatar_tiny", "type"],
            ]);

            foreach ($usersRemoteData as $usersRemote) {
                $usersByKey[$usersRemote['id']] = $usersRemote;
            }
        }


        return [
            'user' => $usersByKey,
            'notes' => $notesByKey,
        ];
    }


//    public static function prepareDescription($activity, $calendarsByKey, $usersByKey)
//    {
//        $operationsList = Activities::getOperations();
//
//        $operationTitle = $operationsList[$activity->operation]['title'];
//
//        $who = $usersByKey[@$activity->user_id];
//        $whom = $usersByKey[@$activity->values->user_id];
//        $calendar = (array)$calendarsByKey[@$activity->values->calendar_id];
//
//        $replaceArray = [
//            '{WHO}' => "<a target='_blank' href='/profile/".@$who['id']."'>".(string)@$who['fullname']."</a>",
//            '{WHOM}' => "<a target='_blank' href='/profile/".@$whom['id']."'>".(string)@$whom['fullname']."</a>",
//            '{CALENDAR_TITLE}' => "<a target='_blank' href='/calendar/edit/".@@$calendar['_id']."'>".(string)@$calendar['title']."</a>",
//        ];
//
//        foreach ($replaceArray as $key => $value) {
//            $operationTitle = str_replace($key, $value, $operationTitle);
//        }
//
//        return $operationTitle;
//    }

}
