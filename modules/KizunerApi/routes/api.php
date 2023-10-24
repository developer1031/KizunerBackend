<?php
Route::post('hangouts', 'HangoutController@createNewHangout');
Route::put('hangouts/{id}', 'HangoutController@updateHangout');
Route::get('hangouts/{id?}', 'HangoutController@getHangoutListByUser')->middleware('user.access');
Route::get('hangouts/{hangOutId}/detail', 'HangoutController@getHangoutDetail')->middleware('user.access');
Route::delete('hangouts/{id}','HangoutController@delete');
Route::post('hangouts/{id}/update-status', 'HangoutController@updateStatus');

/**
 * Skill
 */
Route::post('users/skills', 'SkillController@addSkills');
Route::get('skills/search', 'SkillController@search');
Route::get('skills/{id}/hangouts', 'SkillController@getHangoutsList');

/**
 * Hangout Offer
 */
Route::post('hangouts/offer', 'Hangout\OfferController@offerHangout');
Route::get('hangouts/{id}/offers', 'Hangout\OfferController@getOffers');
Route::patch('hangouts/offer/{id}', 'Hangout\OfferController@changeOfferStatus');

/**
 * Guest and Cast Management
 */
Route::get('user-guests/{status?}', 'User\HangoutController@getGuestHangoutHistory');
Route::get('user-casts/{status?}', 'User\HangoutController@getCastHangoutHistory');

Route::post('hangouts/react', 'ReactController@reactHangout');

/**
 * Friendship
 */
Route::post('users-friends', 'User\FriendShipController@addFriendRequest');
Route::put('users-friends/{id}', 'User\FriendShipController@friendRequestReact');
Route::get('users-friends-list/{id?}', 'User\FriendShipController@getFriendLists');
Route::delete('users-friends/{id}', 'User\FriendShipController@unFriend');

Route::post('users-follows', 'User\FollowController@followUser');
Route::delete('users-follows/{id}', 'User\FollowController@unFollowUser');
Route::get('users-follows/{id?}', 'User\FollowController@getFollows');

Route::post('users-blocks', 'User\BlockController@blockUser');
Route::delete('users-blocks/{id}', 'User\BlockController@unBlock');
Route::get('users-blocks', 'User\BlockController@getBlockList');

/**
 * Status
 */
Route::post('statuses', 'StatusController@addStatus');
Route::get('statuses/{id}', 'StatusController@getStatus')->middleware('user.access');;
Route::put('statuses/{id}', 'StatusController@updateStatus');
Route::delete('statuses/{id}', 'StatusController@removeStatus');
Route::post('statuses/react', 'ReactController@reactStatus');

/*
 * Help
 */
Route::post('helps/react', 'ReactController@reactHelp');

/*
 * Reward
 */
Route::get('rewards', 'LeaderBoardController@getLeaderBoard');


/*
 * Settings
 */
//Route::get('setting/rewards', 'SettingsController@getRewards');


/*
 * Leader board by objects
 */
Route::get('leaderboard/by/{object}', 'LeaderBoardController@leaderboardBy');

Route::get('test/testHelpOfferAutoCompleteCommand', 'TestCommandController@testHelpOfferAutoCompleteCommand');

Route::post('users/support', 'UserSupportController@addUserSupport');












