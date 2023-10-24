<?php

namespace Modules\Admin\Http\Controllers\Config;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Modules\Admin\Http\Requests\Package\StoreRequest as PackageStoreRequest;
use Modules\Admin\Http\Requests\Package\UpdateRequest as PackageUpdateRequest;
use Modules\Config\Config;
use Modules\Config\ConfigEntity;
use Modules\Framework\Support\Facades\EntityManager;
use Modules\Kizuner\Models\LeaderBoard;
use Modules\Package\Domains\Entities\PackageEntity;
use Modules\Package\Domains\Package;
use Modules\Package\Price\Price;
use Modules\Upload\Contracts\UploadPath;
use Yajra\DataTables\DataTables;

class ConfigController
{
    public function index()
    {
        $badge_config = ConfigEntity::where('path', 'offline_remain')->first();
        if (!$badge_config) {
            $badge_config = new ConfigEntity();
            $badge_config->path = 'offline_remain';
            $badge_config->value = json_encode([
                'day_num' => '',
                'content' => ''
            ]);
            $badge_config->save();
        }
        $config_data = new Config();
        $offline_remain_setting =  json_decode($config_data->getConfig('offline_remain'), true);
        $kizuner_share =  $config_data->getConfigValWithDefault('kizuner_share');
        $kizuner_first_add_post =  $config_data->getConfigValWithDefault('kizuner_first_add_post');
        $nearby_radius =  $config_data->getConfigValWithDefault('nearby_radius');
        $map_radius =  $config_data->getConfigValWithDefault('map_radius');
        $now_payments_email = $config_data->getConfig('now_payments_email');
        $now_payments_password = $config_data->getConfig('now_payments_password');

        $data = [
            'offline_remain_setting' => $offline_remain_setting,
            'kizuner_share' => $kizuner_share,
            'kizuner_first_add_post' => $kizuner_first_add_post,
            'nearby_radius' => $nearby_radius,
            'map_radius' => $map_radius,
            'now_payments_email' => $now_payments_email,
            'now_payments_password' => $now_payments_password
        ];

        return view('config::index', $data);
    }

    public function save(Request $request)
    {

        $badge_config = ConfigEntity::where('path', 'offline_remain')->first();
        if ($badge_config) {
            $badge_config->value = json_encode([
                'day_num' => $request->get('day_num'),
                'content' => $request->get('content')
            ]);
            $badge_config->save();
        }

        if ($request->has('kizuner_share')) {
            $kizuner_share = ConfigEntity::where('path', 'kizuner_share')->first();
            if (!$kizuner_share) {
                $kizuner_share = new ConfigEntity();
                $kizuner_share->path = 'kizuner_share';
                $kizuner_share->value = $request->kizuner_share;
                $kizuner_share->save();
            } else {
                $kizuner_share->value = $request->kizuner_share;
                $kizuner_share->save();
            }
        }
        if ($request->has('kizuner_first_add_post')) {
            $kizuner_first_add_post = ConfigEntity::where('path', 'kizuner_first_add_post')->first();
            if (!$kizuner_first_add_post) {
                $kizuner_first_add_post = new ConfigEntity();
                $kizuner_first_add_post->path = 'kizuner_first_add_post';
                $kizuner_first_add_post->value = $request->kizuner_first_add_post;
                $kizuner_first_add_post->save();
            } else {
                $kizuner_first_add_post->value = $request->kizuner_first_add_post;
                $kizuner_first_add_post->save();
            }
        }

        if ($request->has('nearby_radius')) {
            $kizuner_first_add_post = ConfigEntity::where('path', 'nearby_radius')->first();
            if (!$kizuner_first_add_post) {
                $kizuner_first_add_post = new ConfigEntity();
                $kizuner_first_add_post->path = 'nearby_radius';
                $kizuner_first_add_post->value = $request->nearby_radius;
                $kizuner_first_add_post->save();
            } else {
                $kizuner_first_add_post->value = $request->nearby_radius;
                $kizuner_first_add_post->save();
            }
        }

        if ($request->has('map_radius')) {
            $kizuner_first_add_post = ConfigEntity::where('path', 'map_radius')->first();
            if (!$kizuner_first_add_post) {
                $kizuner_first_add_post = new ConfigEntity();
                $kizuner_first_add_post->path = 'map_radius';
                $kizuner_first_add_post->value = $request->map_radius;
                $kizuner_first_add_post->save();
            } else {
                $kizuner_first_add_post->value = $request->map_radius;
                $kizuner_first_add_post->save();
            }
        }

        if ($request->has('now_payments_email')) {
            $config = ConfigEntity::where('path', 'now_payments_email')->first();
            if (!$config) {
                $config = new ConfigEntity();
                $config->path = 'now_payments_email';
                $config->value = $request->now_payments_email;
                $config->save();
            } else {
                $config->value = $request->now_payments_email;
                $config->save();
            }
        }

        if ($request->has('now_payments_password')) {
            $config = ConfigEntity::where('path', 'now_payments_password')->first();
            if (!$config) {
                $config = new ConfigEntity();
                $config->path = 'now_payments_password';
                $config->value = $request->now_payments_password;
                $config->save();
            } else {
                $config->value = $request->now_payments_password;
                $config->save();
            }
        }

        return redirect(route('admin.config-setting.index'))->withSuccess('Update successful!');
    }
}
