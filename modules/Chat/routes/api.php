<?php
// Create new Chat Room - For Both Personal and Groups
Route::post('rooms', 'RoomController@store');

// Update Room Name
Route::put('rooms/{id}', 'RoomController@update');

// Delete a Room
Route::delete('rooms/{id}', 'RoomController@destroy');

// Add More Members to Chat Room
Route::put('rooms/{roomId}/members', 'MemberController@update');

// Remove Member from Chat Room, or leave Room
Route::delete('rooms/{roomId}/members/{memberId}', 'MemberController@destroy');

// Upload Images
Route::post('images', 'ImageController@store');
// Upload Video
Route::post('videos', 'VideoController@store');

// Create Chat Message
Route::post('messages', 'MessageController@store');
Route::delete('messages/{id}', 'MessageController@destroy');

// Get user all chat rooms
Route::get('rooms', 'RoomController@index');
Route::get('rooms/{id}', 'RoomController@show');

// Get Room Messages
Route::get('rooms/{roomId}/messages', 'MessageController@index');

// Search Room
Route::get('search', 'SearchController@index');

// Update User Status
Route::post('user/online', 'StatusController@update');

Route::post('rooms/{roomId}/seen', 'SeenController@update');

Route::post('messages/chatbot', 'MessageController@chatBot');



