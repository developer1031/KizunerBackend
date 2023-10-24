<?php
// Create connected account
Route::post('/stripe/webhook', 'StripeController@stripeWebhook');
// Now Payments Ipn callback
Route::post('/now-payments/ipn/{payment_type}', 'NowController@nowPaymentsIpnCallback');