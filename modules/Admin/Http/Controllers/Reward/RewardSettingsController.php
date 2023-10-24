<?php

namespace Modules\Admin\Http\Controllers\Reward;

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

class RewardSettingsController
{
    public function index()
    {
        $badges = [
            'badge_01' => [
                'name' => 'Badge 1',
                'name_reward' => 'Reward 1',
                'point' => 10,
                'reward' => 5,
                'icon'  => null,
                'inactive_icon'  => null,
            ],
            'badge_02' => [
                'name' => 'Badge 2',
                'name_reward' => 'Reward 2',
                'point' => 20,
                'reward' => 10,
                'icon'  => null,
                'inactive_icon'  => null,
            ],
            'badge_03' => [
                'name' => 'Badge 3',
                'name_reward' => 'Reward 3',
                'point' => 30,
                'reward' => 15,
                'icon'  => null,
                'inactive_icon'  => null,
            ],
            'badge_04' => [
                'name' => 'Badge 4',
                'name_reward' => 'Reward 4',
                'point' => 40,
                'reward' => 20,
                'icon'  => null,
                'inactive_icon'  => null,
            ],
            'badge_05' => [
                'name' => 'Badge 5',
                'name_reward' => 'Reward 5',
                'point' => 50,
                'reward' => 25,
                'icon'  => null,
                'inactive_icon'  => null,
            ],
        ];

        foreach ($badges as $key => $value) {
            $badge = ConfigEntity::where('path', $key)->first();

            if (!$badge) {
                $config = new ConfigEntity();
                $config->path = $key;
                $config->value = json_encode($value);
                $config->save();
                $data[$key] = $value;
            }
            else {
                $data[$key] = json_decode($badge->value, true);
            }
        }

        $config_data = new Config();

        //Trophy icons
        $trophy_icons = $config_data->getConfig('trophy_icons');
        if(!$trophy_icons) {
            $config = new ConfigEntity();
            $config->path = 'trophy_icons';
            $config->value = json_encode(['icon_0' => '',  'icon_1' => '', 'icon_2' => '']);
            $config->save();
        }

        //Tutorial images
        $tutorial_setting = $config_data->getConfig('tutorial_setting');
        $tutorial_data = [
            ['image' => '', 'title' => '', 'description' => ''],
            ['image' => '', 'title' => '', 'description' => ''],
            ['image' => '', 'title' => '', 'description' => ''],
            ['image' => '', 'title' => '', 'description' => ''],
            ['image' => '', 'title' => '', 'description' => ''],
        ];
        if(!$tutorial_setting) {
            $config = new ConfigEntity();
            $config->path = 'tutorial_setting';
            $config->value = json_encode($tutorial_data);
            $config->save();
        }

        $trophy_icons = json_decode($config_data->getConfig('trophy_icons'), true);
        $tutorial_setting = json_decode($config_data->getConfig('tutorial_setting'), true);

        $reward_radius = $config_data->getConfig('reward_radius');
        $reward_radius = $reward_radius ? $reward_radius : 0;

        return view('reward-setting::index', compact('reward_radius', 'trophy_icons', 'tutorial_setting'))->with('data', $data);
    }

    public function store(Request $request) {
        $data = $request->except(['_token', 'reward_radius']);

        $disk = \Storage::disk('gcs');

        foreach ($data as $key => $badge) {

            $badge_config = ConfigEntity::where('path', $key)->first();
            if($badge_config) {

                $badge_value = json_decode($badge_config->value, true);
                $badge['icon'] = isset($badge_value['icon']) ? $badge_value['icon'] : null;
                $badge['inactive_icon'] = isset($badge_value['inactive_icon']) ? $badge_value['inactive_icon'] : null;

                if($file = $request->file($key.'_icon')) {
                    $original = Image::make($file)->encode('png', 90);
                    $fileName = pathinfo($file->hashName(), PATHINFO_FILENAME);
                    $saveOriginal = UploadPath::resolve() . '/' . date('Y/m/d') . '/' . $fileName . '-' . time() . '.png';
                    $originalRs = $original->stream();
                    $disk->put(
                        $saveOriginal,
                        $originalRs
                    );
                    $badge['icon'] = $saveOriginal;
                }
                if($file = $request->file($key.'_inactive_icon')) {
                    $original = Image::make($file)->encode('png', 90);
                    $fileName = pathinfo($file->hashName(), PATHINFO_FILENAME);
                    $saveOriginal = UploadPath::resolve() . '/' . date('Y/m/d') . '/' . $fileName . '-' . time() . '.png';
                    $originalRs = $original->stream();
                    $disk->put(
                        $saveOriginal,
                        $originalRs
                    );
                    $badge['inactive_icon'] = $saveOriginal;
                }

                $badge_config->value = json_encode($badge);
                $badge_config->save();
            }
        }

        $reward_radius = ConfigEntity::where('path', 'reward_radius')->first();
        if($reward_radius) {
            $reward_radius->value = $request->reward_radius;
            $reward_radius->save();
        }
        else {
            $config = new ConfigEntity();
            $config->path = 'reward_radius';
            $config->value = $request->reward_radius;
            $config->save();
        }

        //update leader-board
        $leader_boards = LeaderBoard::all();
        foreach ($leader_boards as $leaderBoard) {
            $leaderBoard->refreshBadge();
        }

        return redirect(route('admin.reward-setting.index'))->withSuccess('Update successful!');
    }

    public function storeTrophySetting(Request $request) {
        $disk = \Storage::disk('gcs');

        $trophy_icons_config = ConfigEntity::where('path', 'trophy_icons')->first();
        $trophy_icons_value = json_decode($trophy_icons_config->value, true);

        if($request->file()) {
            $trophy_icons_files = $request->file();

            $trophy_icons = [];
            foreach ($trophy_icons_files['trophy_icon'] as $key => $icon) {
                $original = Image::make($icon)->encode('png', 90);
                $fileName = pathinfo($icon->hashName(), PATHINFO_FILENAME);
                $saveOriginal = UploadPath::resolve() . '/' . date('Y/m/d') . '/' . $fileName . '-' . time() . '.png';


                $originalRs = $original->stream();
                $disk->put(
                    $saveOriginal,
                    $originalRs
                );


                $trophy_icons_value['icon_'. $key] = $saveOriginal;
            }
        }
        $trophy_icons_config->value = json_encode($trophy_icons_value);
        $trophy_icons_config->save();

        return redirect(route('admin.reward-setting.index'))->withSuccess('Update successful!');
    }

    public function storeTutorialSetting(Request $request) {

        $tutorial_setting = ConfigEntity::where('path', 'tutorial_setting')->first();
        $tutorial_setting_value = json_decode($tutorial_setting->value, true);

        //Update tutorial_title & tutorial_description
        foreach ($request->tutorial_title as $key => $title) {
            $tutorial_setting_value[$key]['title'] = $title;
            $tutorial_setting_value[$key]['disabled'] = false;
        }
        foreach ($request->tutorial_description as $key => $description) {
            $tutorial_setting_value[$key]['description'] = $description;
        }
        $tutorial_disabled = $request->tutorial_disabled ? $request->tutorial_disabled : [];
        foreach ($tutorial_disabled as $tutorial_disabled) {
            $tutorial_setting_value[$tutorial_disabled]['disabled'] = true;
        }

        $disk = \Storage::disk('gcs');

        if($request->file()) {
            $tutorial_images_files = $request->file();

            foreach ($tutorial_images_files['tutorial_images'] as $key => $icon) {
                $original = Image::make($icon)->encode('png', 90);
                $fileName = pathinfo($icon->hashName(), PATHINFO_FILENAME);
                $saveOriginal = UploadPath::resolve() . '/' . date('Y/m/d') . '/' . $fileName . '-' . time() . '.png';

                $originalRs = $original->stream();
                $disk->put(
                    $saveOriginal,
                    $originalRs
                );

                $tutorial_setting_value[$key]['image'] = $saveOriginal;
            }
        }

        $tutorial_setting->value = json_encode($tutorial_setting_value);
        $tutorial_setting->save();

        return redirect(route('admin.reward-setting.index'))->withSuccess('Update successful!');
    }
}
