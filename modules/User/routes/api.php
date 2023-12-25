<?php
Route::post('users/sign-in', 'Api\AuthController@signIn');
Route::post('users/social-sign-in', 'Api\AuthController@socialSignIn');
Route::post('users/sign-up', 'Api\AuthController@signUp');
Route::get('users/{id?}', 'Api\AuthController@getUser')->middleware('auth:api', 'user.access');
Route::post('users/logout', 'Api\AuthController@logout')->middleware('auth:api');
Route::get('redirect_code', 'Api\AuthController@authCode');

Route::put('users/general', 'Api\UpdateController@updateGeneralInfo')->middleware('auth:api');
Route::put('users/identify', 'Api\UpdateController@updateIdentityInfo')->middleware('auth:api');
Route::put('users/media', 'Api\UpdateController@updateMedia')->middleware('auth:api');
Route::put('users/auth', 'Api\UpdateController@updateAuthInfo')->middleware('auth:api');
Route::put('users/location', 'Api\UpdateController@updateLocation')->middleware('auth:api');
Route::delete('users/media', 'Api\UpdateController@removeMedia')->middleware('auth:api');

Route::post('users/reset-password',     'Api\ResetPasswordController@requestReset');
Route::post('users/reset-password/pin',         'Api\ResetPasswordController@pinVerify');
Route::post('users/reset-password/update',    'Api\ResetPasswordController@resetPassword');

Route::patch('users-email-verify/confirm',  'Api\AuthController@emailConfirmation')->middleware('auth:api');
Route::patch('users-email-verify',  'Api\AuthController@verifyEmail')->middleware('auth:api');

Route::post('user-phone-verify', 'Api\AuthController@phoneConfirmation')->middleware('auth:api');

Route::get('tutorial_images', 'Api\AuthController@getTutorialImages');

//Invite in contact list
Route::post('users/invite-by-contact-list', 'Api\AuthController@inviteByContactList')->middleware('auth:api');
