<?php

namespace Modules\Config\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Modules\Config\ConfigEntity;

class ConfigController
{
    public function index()
    {
        $configContent = ConfigEntity::whereIn('path', [
            'term',
            'policy',
            'faq',
            'nearby_radius',
            'map_radius',
            'about'
        ])->get();
        return response()->json([
            'data' => $configContent->toArray()
        ], Response::HTTP_OK);
    }

    public function getCountry() {
        $countries = DB::table('country')->groupBy('country')->get();
        return response()->json([
            'data' => $countries
        ], Response::HTTP_OK);
    }
}
