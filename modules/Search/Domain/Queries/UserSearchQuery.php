<?php

namespace Modules\Search\Domain\Queries;

use Illuminate\Support\Facades\DB;

class UserSearchQuery
{

    public static function search(string $query, $gender, $age)
    {
        $sql = DB::table('users')
                ->select('id')
                ->where('name', 'like', '%'.$query.'%')
                ->orWhere('email', 'like', '%'.$query.'%')
                ->whereNull('users.deleted')
                ->where('users.admin', false)
                ->groupBy('users.id');

        if ($gender) {
            $sql->where('gender', $gender);
        }

        if ($age) {
            $ageRange = explode('-', $age);
            $ageMin   = $ageRange[0];
            $ageMax   = $ageRange[1];
            $sql->whereBetween($age, [$ageMin, $ageMax]);
        }

        return $sql->get();
    }
}
