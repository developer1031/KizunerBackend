<?php

namespace Modules\Wallet\Package;

use Illuminate\Support\Facades\DB;

class Package
{
    public static function getPackage(string $packageId)
    {
        return DB::table('package_packages')
                ->where('id', $packageId)
                ->first();
    }
}
