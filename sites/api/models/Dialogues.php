<?php

namespace Custom\Models;

use Fogito\Lib\Auth;
use Fogito\Lib\Lang;
use Lib\Permission;
use Models\ModelTrait;

class Dialogues extends \Fogito\Db\ModelManager
{
    use ModelTrait;

    public static function getSource()
    {
        return "dialogues";
    }
}
