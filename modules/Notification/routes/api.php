<?php
Route::post('devices', 'DeviceController@store');
Route::post('devices/notifications', 'NotificationController@update');
Route::get('devices/notifications', 'NotificationController@show');
Route::put('notifications/{id}', 'StatusController@update');
Route::put('notifications', 'ReadAllController@update');
Route::get('notifications/statistic', 'StatisticController@index');
Route::get('notifications', 'NotificationController@index');
Route::post('notifications/delete', 'NotificationController@destroy');
Route::get('notifications/{userId}/badge', 'BadgeController@show');
