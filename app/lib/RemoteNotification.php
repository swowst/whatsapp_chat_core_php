<?php

namespace Lib;

use Fogito\Lib\Lang;

class RemoteNotification
{
    public static function sendMail($email, $params)
    {
        $content = $params['content'];
        $data = $params['data'];

        ob_start();
        include $params['template'] ? : 'mails/notifications/notification.php';
        $html = ob_get_contents();
        ob_clean();

        $vars = [
            "to" => $email,
            "key" => EMAIL_KEY,
            "subject" => $params['subject'],
            "content" => $params['content'],
            "from_title" => $params['from_title'] ?? DEFAULT_FROM_NAME,
            "from" => $params['from_email'] ?? DEFAULT_EMAIL,
            "reply_to" => $params['reply_to'] ?? false,
            "html" => $html,
        ];

        $curlRes = self::curl(EMAIL_DOMAIN, $vars);

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


    private static function curl($url, $params)
    {
        $var_fields = "";
        foreach ($params as $key => $value) {
            $var_fields .= $key . '=' . urlencode($value) . '&';
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $var_fields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);

        if (substr($url, 0, 5) == "https") {
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        }else{
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }

        $result = curl_exec($ch);

        if (curl_errno($ch))
            $result = json_encode([
                "status" => "error",
                "description" => "Connection Error",
                "error_code" => 1003
            ]);
        curl_close($ch);

        return $result;
    }


}
