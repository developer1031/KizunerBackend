<?php
/**
 * Category
 */
Route::post('users/categories', 'CategoryController@addCategories');
Route::get('categories/search', 'CategoryController@search');

