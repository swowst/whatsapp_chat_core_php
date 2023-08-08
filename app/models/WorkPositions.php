<?php
namespace Models;

use Fogito\Db\RemoteModelManager;
use Fogito\Lib\Cache;

class WorkPositions extends RemoteModelManager
{
    public static function getServer()
    {
        return CRM_URL;
    }

    public static function getSource()
    {
        return "workpositions";
    }


    public static function getPositions(){
        $cacheKey = 'workpositions_minlist_'.COMPANY_ID.'_'.BUSINESS_TYPE;
        $response = Cache::get($cacheKey);

        if (!$response){
            $response = self::minlist();
            Cache::set($cacheKey,$response,time()+3600);
        }

        return $response;
    }

    public static function minlist($filter = [], $debug=false)
    {
        self::init("minlist");
        return self::request($filter, $debug);
    }
}
