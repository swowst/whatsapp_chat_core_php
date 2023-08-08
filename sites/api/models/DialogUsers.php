<?php

namespace Custom\Models;

use Models\ModelTrait;

class DialogUsers extends \Fogito\Db\ModelManager
{
    use ModelTrait;

    public static function getSource()
    {
        return "dialog_users";
    }
}
