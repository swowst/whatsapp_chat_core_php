<?php
namespace Lib;

use Fogito\Db\ModelManager;
use Fogito\Lib\Cache;
use Fogito\Lib\Lang;

class Helpers
{
    /**
     * translit
     *
     * @param  string $str
     * @return string
     */
    public static function translit($str)
    {
        $a = array('Ə', 'ə', 'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ');
        $b = array('E', 'e', 'A', 'A', 'A', 'A', 'A', 'A', 'AE', 'Ch', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'ch', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'Sh', 'sh', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o');
        return str_replace($a, $b, $str);
    }

    /**
     * textToSlug
     *
     * @param  string $string
     * @param  string $seperator
     * @return string
     */
    public static function textToSlug($string, $seperator = '-')
    {
        $string = strtolower(self::translit(trim($string)));
        $string = preg_replace("/(\s){1,}/", '$1', $string);
        $string = preg_replace('/[^A-Za-z0-9\\_\\-\\[ ]/', '', $string);
        $string = str_replace(' ', $seperator, $string);
        return $string;
    }

    /**
     * strToLat
     *
     * @param  string $string
     * @return string
     */
    public static function strToLat($string)
    {
        return str_replace([' ', '-'], '_', preg_replace('/[^A-Za-z0-9\\_\\-\\[ ]/', '', trim($string)));
    }

    /**
     * isUuid
     *
     * @param  string $str
     * @return string
     */
    public static function isUuid($str)
    {
        return preg_match('/\w{8}-\w{4}-\w{4}-\w{4}-\w{12}/', trim($str));
    }

    /**
     * genUuid
     *
     * @return string
     */
    public static function genUuid()
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            // 32 bits for "time_low"
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),

            // 16 bits for "time_mid"
            mt_rand(0, 0xffff),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand(0, 0x0fff) | 0x4000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand(0, 0x3fff) | 0x8000,

            // 48 bits for "node"
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    /**
     * filesize
     *
     * @param  integer $bytes
     * @return string
     */
    public static function filesize($bytes)
    {
        if ($bytes < 1024) {
            return $bytes . ' B';
        } elseif ($bytes < 1048576) {
            return round($bytes / 1024, 2) . ' KB';
        } elseif ($bytes < 1073741824) {
            return round($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes < 1099511627776) {
            return round($bytes / 1073741824, 2) . ' GB';
        } else {
            return round($bytes / 1099511627776, 2) . ' TB';
        }
    }

    /**
     * randomizer
     *
     * @param  array $lengths
     * @param  string $pool
     * @return string
     */
    public static function randomizer($lengths = [16], $pool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ123456890')
    {
        $size = $lengths[array_rand($lengths)];
        return substr(str_shuffle(str_repeat($pool, $size)), 0, $size);
    }

    /**
     * randomNumber
     *
     * @param  integer $length
     * @return integer
     */
    public static function randomNumber($length)
    {
        $min = pow(10, $length - 1);
        $max = pow(10, $length) - 1;
        return mt_rand($min, $max);
    }

    public static function validateDate($date, $format = 'Y-m-d H:i:s')
    {
        $resultDate = date($format, strtotime($date));

        return $resultDate == $date;
    }


    public static function validateDateModel($dateObj)
    {
        /**
         * date model obj
         * [
         *   date,       / Y-m-d  (required)
         *   time,        / H:i:s (required)
         *   timezone,    / INT  (optional)
         * ]
         */

        $error = false;
        if (!isset($dateObj['date']) || strlen((string)$dateObj['date']) == 0 || !self::validateDate($dateObj['date'], 'Y-m-d')) {
            $error = true;
        } else if (!isset($dateObj['time']) || strlen((string)$dateObj['time']) == 0) {
            $error = true;
        }

        if ($error)
            return false;

        $dateTime = $dateObj['date'] . ' ' . $dateObj['time'];
        $timezone = TimeZones::findOrDefault($dateObj['timezone'], USER_TIMEZONE);
        $timezoneId = $timezone['id'];
        $tzDefaultMongoDate = TimeZones::date(strtotime($dateTime), 'mongo', ['tzfrom' => $timezoneId, 'tzto' => DEFAULT_TIMEZONE]);

        return [
            'date' => date('Y-m-d', strtotime($dateTime)),
            'time' => date('H:i:s', strtotime($dateTime)),
            'timezone' => $timezoneId,
            'default_date' => $tzDefaultMongoDate
        ];
    }

    public static function getDateModel($dateObj)
    {
        /**
         * date model obj
         * [
         *   date,       / Y-m-d
         *   time,        / H:i:s
         *   timezone,    / INT
         *   default_date / MONGO DATE
         * ]
         */
        if (!$dateObj)
            return false;

        $dateTime = $dateObj->date . ' ' . $dateObj->time;
        $timezone = TimeZones::findOrDefault($dateObj->timezone);
        $timezoneId = $timezone['id'];
        $unixDate = TimeZones::date(strtotime($dateTime), 'unix', ['tzfrom' => $timezoneId, 'tzto' => USER_TIMEZONE]);

        return [
            'date' => date('Y-m-d', $unixDate),
            'time' => date('H:i:s', $unixDate),
            'timezone' => $dateObj->timezone,
            'default_unix_date' => ModelManager::toSeconds($dateObj->default_date) ?? false,
//            'timezone_string'   => TimeZones::getById($dateObj->timezone)['slug'],
        ];
    }

    public static function getCustomDateModel($dateTime, $timezone)
    {
        $unixDate = strtotime($dateTime);

        return [
            'date' => date('Y-m-d', $unixDate),
            'time' => date('H:i:s', $unixDate),
            'timezone' => $timezone,
            'default_unix_date' => TimeZones::date($unixDate, 'unix', ['tzfrom' => $timezone, 'tzto' => DEFAULT_TIMEZONE]),
//            'timezone_string'   => TimeZones::getById($dateObj->timezone)['slug'],
        ];
    }




    public static function arrayToObject($Array)
    {

        // Clreate new stdClass object
        $object = new \stdClass();

        // Use loop to convert array into object
        foreach ($Array as $key => $value) {
            if (is_array($value)) {
                $value = self::arrayToObject($value);
            }
            $object->$key = $value;
        }
        return $object;
    }

    public static function objectToArray($obj)
    {
        //only process if it's an object or array being passed to the function
        $ret = (array)$obj;
        if (is_object($obj) || is_array($obj)) {
            foreach ($ret as &$item) {
                //recursively process EACH element regardless of type
                $item = self::objectToArray($item);
            }
            return $ret;
        } //otherwise (i.e. for scalar values) return without modification
        else {
            return $obj;
        }
    }

    public static function fsize($bytes)
    {
        if ($bytes < 1024) {
            return $bytes . ' B';
        } elseif ($bytes < 1048576) {
            return round($bytes / 1024, 2) . ' KB';
        } elseif ($bytes < 1073741824) {
            return round($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes < 1099511627776) {
            return round($bytes / 1073741824, 2) . ' GB';
        } else {
            return round($bytes / 1099511627776, 2) . ' TB';
        }
    }

    public static function trimHardSpace($value)
    {
        return trim(str_replace("\xc2\xa0", '', $value));
    }

    public static function safeStrLen($value)
    {
        return strlen(trim(str_replace("\xc2\xa0", '', $value)));
    }

    public static function safeReq($value)
    {
        return htmlspecialchars(self::trimHardSpace($value));
    }

    public static function isImgFile($fileType)
    {
        return in_array($fileType, [
            "jpeg",
            "jpg",
            "png",
            "gif",
            "image/svg+xml",
            "image/jpeg",
            "image/jpg",
            "image/png",
            "image/gif"
        ]);
    }

    public static function getStringKeyArray(array $dataArray, string $key): array
    {

        $responseArray = [];
        if (count($dataArray) > 0) {
            foreach ($dataArray as $data) {
                if (is_object($data) && $data->{$key}) {
                    $responseArray[] = (string)$data->{$key};
                } elseif (is_array($data) && $data[$key]) {
                    $responseArray[] = (string)$data[$key];
                }
            }
        }

        return $responseArray;
    }


    public static function filterWeekDays($days): array
    {
        if (count($days) == 0 || !is_array($days))
            return [];

        $filteredDays = [];
        foreach ($days as $v) {
            if (is_numeric($v) && $v > 0 && $v < 8 && !in_array($v, $filteredDays)) {
                $filteredDays[] = $v;
            }
        }

        return (array)$filteredDays;
    }


    public static function manageLimitRequest($limit, $min = 50, $max = 200)
    {
        $limit = (int)$limit;

        if ($limit < 5) {
            $limit = $min;
        } else if ($limit > 200) {
            $limit = $max;
        }

        return $limit;
    }


    public static function manageSortRequest($sortField, $sortType, $fields = false, $defaultParams = [])
    {
        $defaultSortField = $defaultParams['sort_field'] ?: 'created_at';
        $defaultSortType = $defaultParams['sort_type'] ?: 'des';
        $sortType = $sortType ?: $defaultSortType;

        $fields = is_array($fields) ? $fields : ['_id', 'created_at', 'title'];

        $sort = [];
        $sort_field = (string)trim($sortField);
        $sort_field = in_array($sort_field, $fields) ? $sort_field : $defaultSortField;
        $sort[$sort_field] = $sortType == 'asc' ? 1 : -1;

        return $sort;
    }


    public static function inputJson($url, $vars_array)
    {
        $ch = \curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($vars_array));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10); //timeout in seconds
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        if (curl_errno($ch) > 0) {
            Response::error(curl_error($ch));
        }
        curl_close($ch);
        return $response;
    }


    public static function cronCacheStart($key, $cron_id, $duration = 55)
    {
        $lastCronStartKey = $key . "_last_start_time";
        $lastCronIdKey = $key . "_last_cron_id";

        $last_cron_start = Cache::get($lastCronStartKey);
        $last_cron_id = Cache::get($lastCronIdKey);

        $elapseFromStart = time() - $last_cron_start;

        echo "Elapse from start: " . $elapseFromStart . ", Start date: " . date("Y-m-d H:i:s", $last_cron_start) . "<br/>";

        if ($elapseFromStart < $duration) {
            exit("other cron is in use:: " . $last_cron_id);
        }

        Cache::remove($lastCronIdKey);
        Cache::set($lastCronIdKey, $cron_id, 86400);

        Cache::remove($lastCronStartKey);
        Cache::set($lastCronStartKey, time(), 86400);

        sleep(3);
    }

    public static function cronCacheKill($key, $cron_id)
    {
        $lastCronStartKey = $key . "_last_start_time";
        $lastCronIdKey = $key . "_last_cron_id";

        if (Cache::get($lastCronIdKey) != $cron_id) {
            exit("Cron stopped by other cron " . Cache::get($lastCronIdKey));
        }

        $last_cron_start = Cache::get($lastCronStartKey);

        self::breakWhenExpire($last_cron_start,100);
    }

    public static function breakWhenExpire($start,$duration = 120){
        $loopEllapse = time() - $start;

        if ($loopEllapse >= $duration){
            exit('loop breaked after '.$duration.'s');
        }
    }

    public static function safeMonthAdd($currentStart, $start, $interval, $option = 0)
    {
        $repeatStartDay = date("d", $start);

        $storedStartDate = $currentStart;
        $i = strtotime('+' . $interval . ' month', $currentStart);

        if ($option == 0) {
            if ($repeatStartDay !== date("d", $i)) {
                $loopCount = 0;
                while ($repeatStartDay !== date("d", $i)) {
                    $loopCount++;
                    $i = strtotime('+' . $loopCount * $interval . ' month', $storedStartDate);
                }
            }
        } else if ($option == 1) {
            $weekNumberOfStart = self::weekOfMonth($start);

            $nextMonth = strtotime('first day of next month', $currentStart);
            $nextMonthWeekNum = self::weekOfMonth($nextMonth);

            $day = date('w', $start);
            $days = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');

            while ($weekNumberOfStart !== $nextMonthWeekNum) {
                $checkNextMonth = strtotime('monday next week', $nextMonth);

                if (date('m', $checkNextMonth) == date('m', $nextMonth)) {
                    $nextMonth = $checkNextMonth;
                    $nextMonthWeekNum = self::weekOfMonth($nextMonth);
                } else {
                    break;
                }
            }

            $i = strtotime($days[$day], $nextMonth);

        } else if ($option == 2) {
            if ($repeatStartDay !== date("d", $i)) {
                $i = strtotime('last day of ' . $interval . ' month', $storedStartDate);
            }
        }
        return $i;
    }


    public static function weekOfMonth($date)
    {
        //Get the first day of the month.
        $firstOfMonth = strtotime(date("Y-m-01", $date));
        //Apply above formula.
        return self::weekOfYear($date) - self::weekOfYear($firstOfMonth) + 1;
    }

    public static function weekOfYear($date)
    {
        $weekOfYear = intval(date("W", $date));
        if (date('n', $date) == "1" && $weekOfYear > 51) {
            // It's the last week of the previos year.
            $weekOfYear = 0;
        }
        return $weekOfYear;
    }


    public static function initCurl($url, $vars_array, $method = 'GET')
    {
        $error = false;
        $error_code = 1004;
        $response = false;

        $method = strtoupper($method);
        $ch = \curl_init();
        $var_fields = "";
        foreach ($vars_array as $key => $value) {
            $var_fields .= $key . '=' . urlencode($value) . '&';
        }
        if ($method == "POST") {
            $post_vars = $var_fields;
        } else {
            $get_vars = (strlen($var_fields) > 0) ? "?" . $var_fields : "";
            $url .= $get_vars;
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        if ($method == "POST") {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_vars);
        }
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10); //timeout in seconds
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if (substr($url, 0, 5) == "https") {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        }
        $curlRes = curl_exec($ch);
        if (curl_errno($ch) > 0) {
            $error = curl_error($ch);
        }
        curl_close($ch);

        $result = json_decode($curlRes, true);

        if ($result && $result["status"] === "success") {
            $response = $result;
        } elseif ($result && $result["status"] === "error") {
            $error = $result['description'];
            $error_code = $result['error_code'];
        } else {
            $error = Lang::get('CurlError');
            $error_code = 1004;
        }

        if ($error) {
            $response = [
                'status' => 'error',
                'error_code' => $error_code,
                'description' => $error,
            ];
        }

        return $response;

    }

    public static function slugify($text, string $divider = '-')
    {
        // replace non letter or digits by divider
        $text = preg_replace('~[^\pL\d]+~u', $divider, $text);

        // transliterate
//        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, $divider);

        // remove duplicate divider
        $text = preg_replace('~-+~', $divider, $text);

        // lowercase
        $text = strtolower($text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }


    public static function validObjIds($ids)
    {
        $response = [];
        foreach ((array)$ids as $id) {
            if (ModelManager::objectId($id) && strlen($id) > 10) {
                $response[] = (string)$id;
            }
        }

        return $response;
    }

    public static function isOverdue($card)
    {

        $res = 0;

        if (!$card->is_completed && $card->end) {
            if ($card->end->default_date < ModelManager::getDate()) {
                $res = 1;
            }
        }

        return $res;
    }

    public static function findFileIconByMime($type)
    {

        $res = false;
        $mimeTypes = [
            // audio
            "audio/mp3" => "mp3",
            "audio/mpeg" => "mp3",
            "audio/ogg" => "mp3",

            // video
            "video/mp4" => "mp4",
            "video/mpeg" => "mp4",
            "video/ogg" => "mp4",

            // document

            "application/vnd.openxmlformats-officedocument.presentationml.presentation" => "ppt",
            "application/vnd.openxmlformats-officedocument.presentationml.slide" => "xlsx",
            "application/vnd.openxmlformats-officedocument.presentationml.slideshow" => "xlsx",
            "application/vnd.openxmlformats-officedocument.presentationml.template" => "xlsx",
            "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" => "xlsx",
            "application/vnd.openxmlformats-officedocument.spreadsheetml.template" => "xlsx",
            "application/vnd.openxmlformats-officedocument.wordprocessingml.document" => "docx",
            "application/vnd.openxmlformats-officedocument.wordprocessingml.template" => "docx",

            "application/vnd.ms-powerpoint.presentation.macroenabled.12" => "ppt",
            "application/vnd.ms-powerpoint.slideshow.macroenabled.12" => "ppt",
            "application/vnd.ms-powerpoint.template.macroenabled.12" => "ppt",
            "application/vnd.ms-powerpoint.addin.macroenabled.12" => "ppt",
            "application/vnd.openofficeorg.extension" => "ppt",
            "application/vnd.ms-powerpoint" => "ppt",


            "application/vnd.ms-excel.sheet.binary.macroenabled.12" => 'xls',
            "application/vnd.ms-excel.template.macroenabled.12" => 'xls',
            "application/vnd.ms-word.document.macroenabled.12" => "doc",
            "application/vnd.ms-excel.addin.macroenabled.12" => 'xls',
            "application/vnd.ms-excel.sheet.macroenabled.12" => 'xls',


            'application/octet-stream' => 'exe',
            "application/vnd.ms-works" => "doc",
            "application/vnd.ms-excel" => 'xls',
            'application/msword' => 'doc',
            'application/json' => 'json',
            'application/zip' => 'zip',
            "application/pdf" => 'pdf',
            'application/xml' => 'xml',
            'application/rtf' => 'rtf',

            //text
            'text/javascript' => 'js',
            'text/plain' => 'txt',
            'text/html' => 'html',
            'text/css' => 'css',
            'text/csv' => 'csv',
            'text/xml' => 'xml',

        ];

        foreach ($mimeTypes as $key => $val) {
            if ($key == $type) {
                $res = PROJECT_URL."/images/extensions/{$val}.svg";
                break;
            }
        }

        if ($res == false){
            $res = PROJECT_URL."/images/extensions/txt.svg";
        }

        return $res;
    }


}
