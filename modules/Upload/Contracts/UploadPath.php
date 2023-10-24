<?php

namespace Modules\Upload\Contracts;

class UploadPath
{
    public static function resolve()
    {
        $type = app('request')->get('type');
        $config = \Config::get('upload.' . $type);
        $userId = md5(app('request')->user()->id);
        $config = str_replace('{userId}', $userId, $config);
        return $config;
    }
}
