<?php
namespace Lib;

use Fogito\Lib\Auth;

class Permission
{

    public static function check($key){
        $permissions = Auth::getPermissions();

        $allow = false;
        if ($permissions[$key]['allow'])
        {
            if($permissions[$key]["selected"]){
                $allow = $permissions[$key];
            }else{
                $allow = true;
            }
        }
        return $allow;
    }

    public static function manualCheck($key,$permissions){
        $allow = false;
        if ($permissions[$key]['allow'])
        {
            if($permissions[$key]["selected"]){
                $allow = $permissions[$key];
            }else{
                $allow = true;
            }
        }
        return $allow;
    }


    public static function selected(string $permissionName,string $permissionType): bool
    {
        return in_array($permissionType, (array)self::check($permissionName)["selected"]);
    }

    public static function allSelected(string $permissionName): bool
    {
        return in_array('all', (array)self::check($permissionName)["selected"]);
    }
}
