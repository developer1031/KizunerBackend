<?php

Route::get('users/timeline/{id?}', 'TimelineController@getTimeline')->middleware('user.access');
