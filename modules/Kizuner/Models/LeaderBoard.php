<?php

namespace Modules\Kizuner\Models;

use App\User;
use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Illuminate\Database\Eloquent\Model;
use Modules\Config\Config;
use Modules\Wallet\Domains\Dto\HistoryDto;
use Modules\Wallet\Domains\Entities\HistoryEntity;
use Modules\Wallet\Domains\Wallet;
use Modules\Wallet\Domains\History;

class LeaderBoard extends Model
{
    use Uuid;
    protected $table = 'leader_board';

    //protected $guarded = ['id'];
    protected $fillable = ['user_id', 'point', 'badge', 'reward'];

    public function user() {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function update_point($point = 0) {
        if($point > 0) {
            $this->point += $point;
            $this->save();
            $is_up = $this->refreshBadge();
            return $is_up;
        }
        return false;
    }

    public function refreshBadge() {

        //Update Badge
        $config = new Config();
        $badge_01 = json_decode($config->getConfig('badge_01'), true);
        $badge_01_point = intval($badge_01['point']);
        $badge_01_reward = intval($badge_01['reward']);

        $badge_02 = json_decode($config->getConfig('badge_02'), true);
        $badge_02_point = intval($badge_02['point']);
        $badge_02_reward = intval($badge_02['reward']);

        $badge_03 = json_decode($config->getConfig('badge_03'), true);
        $badge_03_point = intval($badge_03['point']);
        $badge_03_reward = intval($badge_03['reward']);

        $badge_04 = json_decode($config->getConfig('badge_04'), true);
        $badge_04_point = intval($badge_04['point']);
        $badge_04_reward = intval($badge_04['reward']);

        $badge_05 = json_decode($config->getConfig('badge_05'), true);
        $badge_05_point = intval($badge_05['point']);
        $badge_05_reward = intval($badge_05['reward']);

        $point = $this->point;
        $current_badge = $this->badge;
        $reward = 0;

        switch ($point) {
            case $point < $badge_01_point:
                $this->badge = 0;
                break;

            case $point >= $badge_01_point && $point < $badge_02_point:
                $this->badge = 1;
                $reward = $badge_01_reward;
                break;

            case $point >= $badge_02_point && $point < $badge_03_point:
                $this->badge = 2;
                $reward = $badge_02_reward;
                break;

            case $point >= $badge_03_point && $point < $badge_04_point:
                $this->badge = 3;
                $reward = $badge_03_reward;
                break;

            case $point >= $badge_04_point && $point < $badge_05_point:
                $this->badge = 4;
                $reward = $badge_04_reward;
                break;

            case $point >= $badge_05_point:
                $this->badge = 5;
                $reward = $badge_05_reward;
                break;

        }
        $this->save();

        if($this->badge > $current_badge) {
            $wallet = Wallet::findByUserId($this->user_id);
            Wallet::updateBalance($wallet->id, $reward);

            //Add to Transaction History
            $admin_user = User::where('email', 'admin@admin.com')->first();
            History::create(new HistoryDto(
                $this->user_id,
                $admin_user ? $admin_user->id : $this->user_id,
                null,
                HistoryEntity::TYPE_LEVEL_UP,
                HistoryEntity::BALANCE_ADD,
                $reward,
                0
            ));
        }
        return $this->badge > $current_badge;
    }
}
