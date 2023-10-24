<?php

Route::post('comments', [\Modules\CommentApi\Http\Controllers\Comments\CreateController::class, 'action']);
Route::get('comments/{id}', [\Modules\CommentApi\Http\Controllers\Comments\GetController::class, 'action']);
Route::put('comments/{id}', [\Modules\CommentApi\Http\Controllers\Comments\UpdateController::class, 'action']);
Route::delete('comments/{id}', [\Modules\CommentApi\Http\Controllers\Comments\DeleteController::class, 'action']);
