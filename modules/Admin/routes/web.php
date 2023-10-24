<?php
/** Dashboard */
Route::get('dashboard', 'Dashboard\DashboardController@index')->name('admin.dashboard.index');
Route::get('dashboard/users/data', 'Dashboard\DashboardController@userData')->name('admin.dashboard.user.data');
Route::post('profile', 'User\ProfileController@update')->name('admin.user.profile');

Route::group(['prefix' => 'content'], function () {
    /** Content Term and Condition */
    Route::get('term', 'Content\TermPrivacyController@index')->name('admin.content.term.index');
    Route::post('term', 'Content\TermPrivacyController@update')->name('admin.content.term.update');

    /** Content Guide */
    Route::get('guides', 'Content\GuideController@index')->name('admin.content.guide.index');
    Route::get('guides/youtube', 'Content\YoutubeController@show')->name('admin.content.guide.youtube.show');
    Route::get('guides/data', 'Content\GuideController@data')->name('admin.content.guide.data');
    Route::post('guides', 'Content\GuideController@store')->name('admin.content.guide.store');
    Route::get('guides/{id}/edit', 'Content\GuideController@edit')->name('admin.content.guide.edit');
    Route::post('guides/update','Content\GuideController@update')->name('admin.content.guide.update');
    Route::get('guides/{id}/delete', 'Content\GuideController@destroy')->name('admin.content.guide.destroy');

    Route::get('guides/category', 'Content\GuideController@category')->name('admin.content.guide.category');
    Route::get('guides/category/data', 'Content\GuideController@categoryData')->name('admin.content.guide.category-data');
    Route::post('guides/category', 'Content\GuideController@createCategory')->name('admin.content.guide.category-store');
    Route::get('guides/category/{id}/edit', 'Content\GuideController@categoryEdit')->name('admin.content.guide.category.edit');
    Route::post('guides/category/{id}/update', 'Content\GuideController@categoryUpdate')->name('admin.content.guide.category-update');
    Route::get('guides/category/{id}/delete', 'Content\GuideController@categoryDelete')->name('admin.content.guide.category-delete');
});

/** Package Manage */
Route::group(['prefix' => 'packages'], function () {
    Route::get('data', 'Package\PackageController@data')->name('admin.package.data');
    Route::get('/', 'Package\PackageController@index')->name('admin.package.index');
    Route::post('/', 'Package\PackageController@store')->name('admin.package.store');
    Route::get('{id}/edit', 'Package\PackageController@edit')->name('admin.package.edit');
    Route::post('update', 'Package\PackageController@update')->name('admin.package.update');
});

/** Skill Manage */
Route::group(['prefix' => 'skills'], function () {
    Route::get('data', 'Skill\SkillController@data')->name('admin.skill.data');
    Route::get('/', 'Skill\SkillController@index')->name('admin.skill.index');
    Route::post('/', 'Skill\SkillController@store')->name('admin.skill.store');
    Route::get('{id}/edit', 'Skill\SkillController@edit')->name('admin.skill.edit');
    Route::post('update', 'Skill\SkillController@update')->name('admin.skill.update');
    Route::get('/{id}/delete', 'Skill\SkillController@delete')->name('admin.skill.delete');
    Route::get('data-speciality-dashboard', 'Skill\SkillController@specialityDataDasboard')->name('admin.skill.data-speciality-dashboard');

});

/** Category Manage */
Route::group(['prefix' => 'category'], function () {
    Route::get('data', 'Category\CategoryController@data')->name('admin.category.data');
    Route::get('/', 'Category\CategoryController@index')->name('admin.category.index');
    Route::post('/', 'Category\CategoryController@store')->name('admin.category.store');
    Route::get('{id}/edit', 'Category\CategoryController@edit')->name('admin.category.edit');
    Route::post('update', 'Category\CategoryController@update')->name('admin.category.update');
    Route::get('{id}/delete', 'Category\CategoryController@destroy')->name('admin.category.delete');
});

/** Users Manage */
Route::group(['prefix' => 'users'], function () {
    Route::get('data', 'User\UserController@data')->name('admin.user.data');
    Route::get('/', 'User\UserController@index')->name('admin.user.index');
    Route::get('/{id}', 'User\UserController@show')->name('admin.user.show');
    Route::post('/', 'User\UserController@store')->name('admin.user.store');
    Route::get('{id}/edit', 'User\UserController@edit')->name('admin.user.edit');
    Route::post('update/{id}', 'User\UserController@update')->name('admin.user.update');
    Route::get('{id}/transactions/data', 'User\TransactionController@data')->name('admin.user.transaction.data');
    Route::post('transactions', 'User\TransactionController@show')->name('admin.user.transaction.show');
    Route::post('transactions', 'User\TransactionController@show')->name('admin.user.transaction.show');
});

Route::group(['prefix' => 'hangouts'], function () {
    Route::get('data', 'Hangout\HangoutController@data')->name('admin.hangout.data');
    Route::get('/', 'Hangout\HangoutController@index')->name('admin.hangout.index');
    Route::get('{id}', 'Hangout\HangoutController@show')->name('admin.hangout.show');
    Route::get('{id}/delete', 'Hangout\HangoutController@destroy')->name('admin.hangout.delete');
    Route::get('{id}/offers', 'Hangout\OfferController@index')->name('admin.hangout.offer.index');
    Route::get('{id}/offers/data', 'Hangout\OfferController@data')->name('admin.hangout.offer.data');
});


Route::group(['prefix' => 'hangout-offers-cancel'], function () {
    Route::get('/data', 'HangoutOfferCancel\OfferCancelController@data')->name('admin.hangout.cancel.offer.data');
    Route::get('/', 'HangoutOfferCancel\OfferCancelController@index')->name('admin.hangout.cancel.offer.index');
    Route::get('{id}', 'HangoutOfferCancel\OfferCancelController@show')->name('admin.hangout.cancel.offer.show');
});

Route::group(['prefix' => 'help-offers-cancel'], function () {
    Route::get('/data', 'HelpOfferCancel\HelpOfferCancelController@data')->name('admin.help.cancel.offer.data');
    Route::get('/', 'HelpOfferCancel\HelpOfferCancelController@index')->name('admin.help.cancel.offer.index');
    Route::get('{id}', 'HelpOfferCancel\HelpOfferCancelController@show')->name('admin.help.cancel.offer.show');
});

Route::group(['prefix' => 'helps'], function () {
    Route::get('data', 'Help\HelpController@data')->name('admin.help.data');
    Route::get('/', 'Help\HelpController@index')->name('admin.help.index');
    Route::get('{id}', 'Help\HelpController@show')->name('admin.help.show');
    Route::get('{id}/delete', 'Help\HelpController@destroy')->name('admin.help.delete');
    Route::get('{id}/offers', 'Help\OfferController@index')->name('admin.help.offer.index');
    Route::get('{id}/offers/data', 'Help\OfferController@data')->name('admin.help.offer.data');
});

Route::group(['prefix' => 'statuses'], function () {
    Route::get('data', 'Status\StatusController@data')->name('admin.status.data');
    Route::get('/', 'Status\StatusController@index')->name('admin.status.index');
    Route::get('{id}/delete', 'Status\StatusController@destroy')->name('admin.status.destroy');
});

Route::group(['prefix' => 'supports'], function () {
    Route::get('data', 'Supports\SupportsController@data')->name('admin.supports.data');
    Route::get('/', 'Supports\SupportsController@index')->name('admin.supports.index');
    Route::get('{id}/delete', 'Supports\SupportsController@destroy')->name('admin.supports.destroy');
});

Route::get('logout', 'User\UserController@logout')->name('admin.user.logout');
Route::get('admin', 'AdminController@index')->name('admin.admin.list');
Route::get('admin/data', 'AdminController@data')->name('admin.admin.data');
Route::post('admin', 'AdminController@update')->name('admin.admin.update');
Route::get('admin/{id}', 'AdminController@show')->name('admin.admin.show');
Route::get('admin/{id}/delete', 'AdminController@destroy')->name('admin.admin.destroy');

Route::group(['prefix' => 'reports'], function () {
    Route::get('data', 'Report\ReportController@data')->name('admin.report.data');
    Route::get('/', 'Report\ReportController@index')->name('admin.report.index');
    Route::post('update', 'Report\ReportController@update')->name('admin.report.update');
});

Route::group(['prefix' => 'chat-intent'], function () {
    Route::get('data', 'ChatIntent\ChatIntentController@data')->name('admin.chat-intent.data');
    Route::get('/', 'ChatIntent\ChatIntentController@index')->name('admin.chat-intent.index');

    Route::post('/', 'ChatIntent\ChatIntentController@store')->name('admin.chat-intent.store');
    Route::get('{id}/edit', 'ChatIntent\ChatIntentController@edit')->name('admin.chat-intent.edit');
    Route::post('update', 'ChatIntent\ChatIntentController@update')->name('admin.chat-intent.update');
    Route::get('{id}/delete', 'ChatIntent\ChatIntentController@destroy')->name('admin.chat-intent.delete');
});

Route::group(['prefix' => 'chat-location'], function () {
    Route::get('data', 'ChatLocation\ChatLocationController@data')->name('admin.chat-location.data');
    Route::get('/', 'ChatLocation\ChatLocationController@index')->name('admin.chat-location.index');

    Route::post('/', 'ChatLocation\ChatLocationController@store')->name('admin.chat-location.store');
    Route::get('{id}/edit', 'ChatLocation\ChatLocationController@edit')->name('admin.chat-location.edit');
    Route::post('{id}/update', 'ChatLocation\ChatLocationController@update')->name('admin.chat-location.update');
    Route::get('{id}/delete', 'ChatLocation\ChatLocationController@destroy')->name('admin.chat-location.delete');
});

Route::group(['prefix' => 'chat-public-group'], function () {
    Route::get('data', 'ChatPublicGroup\ChatPublicGroupController@data')->name('admin.chat-public-group.data');
    Route::get('/', 'ChatPublicGroup\ChatPublicGroupController@index')->name('admin.chat-public-group.index');

    Route::post('/', 'ChatPublicGroup\ChatPublicGroupController@store')->name('admin.chat-public-group.store');
    Route::get('{id}/edit', 'ChatPublicGroup\ChatPublicGroupController@edit')->name('admin.chat-public-group.edit');
    Route::post('{id}/update', 'ChatPublicGroup\ChatPublicGroupController@update')->name('admin.chat-public-group.update');
    Route::get('{id}/delete', 'ChatPublicGroup\ChatPublicGroupController@destroy')->name('admin.chat-public-group.delete');
});

Route::group(['prefix' => 'reward-setting'], function () {
    Route::get('/', 'Reward\RewardSettingsController@index')->name('admin.reward-setting.index');
    Route::post('/', 'Reward\RewardSettingsController@store')->name('admin.reward-setting.index');

    Route::post('/trophy', 'Reward\RewardSettingsController@storeTrophySetting')->name('admin.reward-trophy-setting');
    Route::post('/tutorial', 'Reward\RewardSettingsController@storeTutorialSetting')->name('admin.reward-tutorial-setting');
});

Route::group(['prefix' => 'sample-help'], function () {
    Route::get('data', 'SampleHelp\SampleHelpController@data')->name('admin.sample-help.data');
    Route::get('/', 'SampleHelp\SampleHelpController@index')->name('admin.sample-help.index');

    Route::post('/', 'SampleHelp\SampleHelpController@store')->name('admin.sample-help.store');
    Route::get('{id}/edit', 'SampleHelp\SampleHelpController@edit')->name('admin.sample-help.edit');
    Route::post('{id}/update', 'SampleHelp\SampleHelpController@update')->name('admin.sample-help.update');
    Route::get('{id}/delete', 'SampleHelp\SampleHelpController@destroy')->name('admin.sample-help.delete');
    Route::post('/import', 'SampleHelp\SampleHelpController@import')->name('admin.sample-help.import');
});

Route::group(['prefix' => 'sample-hangout'], function () {
    Route::get('data', 'SampleHelp\SampleHelpController@dataHangout')->name('admin.sample-hangout.data');
    Route::get('/', 'SampleHelp\SampleHelpController@hangoutIndex')->name('admin.sample-hangout.index');

    Route::post('/', 'SampleHelp\SampleHelpController@hangoutStore')->name('admin.sample-hangout.store');
    Route::get('{id}/edit', 'SampleHelp\SampleHelpController@hangoutEdit')->name('admin.sample-hangout.edit');
    Route::post('{id}/update', 'SampleHelp\SampleHelpController@hangoutUpdate')->name('admin.sample-hangout.update');
    Route::get('{id}/delete', 'SampleHelp\SampleHelpController@hangoutDestroy')->name('admin.sample-hangout.delete');
    Route::post('/import', 'SampleHelp\SampleHelpController@importHangout')->name('admin.sample-hangout.import');
});

Route::group(['prefix' => 'config'], function () {
    Route::get('/', 'Config\ConfigController@index')->name('admin.config-setting.index');
    Route::post('/', 'Config\ConfigController@save')->name('admin.config-setting.index');
});



