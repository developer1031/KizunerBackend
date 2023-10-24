<?php
Route::get('/', 'RatingController@index');
Route::post('/', 'RatingController@store');
Route::put('{id}', 'RatingController@update');
Route::delete("{id}", 'RatingController@destroy');

Route::get('users/{userId?}', 'UserController@show');
