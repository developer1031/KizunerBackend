<?php

use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Chat\Domains\Entities\MemberEntity;
use Modules\Config\Config;
use Modules\Helps\Models\Help;
use Modules\Helps\Models\SampleHelp;
use Modules\Kizuner\Models\LeaderBoard;
use Modules\Kizuner\Models\Location;
use Modules\Kizuner\Models\User\Follow;
use Modules\KizunerApi\Events\AddedPointSocketEvent;
use Modules\Notification\Device\UserDeviceToken;
use Modules\Notification\Domains\Notification;
use Modules\Notification\Domains\NotificationDto;
use Modules\Notification\Domains\NotificationEntity;
use Modules\Notification\Notification\Mails\MailTag;
use Modules\Notification\Notification\PushNotificationJob;
use Modules\Wallet\Domains\Dto\HistoryDto;
use Modules\Wallet\Domains\Entities\HistoryEntity;
use Modules\Wallet\Domains\Entities\TransactionEntity;
use Modules\Wallet\Domains\History;
use Modules\Wallet\Domains\Transaction;
use Modules\Wallet\Domains\Wallet;

if (!function_exists('is_image_file_uploaded')) {
  function is_image_file_uploaded($file)
  {
    try {
      $pattern = "/image\//i";
      return preg_match($pattern, $file->getMimeType()) > 0;
    } catch (Exception $e) {
      return false;
    }
  }
}

if (!function_exists('detectRiver')) {
  function detectRiver($lat, $lng)
  {
    //$lat = '10.005365';
    //$lng= '105.824542';
    //$key = 'AIzaSyB8LnFj1sl6d138saehdhGk45WbLsg7Emg';
    $key = 'AIzaSyACJ2b7aQZWFfNCO_XOHdRyAJDNDOLGqcM';
    $GMAPStaticUrl = "https://maps.googleapis.com/maps/api/staticmap?center=" . $lat . "," . $lng . "&size=40x40&maptype=roadmap&sensor=false&zoom=12&key=" . $key;

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
    $hexaColor = imagecolorat($image, 0, 0);
    $color_tran = imagecolorsforindex($image, $hexaColor);

    $hexaColor2 = imagecolorat($image, 0, 1);
    $color_tran2 = imagecolorsforindex($image, $hexaColor2);

    $hexaColor3 = imagecolorat($image, 0, 2);
    $color_tran3 = imagecolorsforindex($image, $hexaColor3);

    $red = $color_tran['red'] + $color_tran2['red'] + $color_tran3['red'];
    $green = $color_tran['green'] + $color_tran2['green'] + $color_tran3['green'];
    $blue = $color_tran['blue'] + $color_tran2['blue'] + $color_tran3['blue'];

    //imagedestroy($image);
    //var_dump($red,$green,$blue);
    //int(492) int(570) int(660)
    if ($red == 510 && $green == 654 && $blue == 765) {
      //echo 1;
      return 1;
    } else {
      //echo 0;
      return 0;
    }
  }
}


/*
 * GenerateLocation
 * $coord = [$lat, $lon]
 * return array Lat & Lon
 */
if (!function_exists('generateLocation')) {
  function generateLocation($coord, $radiusKm = 15, $precision = 4)
  {

    for ($i = 0; $i < 5; $i++) {
      $radiusRad = $radiusKm / 111.3;
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

      if (!detectRiver($newY, $newX)) {
        break;
      } else {
        $newY = $coord['lat'];
        $newX = $coord['lon'];
      }
    }
    return ['lat' => $newY, 'lng' => $newX];
  }
}



/*
 * Generate Fake Casts
 */
if (!function_exists('generateFakeCast')) {
  function generateFakeCast($typeObj, $fake_number = 5)
  {

    try {

      //\Illuminate\Support\Facades\Log::info($typeObj->location);
      $obj_location = $typeObj->location;
      $obj_address = explode(',', $obj_location->address);
      $length_obj_address = count($obj_address);
      $format_address = [];
      if ($length_obj_address) {
        if (isset($obj_address[$length_obj_address - 3]) && $obj_address[$length_obj_address - 3]) {
          array_push($format_address, $obj_address[$length_obj_address - 3]);
        }
        if (isset($obj_address[$length_obj_address - 2]) && $obj_address[$length_obj_address - 2]) {
          array_push($format_address, $obj_address[$length_obj_address - 2]);
        }
        if (isset($obj_address[$length_obj_address - 1]) && $obj_address[$length_obj_address - 1]) {
          array_push($format_address, $obj_address[$length_obj_address - 1]);
        }
      }
      if (count($format_address)) {
        $format_address = implode(',', $format_address);
      } else
        $format_address = '.';

      //$faker = new Faker\Generator();
      //$faker = Faker\Factory::create('vi_VN');
      $faker = Faker\Factory::create('en_US');
      $gender = $faker->randomElement([0, 1]);

      for ($i = 0; $i < $fake_number; $i++) {
        $user = App\User::create([
          'name' => $faker->name($gender),
          //'name' => (new Faker\Provider\vi_VN\Person())->name($gender==1 ? 'male' : 'female'),
          'email' => $faker->email,
          'phone' => $faker->phoneNumber,
          'password' => bcrypt('secret'),
          'gender' => $gender,
          'birth_date' => $faker->dateTimeBetween('1980-01-01', '2010-12-31')->format('Y-m-d'),
          'is_fake' => 1,
          'fake_avatar' => 'https://picsum.photos/id/' . rand(1, 1000) . '/200/300'
        ]);

        //Sync Skills
        $skills = [];
        foreach ($typeObj->skills as $skill) {
          array_push($skills, $skill->id);
        }
        $allSkills = \Modules\Kizuner\Models\Skill::take(20)->get();
        $limit_skill = count($allSkills) > 5 ? 5 : count($allSkills);
        //array_push($skills, ($allSkills[rand(0,$limit_skill )])->id);
        //array_push($skills, ($allSkills[rand(0, $limit_skill)])->id);
        $user->skills()->sync($skills);

        //Update Location
        $new_location = generateLocation([
          'lat' => $obj_location->lat,
          'lon' => $obj_location->lng
        ]);
        $location_data = [
          'address'   => $format_address,
          'lat'       => $new_location['lat'],
          'lng'       => $new_location['lng']
        ];
        $location = new Location($location_data);
        $location->save();
        $user->location()->save($location);
      }
    } catch (Exception $e) {
      Log::info("Create Fake Cast");
      Log::info($e->getMessage());
    }
  }
}

if (!function_exists('generateFakeHangouts')) {
  function generateFakeHangouts($typeObj, $fake_number = 3, $request_help = null)
  {

    try {
      //\Illuminate\Support\Facades\Log::info($typeObj->location);
      //$obj_location = $typeObj->location;

      $obj_location = $typeObj->user->location;
      $resident = $typeObj->user->address ? json_decode($typeObj->user->address) : null;
      if ($resident) {
        $obj_location->lat = $resident->residentLat;
        $obj_location->lng = $resident->residentLng;
      }

      //Current location
      if ($request_help && $request_help->has('current_location_lat') && $request_help->has('current_location_long')) {
        $obj_location->lat = $request_help->get('current_location_lat');
        $obj_location->lng = $request_help->get('current_location_long');
      }

      $obj_address = explode(',', $obj_location->address);
      $length_obj_address = count($obj_address);
      $format_address = [];
      if ($length_obj_address) {
        if (isset($obj_address[$length_obj_address - 3]) && $obj_address[$length_obj_address - 3]) {
          array_push($format_address, $obj_address[$length_obj_address - 3]);
        }
        if (isset($obj_address[$length_obj_address - 2]) && $obj_address[$length_obj_address - 2]) {
          array_push($format_address, $obj_address[$length_obj_address - 2]);
        }
        if (isset($obj_address[$length_obj_address - 1]) && $obj_address[$length_obj_address - 1]) {
          array_push($format_address, $obj_address[$length_obj_address - 1]);
        }
      }
      if (count($format_address)) {
        $format_address = implode(',', $format_address);
      } else
        $format_address = '.';

      //$faker = new Faker\Generator();

      $faker = Faker\Factory::create('en_US');
      $gender = $faker->randomElement([0, 1]);
      $disk = \Storage::disk('gcs');
      $fake_sample_ids = [];

      $skills = [];
      foreach ($typeObj->skills as $skill) {
        array_push($skills, $skill->id);
      }
      //Append user's skills
      $current_user = auth()->user();
      $user_skills = $current_user->skills;
      if ($user_skills) {
        $user_skills = $user_skills->pluck('id');
        foreach ($user_skills as $user_skill) {
          array_push($skills, $user_skill);
        }
      }

      $sampleHelpIds = [];
      foreach ($skills as $skill) {
        $sampleHelpId = DB::table('skillables')
          ->where('skill_id', $skill)
          ->where('skillable_type', SampleHelp::class)
          ->whereNotIn('skillable_id', $sampleHelpIds)
          ->orderByRaw('RAND()')
          ->first();

        if ($sampleHelpId) {
          array_push($sampleHelpIds, $sampleHelpId->skillable_id);
          $sampleHelp = SampleHelp::where('id', $sampleHelpId->skillable_id)->where('type', 'hangout')->first();

          if ($sampleHelp) {
            //Create Fake User
            $user = App\User::create([
              'name' => $faker->name($gender),
              'email' => $faker->email,
              'phone' => $faker->phoneNumber,
              'password' => bcrypt('secret'),
              'gender' => $gender,
              'birth_date' => $faker->dateTimeBetween('1980-01-01', '2010-12-31')->format('Y-m-d'),
              'is_fake' => 1,
              'fake_avatar' => 'https://picsum.photos/id/' . rand(1, 1000) . '/200/300'
            ]);
            $user->skills()->sync($skills);

            //Update Location
            $new_location = generateLocation([
              'lat' => $obj_location->lat,
              'lon' => $obj_location->lng
            ]);
            $location_data = [
              'address' => $format_address,
              'lat' => $new_location['lat'],
              'lng' => $new_location['lng']
            ];
            $location = new Location($location_data);
            $location->save();
            $user->location()->save($location);

            //Create Fake Hangout
            $capacity = rand(1, 10);
            $hangoutData = [
              "is_fake" => 1,
              "title" => $sampleHelp->title,
              "description" => $sampleHelp->description,
              "cover_img" => $sampleHelp->media ? $sampleHelp->media->path : 'https://picsum.photos/id/' . rand(1, 600) . '/200/300',
              "address" => $format_address,
              "kizuna" => rand(10, 30),
              "lat" => $new_location['lat'],
              "lng" => $new_location['lng'],
              "start" => Carbon::now()->addDays(15),
              "end" => Carbon::now()->addDays(20),
              "skills" => $skills,
              "capacity" => $capacity
            ];

            $hangout = new \Modules\Kizuner\Models\Hangout($hangoutData);
            $hangout->user_id = $user->id;
            $hangout->available = $capacity;
            $hangout->save();

            $locationData = [
              'address' => $format_address,
              'lat' => $new_location['lat'],
              'lng' => $new_location['lng']
            ];
            $location = new Location($locationData);
            $location->save();
            $hangout->location()->save($location);
            $hangout->skills()->sync($sampleHelp->skills ? $sampleHelp->skills->pluck('id') : []);
            $hangout->categories()->sync($sampleHelp->categories ? array_filter($sampleHelp->categories->pluck('id'), 'strlen') : []);
          }
        }
      }
    } catch (Exception $e) {
      Log::info("Create Fake hangout");
      Log::info($e->getMessage());
    }

    //        for($i = 0; $i < $fake_number; $i++) {
    //            try {
    //                //Sync Skills
    //                $skills = [];
    //                $firstSkill = null;
    //                foreach ($typeObj->skills as $skill) {
    //                    $firstSkill = $skill->id;
    //                    array_push($skills, $skill->id);
    //                }
    //
    //                //Append user's skills
    //                /*
    //                $current_user = auth()->user();
    //                $user_skills = $current_user->skills;
    //                if($user_skills) {
    //                    $user_skills = $user_skills->pluck('id');
    //                    foreach ($user_skills as $user_skill) {
    //                        array_push($skills, $user_skill);
    //                    }
    //                }
    //                */
    //
    //                $allSkills = \Modules\Kizuner\Models\Skill::take(20)->get();
    //                $limit_skill = count($allSkills) > 5 ? 5 : count($allSkills);
    //                //array_push($skills, ($allSkills[rand(0,$limit_skill )])->id);
    //                //array_push($skills, ($allSkills[rand(0, $limit_skill)])->id);
    //
    //                /*
    //                 * generate fake Hangout for this user
    //                 * - Get sample Hangout (random by Skill)
    //                 * - create Hangout with this sample
    //                 */
    //                $sampleHelp = null;
    //                for($sampleCount = 0; $sampleCount < 3; $sampleCount++) {
    //                    if($firstSkill) {
    //                        //$firstSkill = '699f66cf-e4a8-40ec-bb92-bc6cc53ce6fc';
    //                        $sampleHelpId = DB::table('skillables')
    //                            //->where('skill_id', $firstSkill)
    //                            ->whereIn('skill_id', $skills)
    //                            ->where('skillable_type', SampleHelp::class)
    //                            ->orderByRaw('RAND()')
    //                            ->first();
    //                        if(!$sampleHelpId) {
    //                            $sampleHelp = SampleHelp::where('type', 'hangout')->orderByRaw('RAND()')->first();
    //                        }
    //                        else {
    //                            //$sampleHelp = SampleHelp::find($sampleHelpId->skillable_id);
    //                            $sampleHelp = SampleHelp::where('id', $sampleHelpId->skillable_id)->where('type', 'hangout')->orderByRaw('RAND()')->first();
    //                        }
    //                    }
    //                    else {
    //                        $sampleHelpId = DB::table('skillables')
    //                            ->where('skillable_type', SampleHelp::class)
    //                            ->orderByRaw('RAND()')
    //                            ->first();
    //                        if(!$sampleHelpId) {
    //                            $sampleHelp = SampleHelp::where('type', 'hangout')->orderByRaw('RAND()')->first();
    //                        }
    //                        else {
    //                            //$sampleHelp = SampleHelp::find($sampleHelpId->skillable_id);
    //                            $sampleHelp = SampleHelp::where('id', $sampleHelpId->skillable_id)->where('type', 'hangout')->orderByRaw('RAND()')->first();
    //                        }
    //                    }
    //                    if(!$sampleHelp) {
    //                        $sampleHelp = SampleHelp::where('type', 'hangout')->orderByRaw('RAND()')->first();
    //                    }
    //                    if( !in_array($sampleHelp->id, $fake_sample_ids) )
    //                        break;
    //                }
    //                if($sampleHelp && !in_array($sampleHelp->id, $fake_sample_ids)) {
    //
    //                    //Create Fake User
    //                    $user = App\User::create([
    //                        'name' => $faker->name($gender),
    //                        //'name' => (new Faker\Provider\vi_VN\Person())->name($gender==1 ? 'male' : 'female'),
    //                        'email' => $faker->email,
    //                        'phone' => $faker->phoneNumber,
    //                        'password' => bcrypt('secret'),
    //                        'gender' => $gender,
    //                        'birth_date' => $faker->dateTimeBetween('1980-01-01', '2010-12-31')->format('Y-m-d'),
    //                        'is_fake' => 1,
    //                        'fake_avatar' => 'https://picsum.photos/id/'. rand(1, 1000) .'/200/300'
    //                    ]);
    //                    $user->skills()->sync($skills);
    //
    //                    //Update Location
    //                    $new_location = generateLocation([
    //                        'lat' => $obj_location->lat,
    //                        'lon' => $obj_location->lng
    //                    ]);
    //                    $location_data = [
    //                        'address'   => $format_address,
    //                        'lat'       => $new_location['lat'],
    //                        'lng'       => $new_location['lng']
    //                    ];
    //                    $location = new Location($location_data);
    //                    $location->save();
    //                    $user->location()->save($location);
    //
    //                    //Create Fake Hangout
    //                    $capacity = rand(1, 10);
    //                    $hangoutData = [
    //                        "is_fake" => 1,
    //                        "title" => $sampleHelp->title,
    //                        "description" => $sampleHelp->description,
    //                        //"cover_img" => 'https://picsum.photos/id/'. rand(1, 600) .'/200/300',
    //                        "cover_img" => $sampleHelp->media ? $sampleHelp->media->path : 'https://picsum.photos/id/'. rand(1, 600) .'/200/300',
    //                        "address" => $format_address,
    //                        "kizuna" => rand(10, 30),
    //                        "lat" => $new_location['lat'],
    //                        "lng" => $new_location['lng'],
    //                        "start" => Carbon::now()->addMinutes(45),
    //                        "end" => Carbon::now()->addDays(4),
    //                        "skills" => $skills,
    //                        "capacity"=> $capacity
    //                    ];
    //
    //                    $hangout = new \Modules\Kizuner\Models\Hangout($hangoutData);
    //                    $hangout->user_id = $user->id;
    //                    $hangout->available = $capacity;
    //                    $hangout->save();
    //
    //                    $locationData = [
    //                        'address' => $format_address,
    //                        'lat' => $new_location['lat'],
    //                        'lng' => $new_location['lng']
    //                    ];
    //                    $location = new Location($locationData);
    //                    $location->save();
    //                    $hangout->location()->save($location);
    //                    $hangout->skills()->sync($skills);
    //                }
    //
    //                //Save Sample_id
    //                array_push($fake_sample_ids, $sampleHelp->id);
    //            }
    //            catch (Exception $e) {}
    //        }
  }
}


/*
 * Generate Fake Casts
 */
if (!function_exists('generateFakeUserHelps')) {
  function generateFakeUserHelps($typeObj, $fake_number = 5, $request = null)
  {
    try {
      //\Illuminate\Support\Facades\Log::info($typeObj->location);
      //$obj_location = $typeObj->location;

      $obj_location = $typeObj->user->location;

      $resident = $typeObj->user->address ? json_decode($typeObj->user->address) : null;
      if ($resident) {
        $obj_location->lat = $resident->residentLat;
        $obj_location->lng = $resident->residentLng;
      }

      //Current location
      if ($request && $request->has('current_location_lat') && $request->has('current_location_long')) {
        $obj_location->lat = $request->get('current_location_lat');
        $obj_location->lng = $request->get('current_location_long');
      }

      $obj_address = explode(',', $obj_location->address);
      $length_obj_address = count($obj_address);
      $format_address = [];
      if ($length_obj_address) {
        if (isset($obj_address[$length_obj_address - 3]) && $obj_address[$length_obj_address - 3]) {
          array_push($format_address, $obj_address[$length_obj_address - 3]);
        }
        if (isset($obj_address[$length_obj_address - 2]) && $obj_address[$length_obj_address - 2]) {
          array_push($format_address, $obj_address[$length_obj_address - 2]);
        }
        if (isset($obj_address[$length_obj_address - 1]) && $obj_address[$length_obj_address - 1]) {
          array_push($format_address, $obj_address[$length_obj_address - 1]);
        }
      }
      if (count($format_address)) {
        $format_address = implode(',', $format_address);
      } else {
        $format_address = '.';
      }

      $faker = Faker\Factory::create('en_US');
      $gender = $faker->randomElement([0, 1]);

      $skills = [];
      foreach ($typeObj->skills as $skill) {
        array_push($skills, $skill->id);
      }
      //Append user's skills
      $current_user = auth()->user();
      $user_skills = $current_user->skills;
      if ($user_skills) {
        $user_skills = $user_skills->pluck('id');
        foreach ($user_skills as $user_skill) {
          array_push($skills, $user_skill);
        }
      }

      $sampleHelpIds = [];
      foreach ($skills as $skill) {

        $sampleHelpId = DB::table('skillables')
          ->where('skill_id', $skill)
          ->where('skillable_type', SampleHelp::class)
          ->whereNotIn('skillable_id', $sampleHelpIds)
          ->orderByRaw('RAND()')
          ->first();

        if ($sampleHelpId) {
          array_push($sampleHelpIds, $sampleHelpId->skillable_id);
          $sampleHelp = SampleHelp::where('id', $sampleHelpId->skillable_id)->where('type', 'help')->first();

          //Log::info('$sampleHelp');
          //Log::info($sampleHelp);

          if ($sampleHelp) {
            $user = App\User::create([
              'name' => $faker->name($gender),
              'email' => $faker->email,
              'phone' => $faker->phoneNumber,
              'password' => bcrypt('secret'),
              'gender' => $gender,
              'birth_date' => $faker->dateTimeBetween('1980-01-01', '2010-12-31')->format('Y-m-d'),
              'is_fake' => 1,
              'fake_avatar' => 'https://picsum.photos/id/' . rand(1, 1000) . '/200/300'
            ]);
            $user->skills()->sync($skills);

            //Update Location
            $new_location = generateLocation([
              'lat' => $obj_location->lat,
              'lon' => $obj_location->lng
            ]);
            $location_data = [
              'address'   => $format_address,
              'lat'       => $new_location['lat'],
              'lng'       => $new_location['lng']
            ];
            $location = new Location($location_data);
            $location->save();
            $user->location()->save($location);

            $capacity = rand(1, 10);
            $helpData = [
              "is_fake" => 1,
              "title" => $sampleHelp->title,
              "description" => $sampleHelp->description,
              //"cover_img" => 'https://picsum.photos/id/'. rand(1, 600) .'/200/300',
              "cover_img" => $sampleHelp->media ? $sampleHelp->media->path : 'https://picsum.photos/id/' . rand(1, 600) . '/200/300',
              "address" => $format_address,
              "budget" => rand(10, 30),
              "lat" => $new_location['lat'],
              "lng" => $new_location['lng'],
              "start" => Carbon::now()->addDays(15),
              "end" => Carbon::now()->addDays(25),
              "skills" => $skills,
              "capacity" => $capacity
            ];

            $help = new Help($helpData);
            $help->user_id = $user->id;
            $help->available = $capacity;
            $help->save();

            $locationData = [
              'address' => $helpData['address'],
              'lat' => $helpData['lat'],
              'lng' => $helpData['lng']
            ];
            $location = new Location($locationData);
            $location->save();
            $help->location()->save($location);
            $help->skills()->sync($sampleHelp->skills ? $sampleHelp->skills->pluck('id') :  []);
            $help->categories()->sync($sampleHelp->categories ? array_filter($sampleHelp->categories->pluck('id'), 'strlen') : []);
          }
        }
      }
    } catch (Exception $e) {
      Log::info($e->getMessage());
    }


    $fake_sample_ids = [];
    for ($i = 0; $i < $fake_number; $i++) {
      \Log::debug("___________FAKE____________");
      try {
        //Sync Skills
        $skills = [];
        $firstSkill = null;
        foreach ($typeObj->skills as $skill) {
          $firstSkill = $skill->id;
          array_push($skills, $skill->id);
        }

        //Append user's skills
        /*
               $current_user = auth()->user();
               $user_skills = $current_user->skills;
               if($user_skills) {
                   $user_skills = $user_skills->pluck('id');
                   foreach ($user_skills as $user_skill) {
                       array_push($skills, $user_skill);
                   }
               }
               */

        $allSkills = \Modules\Kizuner\Models\Skill::take(20)->get();
        $limit_skill = count($allSkills) > 5 ? 5 : count($allSkills);
        //array_push($skills, ($allSkills[rand(0,$limit_skill )])->id);
        //array_push($skills, ($allSkills[rand(0, $limit_skill)])->id);

        /*
                * generate fake Helps for this user
                * - Get sample Help (random by Skill)
                * - create Help with this sample
                */
        $sampleHelp = null;
        for ($sampleCount = 0; $sampleCount < 3; $sampleCount++) {
          if ($firstSkill) {
            $sampleHelpId = DB::table('skillables')
              //->where('skill_id', $firstSkill)
              ->whereIn('skill_id', $skills)
              ->where('skillable_type', SampleHelp::class)
              ->orderByRaw('RAND()')
              ->first();

            if (!$sampleHelpId) {
              $sampleHelp = SampleHelp::where('type', 'help')->orderByRaw('RAND()')->first();
            } else {
              $sampleHelp = SampleHelp::where('id', $sampleHelpId->skillable_id)->where('type', 'help')->orderByRaw('RAND()')->first();
            }
          } else {
            $sampleHelpId = DB::table('skillables')
              ->where('skillable_type', SampleHelp::class)
              ->orderByRaw('RAND()')
              ->first();
            if (!$sampleHelpId) {
              $sampleHelp = SampleHelp::where('type', 'help')->orderByRaw('RAND()')->first();
            } else {
              $sampleHelp = SampleHelp::where('id', $sampleHelpId->skillable_id)->where('type', 'help')->orderByRaw('RAND()')->first();
            }
          }
          if (!$sampleHelp) {
            $sampleHelp = SampleHelp::where('type', 'help')->orderByRaw('RAND()')->first();
          }
          if (!in_array($sampleHelp->id, $fake_sample_ids))
            break;
        }

        if ($sampleHelp && !in_array($sampleHelp->id, $fake_sample_ids)) {
          $user = App\User::create([
            'name' => $faker->name($gender),
            //'name' => (new Faker\Provider\vi_VN\Person())->name($gender==1 ? 'male' : 'female'),
            'email' => $faker->email,
            'phone' => $faker->phoneNumber,
            'password' => bcrypt('secret'),
            'gender' => $gender,
            'birth_date' => $faker->dateTimeBetween('1980-01-01', '2010-12-31')->format('Y-m-d'),
            'is_fake' => 1,
            'fake_avatar' => 'https://picsum.photos/id/' . rand(1, 1000) . '/200/300'
          ]);
          $user->skills()->sync($skills);

          //Update Location
          $new_location = generateLocation([
            'lat' => $obj_location->lat,
            'lon' => $obj_location->lng
          ]);
          $location_data = [
            'address'   => $format_address,
            'lat'       => $new_location['lat'],
            'lng'       => $new_location['lng']
          ];
          $location = new Location($location_data);
          $location->save();
          $user->location()->save($location);

          $capacity = rand(1, 10);
          $helpData = [
            "is_fake" => 1,
            "title" => $sampleHelp->title,
            "description" => $sampleHelp->description,
            //"cover_img" => 'https://picsum.photos/id/'. rand(1, 600) .'/200/300',
            "cover_img" => $sampleHelp->media ? $sampleHelp->media->path : 'https://picsum.photos/id/' . rand(1, 600) . '/200/300',
            "address" => $format_address,
            "budget" => rand(10, 30),
            "lat" => $new_location['lat'],
            "lng" => $new_location['lng'],
            "start" => Carbon::now()->addMinutes(45),
            "end" => Carbon::now()->addDays(4),
            "skills" => $skills,
            "capacity" => $capacity
          ];

          $help = new Help($helpData);
          $help->user_id = $user->id;
          $help->available = $capacity;
          $help->save();

          $locationData = [
            'address' => $helpData['address'],
            'lat' => $helpData['lat'],
            'lng' => $helpData['lng']
          ];
          $location = new Location($locationData);
          $location->save();
          $help->location()->save($location);
          $help->skills()->sync($skills);
        }
        //Save sample ID
        array_push($fake_sample_ids, $sampleHelp->id);
      } catch (Exception $e) {
      }
    }
  }
}


/*
 * Get Friend + Follow
 * @param: user_id
 * @return array user_ids
 */
if (!function_exists('getFriendsFollows')) {
  function getFriendsFollows($user_id)
  {
    $followList = [];

    //Follow
    $follow_users = Follow::where('user_id', $user_id)->orWhere('follow_id', $user_id)->get();
    foreach ($follow_users as $follow_user) {
      array_push($followList, $follow_user->user_id);
      array_push($followList, $follow_user->follow_id);
    }

    //Friends
    $friends_users = \Modules\Kizuner\Models\User\Friend::where('user_id', $user_id)->orWhere('friend_id', $user_id)->get();
    foreach ($friends_users as $friends_user) {
      array_push($followList, $friends_user->user_id);
      array_push($followList, $friends_user->friend_id);
    }
    array_push($followList, $user_id);

    $followList = array_unique($followList);
    return $followList;
  }
}

/*
 * Age from birth date
 */
if (!function_exists('ageFromBirthDate')) {
  function ageFromBirthDate($birthdate)
  {
    return date_diff(date_create($birthdate), date_create('today'))->y;
  }
}


/*
 * Get Hangout Statistic
 */
if (!function_exists('getStatistic')) {
  function getStatistic($type = 'hangout')
  {

    if ($type == 'hangout') {
      $statistic = [];
      for ($i = 5; $i >= 1; $i--) {
        $startMonth = Carbon::now()->subMonths($i)->startOfMonth();
        $endMonth = Carbon::now()->subMonths($i)->endOfMonth();

        $statistic[] = DB::table('hangout_hangouts')
          ->whereNull('deleted_at')
          ->whereBetween('created_at', [$startMonth, $endMonth])
          ->count();
      }
    } else if ($type == 'hangout_offer') {
      $statistic = [];
      for ($i = 5; $i >= 1; $i--) {
        $startMonth = Carbon::now()->subMonths($i)->startOfMonth();
        $endMonth = Carbon::now()->subMonths($i)->endOfMonth();

        $statistic[] = DB::table('hangout_offers')
          ->whereNull('deleted_at')
          ->whereBetween('created_at', [$startMonth, $endMonth])
          ->count();
      }
    } else if ($type == 'help') {
      $statistic = [];
      for ($i = 5; $i >= 1; $i--) {
        $startMonth = Carbon::now()->subMonths($i)->startOfMonth();
        $endMonth = Carbon::now()->subMonths($i)->endOfMonth();

        $statistic[] = DB::table('help_helps')
          ->whereNull('deleted_at')
          ->whereBetween('created_at', [$startMonth, $endMonth])
          ->count();
      }
    } else if ($type == 'help_offer') {
      $statistic = [];
      for ($i = 5; $i >= 1; $i--) {
        $startMonth = Carbon::now()->subMonths($i)->startOfMonth();
        $endMonth = Carbon::now()->subMonths($i)->endOfMonth();

        $statistic[] = DB::table('help_helps')
          ->whereNull('deleted_at')
          ->whereBetween('created_at', [$startMonth, $endMonth])
          ->count();
      }
    }

    //Likes
    else if ($type == 'likes') {
      $statistic = [];
      for ($i = 5; $i >= 1; $i--) {
        $startMonth = Carbon::now()->subMonths($i)->startOfMonth();
        $endMonth = Carbon::now()->subMonths($i)->endOfMonth();

        $statistic[] = DB::table('reacts')
          ->where('react_type', 'like')
          ->whereBetween('created_at', [$startMonth, $endMonth])
          ->count();
      }
    }

    //Shares
    else if ($type == 'shares') {
      $statistic = [];
      for ($i = 5; $i >= 1; $i--) {
        $startMonth = Carbon::now()->subMonths($i)->startOfMonth();
        $endMonth = Carbon::now()->subMonths($i)->endOfMonth();

        $statistic[] = DB::table('reacts')
          ->where('react_type', 'share')
          ->whereBetween('created_at', [$startMonth, $endMonth])
          ->count();
      }
    }

    //Comments
    else if ($type == 'comments') {
      $statistic = [];
      for ($i = 5; $i >= 1; $i--) {
        $startMonth = Carbon::now()->subMonths($i)->startOfMonth();
        $endMonth = Carbon::now()->subMonths($i)->endOfMonth();

        $statistic[] = DB::table('comment_comments')
          ->whereBetween('created_at', [$startMonth, $endMonth])
          ->count();
      }
    }

    return $statistic;
  }
}


/*
 * Get Hangout Statistic
 */
if (!function_exists('addPoint')) {
  function addPoint($point = 1, $user = null)
  {
    $user_id = $user ? $user->id : app('request')->user()->id;
    $leaderboard = LeaderBoard::where('user_id', $user_id)->first();
    if ($leaderboard) {
      $is_up = $leaderboard->update_point($point);
    } else {
      LeaderBoard::create([
        //'user_id' => app('request')->user()->id,
        'user_id' => $user_id,
        'point' => 0
      ]);
      //$leaderboard = LeaderBoard::where('user_id', app('request')->user()->id)->first();
      $leaderboard = LeaderBoard::where('user_id', $user_id)->first();
      $is_up = $leaderboard->update_point($point);
    }

    event(new AddedPointSocketEvent($is_up, $user));

    /* comment notify and mail function */
    if ($is_up) {
      // $user = User::where('id', $user_id)->first();
      // if($user) {
      //     $user->notify(new MailTag('up_level'));
      // }

      //Send noti for all friends
      // try {
      //     $image = null;
      //     $userMedia = $user->medias()->where('type', 'user.avatar')->first();
      //     if ($userMedia) {
      //         $image = \Storage::disk('gcs')->url($userMedia->thumb);
      //     }
      //     $message = $user->name . ' has just been level up.';
      //     $type    = 'level_up';
      //     $payload = [
      //         'relation' => [
      //             'id'        => $user->id,
      //             'type'      => 'user'
      //         ],
      //         'type'          => $type,
      //         'message'       => '<b>' . $user->name . '</b>' . ' has just been level up.'
      //     ];

      //     $friends = $user->friends;
      //     foreach ($friends as $friend) {


      //         $data = (new NotificationDto())
      //             ->setUserId($friend)
      //             ->setTitle('Kizuner')
      //             ->setBody($message)
      //             ->setPayload($payload)
      //             ->setType($type)
      //             ->setUploadableId(null);
      //         $notification = Notification::create($data);

      //         $token = UserDeviceToken::getUserDevice($friend->friend_id);
      //         if ($token) {
      //             $data = (new NotificationDto())
      //                 ->setUserId($friend)
      //                 ->setTitle('Kizuner')
      //                 ->setBody($message)
      //                 ->setPayload($payload)
      //                 ->setType($type)
      //                 ->setUploadableId($userMedia ? $userMedia->uploadable_id : null);
      //             $notification = Notification::create($data);

      //             $payload['image'] = $image;
      //             $payload['id'] = $notification->id;
      //             $payload['unread_count'] = getUnreadNotification($friend->friend_id);
      //             PushNotificationJob::dispatch('sendBatchNotification', [
      //                 [$token], [
      //                     'topicName'     => 'kizuner',
      //                     'title'         => $notification->title,
      //                     'body'          => $notification->body,
      //                     'payload'       => $payload
      //                 ],
      //             ]);
      //         }
      //     }
      // }
      // catch (Exception $e) {
      //     Log::info('Level-up... Errror');
      //     Log::info($e->getMessage());
      // }
    }
  }
}

/*
 * Add Kizuna if First add
 */
if (!function_exists('addKizuna')) {
  function addKizuna($user, $kz = 30)
  {
    if (!count($user->feedTimelines) && !$user->is_added_first_post) {
      //Add to wallet
      $wallet = Wallet::findByUserId($user->id);
      Wallet::updateBalance($wallet->id, $kz);

      //Add to Transaction History
      $admin_user = User::where('email', 'admin@admin.com')->first();
      History::create(new HistoryDto(
        $user->id,
        $admin_user ? $admin_user->id : $user->id,
        null,
        HistoryEntity::TYPE_FIRST_POST,
        HistoryEntity::BALANCE_ADD,
        0,
        0
      ));

      //Update status to user
      $user->is_added_first_post = 1;
      $user->save();

      event(new AddedPointSocketEvent());
    }
  }
}

/*
 * Add Kizuna on Share
 */
if (!function_exists('addKizunaOnShare')) {
  function addKizunaOnShare($user, $kz = 30)
  {
    if ($user && !$user->is_first_shared) {
      $wallet = Wallet::findByUserId($user->id);
      Wallet::updateBalance($wallet->id, $kz);

      //Add to Transaction History
      $admin_user = User::where('email', 'admin@admin.com')->first();

      History::create(new HistoryDto(
        $user->id,
        $admin_user ? $admin_user->id : $user->id,
        null,
        HistoryEntity::TYPE_SHARE_POST,
        HistoryEntity::BALANCE_ADD,
        $kz,
        0
      ));

      Transaction::create(
        $admin_user->id,
        $user->id,
        $kz,
        TransactionEntity::TYPE_SHARE_POST,
        0
      );

      $user->is_first_shared = 1;
      $user->save();

      event(new AddedPointSocketEvent());
    }
  }
}

/*
 * Add Kizuna on Share
 */
if (!function_exists('pushNotiPrivateByRoomId')) {
  function pushNotiPrivateByRoomId($room_id, $except_user)
  {
    $members = MemberEntity::where('room_id', $room_id)->get();

    foreach ($members as $member) {
      if ($member->user_id != $except_user) {
        $user = User::find($member->user_id);
        dump($user);
      } else {
        dump('dup');
      }
    }
  }
}

if (!function_exists('dynamicUrl')) {
  function dynamicUrl($type, $id)
  {

    return url(config('app.open_inapp_url') . '?type=' . $type . '&id=' . $id);
    //return url(config('app.open_inapp_url') . '/' . $type . '?id=' . $id);

    $disk = \Storage::disk('gcs');
    if ($type == 'help') {
      $help = Help::find($id);
      if ($help) {
        $media = $help->media;
        if ($media)
          return $disk->url($media->path) . '?id=' . $help->id;
      }
    }
    if ($type == 'hangout') {
      $hangout = \Modules\Kizuner\Models\Hangout::find($id);
      if ($hangout) {
        $media = $hangout->media;
        if ($media)
          return $disk->url($media->path) . '?id=' . $hangout->id;
      }
    }
    if ($type == 'status') {
      $status = \Modules\Kizuner\Models\Status::find($id);
      if ($status) {
        $media = $status->media;
        if ($media)
          return $disk->url($media->path) . '?id=' . $status->id;
      }
    }

    //return 'uhwuwu';
    return url(config('app.open_inapp_url') . '/' . $type . '?id=' . $id);
  }
}

if (!function_exists('getUnreadNotification')) {
  function getUnreadNotification($user_id)
  {
    return NotificationEntity::where([
      'user_id' => $user_id,
      'status'  => 0
    ])->count();
  }
}

if (!function_exists('getUserFakeAvatar')) {
  function getUserFakeAvatar($user_id)
  {
    $user = User::find($user_id);
    if ($user) {
      return $user->fake_avatar;
    }
  }
}

if (!function_exists('getSql')) {
  function getSql($query)
  {
    $bindings = $query->getBindings();

    return preg_replace_callback('/\?/', function ($match) use (&$bindings, $query) {
      return $query->getConnection()->getPdo()->quote(array_shift($bindings));
    }, $query->toSql());
  }
}