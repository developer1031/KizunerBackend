<?php

namespace Modules\Config;

class Config
{
    public static function updateConfig(string $path, string $value)
    {
        $config = ConfigEntity::where('path', $path)->first();
        $config->value = $value;
        $config->save();
        return $config->value;
    }

    public function getConfig(string  $path)
    {
        $config = ConfigEntity::where('path', $path)->first();
        return $config ? $config->value : null;
    }

    public static function getConfigVal (string $path) {
        $config = ConfigEntity::where('path', $path)->first();
        return $config ? $config->value : null;
    }

    public static function getConfigValWithDefault (string $path, $default=0) {
        $config = ConfigEntity::where('path', $path)->first();
        return $config ? $config->value : $default;
    }
}
