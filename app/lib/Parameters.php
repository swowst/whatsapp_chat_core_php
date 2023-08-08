<?php

namespace Lib;

use Fogito\Lib\Lang;

class Parameters
{

    public static function getManuelParameters($key,$keyName = false)
    {
        $data = call_user_func("self::" . "" . $key);
        $result = [];
        foreach ($data as $value) {
            $r = [];
            foreach ($value as $k => $v) {
                $r[$k] = $v;
            }

            if ($keyName) {
                $result[(string)$r[$keyName]] = $r;
            } else {
                $result[] = $r;
            }
        }
        return $result;
    }

    public static function default_colors()
    {
        $statuses = [
            ["id" => 1, "dex" => "#0079bf", "title" => Lang::get('blue')],
            ["id" => 2, "dex" => "#d29034", "title" => Lang::get('orange')],
            ["id" => 3, "dex" => "#519839", "title" => Lang::get('green')],
            ["id" => 4, "dex" => "#b04632", "title" => Lang::get('red')],
            ["id" => 5, "dex" => "#89609e", "title" => Lang::get('purple')],
            ["id" => 6, "dex" => "#cd5a91", "title" => Lang::get('pink')],
            ["id" => 7, "dex" => "#4bbf6b", "title" => Lang::get('lime')],
            ["id" => 8, "dex" => "#00aecc", "title" => Lang::get('sky')],
            ["id" => 9, "dex" => "#838c91", "title" => Lang::get('grey')],
        ];
        return $statuses;
    }

    public static function invoice_status()
    {
        return [
            ["id" => 1, "dex" => "#e0b11f", "value" => 'notsent', "title" => Lang::get('NotSent'),],
            ["id" => 2, "dex" => "#37b710", "value" => "pending", "title" => Lang::get('PendingPayment'),],
            ["id" => 3, "dex" => "#e33851", "value" => "error", "title" => Lang::get('Error'),],
            ["id" => 4, "dex" => "#b04632", "value" => "accepted", "title" => Lang::get('Accepted'),],
            ["id" => 5, "dex" => "#89609e", "value" => "paid", "title" => Lang::get('Paid'),],
            ["id" => 6, "dex" => "#cd5a91", "value" => "rejected", "title" => Lang::get('Rejected'),],
            ["id" => 7, "dex" => "#4bbf6b", "value" => "expired", "title" => Lang::get('Expired'),],
            ["id" => 8, "dex" => "#00aecc", "value" => "canceled", "title" => Lang::get('Cancelled'),],
            ["id" => 9, "dex" => "#838c91", "value" => "created", "title" => Lang::get('Created'),],
        ];
    }


    public static function board_status()
    {
        $statuses = [
            ["id" => 1, "dex" => "#4b7bec", "title" => ["da" => "open", "en" => "open", "az" => "open"]],
            ["id" => 2, "dex" => "#fd9644", "title" => ["da" => "in progress", "en" => "in progress", "az" => "in progress"]],
            ["id" => 3, "dex" => "#26de81", "title" => ["da" => "completed", "en" => "completed", "az" => "completed"]]
        ];
        return $statuses;
    }

}
