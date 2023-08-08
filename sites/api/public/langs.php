<?php
error_reporting(0);
$folder = "/home/fogito";

class Langs {
    public static $data = [];
}

function scanFolder($folder, $index)
{
    $index++;
    $ls = scandir($folder);
    $t = str_repeat("--", $index);
    foreach ($ls as $l)
    {
        if(!in_array($l, [".","..","vendor", "public"]))
        {
            if(strpos($l, ".php") !== false)
            {
                //echo "<br/>F - ".$t." ".$l;
                getLangs($folder."/".$l);
            }else if(!is_file($l))
            {
                //echo "<br/>D - ".$t." ".$l;
                scanFolder($folder."/".$l, $index);
            }
        }
    }
}

function getLangs($file)
{
    $c = file_get_contents($file);
    $ar = explode('Lang::get(', $c);
    foreach ($ar as $i => $v)
    {
        if($i>0)
        {
            $lang = explode(')', $v)[0];
            if(mb_strlen($lang)>0)
            {
                //if(mb_substr($lang, 0, 1) === "'")
                $lang = trim(str_replace("'", '"', $lang));
                $lang = explode('",', str_replace('" ,', '",', $lang));
                $key    = mb_substr(trim($lang[0]), 1);
                $value  = mb_substr(trim($lang[1]), 1);
                if(mb_substr($key, -1) === '"')
                    $key = mb_substr($key, 0, -1);
                if(mb_substr($value, -1) === '"')
                    $value = mb_substr($value, 0, -1);

                if(strlen($key)>0)
                    Langs::$data[$key] = $value;
                //echo "<br/>".$key." - ".$value;
            }
        }
    }
}

scanFolder($folder, -1);

if(@$_GET["dump"]>0)
{
    echo '<table border="1" cellspacing="0" cellpadding="5">';
    echo '<tr><td>Key</td><td>Value</td><td>Error</td></tr>';
    foreach (Langs::$data as $k => $v)
    {
        echo '<tr>';
        echo '<td color="#333">'.$k.'</td> <td color="#6495ed">'.$v.'</td> ';
        echo '<td>';
        if(strpos($k, " ") !== false){
            echo '<font color="red">wrong key format</font>';
        }else{
            $k = str_replace(["-","_"], "", $k);
            if($k !== preg_replace('/[^\da-z]/i', '', $k))
                echo '<font color="red">wrong key format</font>';
        }
        echo '</td>';
        echo '</tr>';
    }
    echo '</table>';
}else{
    echo json_encode(Langs::$data);
}
