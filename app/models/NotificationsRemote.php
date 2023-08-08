<?php
namespace Models;

use Fogito\Db\RemoteModelManager;

class NotificationsRemote extends RemoteModelManager
{
    public static function getUrl()
    {
        return API_S2S."/notifications";
    }

    public static function getServer()
    {
        return API_S2S;
    }

    public static function getSource()
    {
        return "notifications";
    }
}
