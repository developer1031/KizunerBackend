<?php

namespace Modules\Framework\Service;

use Illuminate\Support\Facades\App;

class EnvironmentManager
{
    public function isProduction()
    {
        return App::environment(['staging', 'production']);
    }

    public function isDev()
    {
        return App::environment(['local', 'dev']);
    }
}
