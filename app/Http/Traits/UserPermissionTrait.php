<?php
namespace App\Http\Traits;

use Illuminate\Support\Facades\DB;

trait UserPermissionTrait{

    public function getPermission(string $email = '', $groupId = 0)
    {
        $hasPermission = DB::table('users')
                           ->join('user_groups', 'user_groups.user_id', 'users.id')
                           ->select('users.id')
                           ->where('users.active', true)
                           ->where('users.email', $email)
                           ->where('user_groups.group_id', $groupId)
                           ->where('user_groups.active', true)
                           ->first();

        return $hasPermission <> null;
    }

    public function getAllPermission(string $email = '')
    {
        return DB::table('user_groups')
                 ->join('users', 'user_groups.user_id', 'users.id')
                 ->select('user_groups.group_id')
                 ->where('users.email', $email)
                 ->where('users.active', true)
                 ->where('user_groups.active', true)
                 ->get();
    }

    public function getUserId(string $email = '')
    {
        return (DB::table('users')
                  ->select('id')
                  ->where('email', $email)
                  ->first())->id;

    }
}
