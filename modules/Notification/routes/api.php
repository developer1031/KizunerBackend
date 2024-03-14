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

Route::post('devices/hangout_help_notification', 'NotificationController@hangout_help_notification');
Route::post('devices/message_notification', 'NotificationController@message_notification');
Route::post('devices/follow_notification', 'NotificationController@follow_notification');
Route::post('devices/comment_notification', 'NotificationController@comment_notification');
Route::post('devices/like_notification', 'NotificationController@like_notification');
Route::post('devices/payment_email_notification', 'NotificationController@payment_email_notification');