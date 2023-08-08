<?php
namespace Lib;

use Fogito\Db\ModelManager;
use Fogito\Lib\Lang;

class TimeZones
{
    /*
     * datetime: timestamp / datetime / mongodate
     *
     * options: [
     *    tzfrom: id from timezone list, default: You can define constant DEFAULT_TIMEZONE in settings.php if not defined value: 100 - server time GMT 0
     *    tzto: id from timezone list, default: You can define constant USER_TIMEZONE in AclApi.php if not defined value: 101 - Denmark
     *    formatfrom: in settings.php You can define constant "DEFAULT_DATE_FORMAT", if not defined default: "Y-m-d H:i:s"
     *    formatto: in settings.php You can define constant "DEFAULT_DATE_FORMAT", if not defined default: "Y-m-d H:i:s"
     * ]
     *
     *
     * $dateTo = TimeZones::date(strtotime($date), false, ["tzfrom" => 102, "tzto" => 101]);
     */
    public static function date($datetime=0, $realFormatto=false, $options=[])
    {
        $tzfrom         = self::getById($options["tzfrom"]) ?  (int)$options["tzfrom"]: (DEFAULT_TIMEZONE > 0 ? DEFAULT_TIMEZONE: 100);
        $tzto           = self::getById($options["tzto"]) ?  (int)$options["tzto"]: (USER_TIMEZONE > 0 ? USER_TIMEZONE: 101);
        $formatfrom     = $options["formatfrom"] ?  $options["formatfrom"]: (defined("DEFAULT_DATE_FORMAT") ? DEFAULT_DATE_FORMAT: "Y-m-d H:i:s");
        $formatto       = $realFormatto ?  $realFormatto: (defined("DEFAULT_DATE_FORMAT") ? DEFAULT_DATE_FORMAT: "Y-m-d H:i:s");

        if (method_exists($datetime, "toDateTime")) {
            $datetime = @$datetime->toDateTime()->format("Y-m-d H:i:s");
        }elseif(is_numeric($datetime)){
            $datetime = date("Y-m-d H:i:s", $datetime);
        }

        $dt1 = new \DateTime($datetime, new \DateTimeZone(self::getById($tzfrom)["slug"]));
        $toTimezone = new \DateTimeZone(self::getById($tzto)["slug"]);
        $dt1->setTimezone($toTimezone);
        $datetime = $dt1->format('Y-m-d H:i:s');

        $timestamp = strtotime($datetime);

        if($realFormatto === "unix" || $realFormatto === "unixtime")
            return $timestamp;
        if($realFormatto === "mongo")
            return ModelManager::getDate($timestamp);
        return date($formatto, $timestamp);
    }

    /*
     * countryCode: $_SERVER["HTTP_CF_IPCOUNTRY"], Cloudflare returns country code: AZ
     */
    public static function detectTz($slug)
    {

        $timezone = self::getBySlug($slug);
        if(!$timezone)
            $timezone = self::getById(101);
        $currentDate = self::date(time(), false, ["tzfrom" => 100, "tzto" => $timezone["id"]]);
        return [
            "id"            => $timezone["id"],
            "title"         => $timezone["titles"]["en"],
            //"current_date"  => $currentDate,
            "current_time"  => strtotime($currentDate),
        ];
    }

    public static function getBySlug($slug)
    {
        foreach (self::getList() as $value)
        {
            if(mb_strtolower($value["slug"]) === mb_strtolower($slug))
                return $value;
        }
        return false;
    }

    public static function getByCountryCode($countryCode)
    {
        foreach (self::getList() as $value)
        {
            if($value["country_code"] === mb_strtolower($countryCode))
                return $value;
        }
        return false;
    }

    public static function getById($id)
    {
        return self::getList()[(int)$id];
    }

    public static function findOrDefault($id,$default = DEFAULT_TIMEZONE)
    {
        $timezoneId = self::getList()[(int)$id];
        if (!$timezoneId){
            $timezoneId = self::getList()[$default];
            if (!$timezoneId){
                $timezoneId = self::getList()[DEFAULT_TIMEZONE];
            }
        }

        return $timezoneId;
    }

    public static function getTitleByLang($data, $lang)
    {
        if($data->titles->{$lang}){
            return $data->titles->{$lang};
        } elseif($data->titles->{$data->default_lang}) {
            return $data->titles->{$data->default_lang};
        } else {
            foreach(Lang::$_languages as $lang){
                if($data->titles->{$lang}){
                    return $data->titles->{$lang};
                }
            }
        }
    }

    public static function getList()
    {
        return [
            100 => [
                "id"    => 100,
                "slug"    => "Iceland",
                "titles" => [
                    "dk" => "Iceland",
                    "en" => "Iceland",
                ]
            ],
            101 => [
                "id"    => 101,
                "slug"    => "Europe/Copenhagen",
                "titles" => [
                    "dk" => "København",
                    "en" => "Copenhagen, Denmark",
                ]
            ],
            102 => [
                "id"    => 102,
                "slug"    => "Asia/Baku",
                "titles" => [
                    "en" => "Baku (GMT+4)",
                ]
            ],
            103 => [
                "id"    => 103,
                "slug"    => "Europe/Istanbul",
                "titles" => [
                    "en" => "Istanbul",
                ]
            ],
            150 => [
                "id"    => 150,
                "slug"    => "America/New_York",
                "titles" => [
                    "en" => "New york, USA (EST)",
                ]
            ],
        ];
    }

}
