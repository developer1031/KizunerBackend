<?php

/**
 * Generate Token
 */
Route::post('social-token', [\Modules\Auth\Http\Controllers\SocialTokenController::class, 'action']);
Route::post('token', [\Modules\Auth\Http\Controllers\TokenController::class, 'action']);

/**
 * Get new Token from Refresh Token
 */
Route::post('refresh-token', [\Modules\Auth\Http\Controllers\RefreshTokenController::class, 'action']);

/**
 * Remove Refresh Token
 */
Route::delete('token', [\Modules\Auth\Http\Controllers\RemoveTokenController::class, 'action']);
