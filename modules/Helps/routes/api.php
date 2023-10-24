<?php

Route::get('helps/user-casts/{status?}', 'HelpsController@getCastHelpHistory');
Route::get('helps/user-guest/{status?}', 'HelpsController@getGuestHelpHistory');

Route::post('helps', 'HelpsController@createNewHelp');
Route::get('helps/getCurrentTime', 'HelpsController@getCurrentTime');

Route::put('helps/{id}', 'HelpsController@updateHelp');
Route::get('helps/{id?}', 'HelpsController@getHangoutListByUser')->middleware('user.access');
Route::get('helps/{helpId}/detail', 'HelpsController@getHelpDetail')->middleware('user.access');
Route::delete('helps/{id}','HelpsController@delete');
Route::post('helps/{id}/update-status', 'HelpsController@updateStatus');
Route::post('helps/request-cancel', 'HelpsController@requestCancel');
/**
 * Help Offer
 */
//Route::post('helps/{id}/offer', 'HelpsController@offer');
Route::post('helps/offer', 'HelpsController@offer');
Route::get('helps/{id}/offers', 'HelpsController@getOffers');
Route::patch('helps/offer/{id}', 'HelpsController@changeOfferStatus');



/**
 * Short Link
 */
//Route::post('helps/{id}/offer', 'HelpsController@offer');
Route::post('shortlink', 'HelpsController@shortlinkStore');
//Route::get('share/{code}', 'HelpsController@shortenLink');
