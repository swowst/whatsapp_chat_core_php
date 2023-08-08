<?php

namespace Custom\Models;

use Models\ModelTrait;

class Messages extends \Fogito\Db\ModelManager
{
    use ModelTrait;

    public static function getSource()
    {
        return "boards";
    }
}
