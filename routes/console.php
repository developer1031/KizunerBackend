<?php

use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Chat\Domains\Actions\CreateMessageAction;
use Modules\Chat\Domains\Dto\MessageDto;
use Modules\Chat\Domains\Entities\MemberEntity;
use Modules\Chat\Domains\Entities\MessageEntity;
use Modules\Chat\Domains\Entities\RoomEntity;
use Modules\Chat\Domains\Events\MessageCreatedEvent;
use Modules\Chat\Domains\Member;
use Modules\Chat\Domains\Message;
use Modules\Chat\Domains\Room;
use Modules\Feed\Models\Timeline;
use Modules\Framework\Support\Facades\EntityManager;
use Modules\Helps\Models\Help;
use Modules\Kizuner\Models\Hangout;
use Modules\Kizuner\Models\LeaderBoard;
use Modules\Kizuner\Models\Offer;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->describe('Display an inspiring quote');

Artisan::command('generateLocation', function () {

    $coord = ['lat' => 10.7758439, 'lon' => 106.7017555];
    $radiusKm = 5;
    $precision = 4;

    $radiusRad = $radiusKm/111.3;
    $y0 = $coord['lat'];
    $x0 = $coord['lon'];
    $u = \lcg_value();
    $v = \lcg_value();
    $w = $radiusRad * \sqrt($u);
    $t = 2 * M_PI * $v;
    $x = $w * \cos($t);
    $y1 = $w * \sin($t);
    $x1 = $x / \cos(\deg2rad($y0));
    $newY = \round($y0 + $y1, $precision);
    $newX = \round($x0 + $x1, $precision);

    $result =  ['lat' => $newY, 'lon' => $newX];

    dump($result);

})->describe('generateLocation');

Artisan::command('generate_chat_room_location', function () {
    $tmp_locations = DB::table('location_tmp')->where('country', '<>' ,'Vietnam')->get();
    $type = 'location';

    foreach ($tmp_locations as $tmp_location) {
        $room = EntityManager::create(RoomEntity::class);

        dump('Processing: ' . $tmp_location->country . ' - ' . $tmp_location->city);

        if($tmp_location->city) {
            $room->name     = $tmp_location->city;
            $room->type     = $type;
            $room->status   = RoomEntity::STATUS_ACTIVE;
            $room->country  = $tmp_location->country;
            $room->city     = $tmp_location->city;
            $room->latitude = $tmp_location->latitude;
            $room->longitude= $tmp_location->longitude;
            $room->altitude = $tmp_location->altitude;
            $room->save();
        }
    }
    dd('Done!');
});

Artisan::command('generate_leader_board', function () {

    //Clean data
    LeaderBoard::truncate();

    //count point from Timeline
    dump('---count point from Timeline---');
    $timelines = DB::table('feed_timelines')->selectRaw('user_id, COUNT(`user_id`) AS `point`')->groupBy('user_id')->get();

    foreach ($timelines as $timeline) {
        dump('Processing: ' . $timeline->user_id);
        $leader_board = LeaderBoard::create([
            'user_id' => $timeline->user_id,
            'point' => $timeline->point,
        ]);
    }

    //Count Hangout Offer
    dump('---Count Hangout Offer---');
    $hangout_offers = DB::table('hangout_offers')->selectRaw('`sender_id`, COUNT(`sender_id`) AS `point`')->groupBy('sender_id')->get();
    foreach ($hangout_offers as $hangout_offer) {
        dump('Processing: ' . $hangout_offer->sender_id);

        $leader_board = LeaderBoard::where('user_id', $hangout_offer->sender_id)->first();
        if($leader_board) {
            $leader_board->update_point($hangout_offer->point);
        }
        else {
            $leader_board = LeaderBoard::create([
                'user_id' => $hangout_offer->sender_id,
                'point' => $hangout_offer->point
            ]);
        }
    }

    //Count Help Offer
    dump('Count Help Offer');

    $help_offers = DB::table('help_offers')->selectRaw('`sender_id`, COUNT(`sender_id`) AS `point`')->groupBy('sender_id')->get();
    foreach ($help_offers as $help_offer) {
        dump('Processing: ' . $help_offer->sender_id);

        $leader_board = LeaderBoard::where('user_id', $help_offer->sender_id)->first();
        if($leader_board) {
            $leader_board->update_point($help_offer->point);
        }
        else {
            $leader_board = LeaderBoard::create([
                'user_id' => $help_offer->sender_id,
                'point' => $help_offer->point
            ]);

        }
    }


    $leader_boards = LeaderBoard::all();
    foreach ($leader_boards as $leaderBoard) {
        $leaderBoard->refreshBadge();
    }

//    //Dump Badge
//
//    $config = new Modules\Config\Config();
//    $badge_01 = json_decode($config->getConfig('badge_01'), true);
//    $badge_01_point = intval($badge_01['point']);
//
//    $badge_02 = json_decode($config->getConfig('badge_02'), true);
//    $badge_02_point = intval($badge_02['point']);
//
//    $badge_03 = json_decode($config->getConfig('badge_03'), true);
//    $badge_03_point = intval($badge_03['point']);
//
//    $badge_04 = json_decode($config->getConfig('badge_04'), true);
//    $badge_04_point = intval($badge_04['point']);
//
//    $badge_05 = json_decode($config->getConfig('badge_05'), true);
//    $badge_05_point = intval($badge_05['point']);
//
//    $leader_boards = LeaderBoard::all();
//    foreach ($leader_boards as $leader_board) {
//        $point = $leader_board->point;
//        switch ($point) {
//            case $point <= $badge_01_point:
//                $leader_board->badge = 1;
//                break;
//
//            case $point > $badge_01_point && $point <= $badge_02_point:
//                $leader_board->badge = 2;
//                break;
//
//            case $point > $badge_02_point && $point <= $badge_03_point:
//                $leader_board->badge = 3;
//                break;
//
//            case $point > $badge_03_point && $point <= $badge_04_point:
//                $leader_board->badge = 4;
//                break;
//
//            case $point > $badge_04_point:
//                $leader_board->badge = 5;
//                break;
//        }
//        $leader_board->save();
//    }

});

Artisan::command('test', function () {


    $array = ['1', '2', '3', '', '5'];
    dd( array_filter($array, 'strlen') );





    $email = 'huong.nguyen@inapps.net';
    $user = \App\User::where('email', $email)->first();

    //Append user's skills
    $current_user = $user;
    $user_skills = $current_user->skills->pluck('id');

    $sampleHelpIds = [];
    foreach ($user_skills as $skill) {
        $sampleHelp = null;
        $sampleHelpId = DB::table('skillables')
            ->where('skill_id', $skill)
            ->whereNotIn('skillable_id', $sampleHelpIds)
            ->where('skillable_type', \Modules\Helps\Models\SampleHelp::class)
            ->orderByRaw('RAND()')
            ->first();

        if($sampleHelpId) {
            array_push($sampleHelpIds, $sampleHelpId->skillable_id);
        }
    }

    dd($sampleHelpIds);
    dd($skills);


//    try {
//        $image = null;
//        $userMedia = $user->medias()->where('type', 'user.avatar')->first();
//        if ($userMedia) {
//            $image = \Storage::disk('gcs')->url($userMedia->thumb);
//        }
//        $message = $user->name . ' has just been level up.';
//        $type    = 'level_up';
//        $payload = [
//            'relation' => [
//                'id'        => $user->id,
//                'type'      => 'user'
//            ],
//            'type'          => $type,
//            'message'       => '<b>' . $user->name . '</b>' . ' has just been level up.'
//        ];
//
//        $friends = $user->friends;
//        foreach ($friends as $friend) {
//
//            dump($friend);
//
//            $token = \Modules\Notification\Device\UserDeviceToken::getUserDevice($friend->friend_id);
//
//            dump($token);
//
//            if ($token) {
//                $data = (new \Modules\Notification\Domains\NotificationDto())
//                    ->setUserId($friend)
//                    ->setTitle('Kizuner')
//                    ->setBody($message)
//                    ->setPayload($payload)
//                    ->setType($type)
//                    ->setUploadableId($userMedia ? $userMedia->uploadable_id : null);
//                $notification = \Modules\Notification\Domains\Notification::create($data);
//
//                $payload['image'] = $image;
//                $payload['id'] = $notification->id;
//                $payload['unread_count'] = getUnreadNotification($friend->friend_id);
//                \Modules\Notification\Notification\PushNotificationJob::dispatch('sendBatchNotification', [
//                    [$token], [
//                        'topicName'     => 'kizuner',
//                        'title'         => $notification->title,
//                        'body'          => $notification->body,
//                        'payload'       => $payload
//                    ],
//                ]);
//            }
//        }
//    }
//    catch (Exception $e) {
//        dump($e->getMessage());
//    }

    //Send noti to users
    /*
    $today = \Carbon\Carbon::now();
    $hangouts = \Modules\Kizuner\Models\Hangout::doesntHave('offers')->where('is_sent_to_users', 0)->orderBy('created_at', 'desc')->whereDate('created_at', $today->format('Y-m-d'))->get();
    foreach ($hangouts as $hangout) {
        $specialities = $hangout->skills->pluck('id')->toArray();
        $skill_users = DB::table('skillables')
            ->select('users.id as user_id', 'users.email as email', 'users.fcm_token', 'users.name')
            ->leftJoin('users', 'skillables.skillable_id', '=', 'users.id')
            ->whereIn('skill_id', $specialities)
            ->where('skillable_type', \App\User::class)
            ->where('is_fake', 0)
            ->groupBy('skillable_id')->get();

        foreach ($skill_users as $skill_user) {
            \Modules\Notification\Job\Hangout\HangoutFitUsersJob::dispatch($hangout, $skill_user->user_id);
        }
    }
    */
    //dd($hangouts->count());
    //Send noti to Admin
    //echo( dynamicUrl('status', '1222') );
    //pushNotiPrivateByRoomId('b6c8142b-a2e4-409f-8de3-715d2323049f', 'cd4448d8-f2c3-40eb-9ce5-b4e224c7bc20');

    /*
    $leader_boards = LeaderBoard::all();
    foreach ($leader_boards as $leaderBoard) {
        $leaderBoard->refreshBadge();
    }
    */

//    function cutNumber($number) {
//        $precision = substr($number, strpos($number, '.'), 4); // 3 because . plus 2 precision
//        return substr($number, 0, strpos($number, '.')).$precision;
//    }
//
//    $locations = \Modules\Kizuner\Models\Location::all();
//    //$locations = \Modules\Kizuner\Models\Location::where('id', '00369096-5b9e-4f34-b1e4-25c5b8a755b8')->get();
//    foreach ($locations as $location) {
//
//        $location->lat = cutNumber($location->lat);
//        $location->lng = cutNumber($location->lng);
//        $location->save();
//    }


//    $users = \App\User::where('is_fake', 1)->get();
//    foreach ($users as $user) {
//        $user->fake_avatar = 'https://picsum.photos/id/'. rand(1, 600) .'/200/300';
//        $user->save();
//    }


//    $leader_boards = LeaderBoard::all();
//    foreach ($leader_boards as $leader_board) {
//
//
//
//        die;
//    }

    /*
    $faker = Faker\Factory::create('vi_VN');
    $gender = $faker->randomElement([0, 1]);

    $user = [
        'name' => $faker->name($gender),
        'avatar' => 'https://source.unsplash.com/random',
        'age' => ageFromBirthDate('1984-7-12')
    ];

    dd($user);
    */

//    $users = \App\User::where('is_fake', 1)->get();
//    foreach ($users as $user) {
//        $user->fake_avatar = 'https://picsum.photos/id/'. rand(1, 1000) .'/200/300';
//        $user->save();
//    }

})->describe('Test command');


Artisan::command('findRealUser', function () {

    //$message = MessageEntity::find('0171a741-ad05-4a7c-b1b4-0c51d2afbcab');
    //event(new MessageCreatedEvent($message, false));
    //die;

    //Find all fake chat - group by user_id
    $fake_messages = \Modules\Chat\Domains\Entities\MessageEntity::select('user_id', 'room_id', 'is_fake')->where('is_fake', 1)->groupBy('user_id')->get();

    //Foreach user
    foreach ($fake_messages as $fake_message) {
        $chat_member = Member::findByRoomIdExceptUserId($fake_message->room_id, $fake_message->user_id);
        $realUser = \App\User::find($chat_member->user_id);

        //get all Spec => get random user has same Spec
        if($realUser) {
            $relateUser = DB::table('skillables')
                ->select(['skillables.*', 'users.email', 'users.name', 'users.id as user_id'])
                ->leftJoin('users', 'users.id', '=', 'skillables.skillable_id')
                ->where('skillable_type', \App\User::class)
                ->whereIn('skill_id', $realUser->skills()->get()->pluck('id'))
                ->where('users.is_fake', '<>', 1)
                ->whereNull('users.deleted')
                ->where('users.id', '<>', $realUser->id)->inRandomOrder()->first();

            //dd($relateUser->skillable_id); //user_id

            //Push noti with User profile to user_id
            if($relateUser) {
                //$text = 'I am busy now, I would like to introduce another one person who can help you. ['. $relateUser->name .']';
                $text = null;
                $messageDto = new MessageDto(
                    $fake_message->user_id,
                    $fake_message->room_id,
                    $text,
                    null,
                    null,
                    null,
                    null,
                    $relateUser->user_id
                );
                $chatRoom = Room::find($fake_message->room_id);
                $chatRoom->updated_at = Carbon::now();
                $chatRoom->save();

                $chatMembers = MemberEntity::where([
                    'room_id' => $fake_message->room_id,
                    'user_id' => $fake_message->user_id
                ])->first();
                $chatMembers->seen_at = Carbon::now();
                $chatMembers->save();

                DB::table('chat_messages')
                    ->where('room_id', $fake_message->room_id)
                    ->where('is_fake', 1)
                    ->update(array('is_fake' => 2));

                $message = (new CreateMessageAction($messageDto, true, $relateUser))->execute();
                //event(new MessageCreatedEvent($message, false));
            }
        }
    }

})->describe('Find Real User');


Artisan::command('detectRiver', function () {

    $lat = '10.005365';
    $lng= '105.824542';
    $key = 'AIzaSyB8LnFj1sl6d138saehdhGk45WbLsg7Emg';

    $GMAPStaticUrl = "https://maps.googleapis.com/maps/api/staticmap?center=".$lat.",".$lng."&size=40x40&maptype=roadmap&sensor=false&zoom=12&key=" . $key;

    //echo $GMAPStaticUrl;
    $chuid = curl_init();
    curl_setopt($chuid, CURLOPT_URL, $GMAPStaticUrl);
    curl_setopt($chuid, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($chuid, CURLOPT_SSL_VERIFYPEER, FALSE);
    $data = trim(curl_exec($chuid));

    //dump($data);

    curl_close($chuid);
    $image = imagecreatefromstring($data);

    // this is for debug to print the image
    ob_start();
    imagepng($image);
    $contents =  ob_get_contents();
    ob_end_clean();
    //echo "<img src='data:image/png;base64,".base64_encode($contents)."' />";

    // here is the test : I only test 3 pixels ( enough to avoid rivers ... )
    $hexaColor = imagecolorat($image,0,0);
    $color_tran = imagecolorsforindex($image, $hexaColor);

    $hexaColor2 = imagecolorat($image,0,1);
    $color_tran2 = imagecolorsforindex($image, $hexaColor2);

    $hexaColor3 = imagecolorat($image,0,2);
    $color_tran3 = imagecolorsforindex($image, $hexaColor3);

    $red = $color_tran['red'] + $color_tran2['red'] + $color_tran3['red'];
    $green = $color_tran['green'] + $color_tran2['green'] + $color_tran3['green'];
    $blue = $color_tran['blue'] + $color_tran2['blue'] + $color_tran3['blue'];

    //imagedestroy($image);
    //var_dump($red,$green,$blue);
    //int(492) int(570) int(660)
    if($red == 510 && $green == 654 && $blue == 765) {
        //echo 1;
        return 1;
    }
    else {
        echo 0;
        //return 0;
    }
})->describe('testRiver');


Artisan::command('updateFirstPost', function () {
    $users = User::where('is_fake', '<>', 1)->get();

    foreach($users as $user) {

        if( count($user->feedTimelines)) {
            $user->is_added_first_post = 1;
            $user->save();
        }
    }
})->describe('testRiver');


Artisan::command('updateCompletePost', function () {

    $hangouts = Hangout::where(function($query){
        $query->where('is_fake', '<>', 1)->orWhereNull('is_fake');
    })
        ->where('is_completed', 0)->get();

    foreach($hangouts as $hangout) {
        foreach($hangout->offers as $offer) {
            if($offer->status==Offer::$status['completed']) {
                $hangout->is_completed = 1;
                $hangout->save();

                $feed_timeline = Timeline::where('reference_id', $hangout->id)->first();
                if($feed_timeline) {
                    $feed_timeline->status = 'inactive';
                    $feed_timeline->save();
                }
            }
        }
    }

    $helps = Help::where(function($query){
        $query->where('is_fake', '<>', 1)->orWhereNull('is_fake');
    })
        ->where('is_completed', 0)->get();

    foreach($helps as $help) {
        foreach($help->offers as $offer) {
            if($offer->status==Offer::$status['completed']) {
                $help->is_completed = 1;
                $help->save();

                $feed_timeline = Timeline::where('reference_id', $help->id)->first();
                if($feed_timeline) {
                    $feed_timeline->status = 'inactive';
                    $feed_timeline->save();
                }
            }
        }
    }



    //Update already Completed
    $hangouts = Hangout::where(function($query){
        $query->where('is_fake', '<>', 1)->orWhereNull('is_fake');
    })
        ->where('is_completed', 1)->get();

    foreach($hangouts as $hangout) {
        foreach($hangout->offers as $offer) {
            if($offer->status==Offer::$status['completed']) {
                $hangout->is_completed = 1;
                $hangout->save();

                $feed_timeline = Timeline::where('reference_id', $hangout->id)->first();
                if($feed_timeline) {
                    $feed_timeline->status = 'inactive';
                    $feed_timeline->save();
                }
            }
        }
    }

    $helps = Help::where(function($query){
        $query->where('is_fake', '<>', 1)->orWhereNull('is_fake');
    })
        ->where('is_completed', 1)->get();

    foreach($helps as $help) {
        foreach($help->offers as $offer) {
            if($offer->status==Offer::$status['completed']) {
                $help->is_completed = 1;
                $help->save();

                $feed_timeline = Timeline::where('reference_id', $help->id)->first();
                if($feed_timeline) {
                    $feed_timeline->status = 'inactive';
                    $feed_timeline->save();
                }
            }
        }
    }


    //Expired
    $now = Carbon::now();
    $hangouts = Hangout::where(function($query){
        $query->where('is_fake', '<>', 1)->orWhereNull('is_fake');
    })
    ->where(function($query) use ($now) {
        $query->whereNotNull('end')->where('end', '<', $now);
    })->get();

    foreach($hangouts as $hangout) {
        $feed_timeline = Timeline::where('reference_id', $hangout->id)->first();
                if($feed_timeline) {
                    $feed_timeline->status = 'inactive';
                    $feed_timeline->save();
                }
    }

    $helps = Help::where(function($query){
        $query->where('is_fake', '<>', 1)->orWhereNull('is_fake');
    })
    ->where(function($query) use ($now) {
        $query->whereNotNull('end')->where('end', '<', $now);
    })->get();

    foreach($helps as $help) {
        $feed_timeline = Timeline::where('reference_id', $help->id)->first();
                if($feed_timeline) {
                    $feed_timeline->status = 'inactive';
                    $feed_timeline->save();
                }
    }


})->describe('updateCompletePost');


Artisan::command('detectFakeImage', function () {

    $fakeUsers = User::where('is_fake', 1)->get();
    foreach($fakeUsers as $user) {
        $this->info($user->email);
        $url = $user->fake_avatar;
        if(@getimagesize($url)) {
            //$user->fake_avatar = 'https://picsum.photos/id/967/200/' . rand(1,300);
            $user->fake_avatar = 'https://picsum.photos/id/'. rand(1, 1000) .'/200/200';
            $user->save();
        }
    }
})->describe('updateCompletePost');


Artisan::command('user:generateAge', function () {

    $users = User::where('is_fake', 0)->get();
    foreach($users as $user) {
        if($user->birth_date)
            $user->age = Carbon::parse($user->birth_date)->age;
        else
            $user->age = -1;

        $user->save();
        $this->info($user->email . ' - Age: ' . $user->age);
    }
})->describe('user:generateAge');

