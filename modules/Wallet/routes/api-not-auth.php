<?php
// Create connected account
Route::post('/stripe/webhook', 'StripeController@stripeWebhook');
// Now Payments Ipn callback
Route::post('/now-payments/ipn/{payment_type}', 'NowController@nowPaymentsIpnCallback');

Route::get('/stripe/return_url', 'StripeController@return_url');
Route::get('/stripe/refresh_url', 'StripeController@refresh_url');