<?php
Route::get('search', 'SearchController@index');

Route::get('suggest/nearby',    'HangoutNearByController@index');
Route::get('suggest/recommend', 'HangoutRecommendController@index');
Route::get('suggest/online',    'HangoutOnlineController@index');
