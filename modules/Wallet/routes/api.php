<?php
// Wallet Information include total and today income
Route::get('/', 'WalletController@index');
// Get cards
Route::get('cards/create', 'CardController@create');
// Store Card in DB
Route::post('cards', 'CardController@store');
// Get Users Card Lists
Route::get('cards', 'CardController@index');
// Delete user card
Route::delete('cards/{id}', 'CardController@destroy');
// Purchase a Package
Route::post('purchases', 'PurchaseController@store');
// Choose Package -> Choose Credit Card -> Purchase
Route::post('transactions', 'TransactionController@store');
// Transaction History
Route::get('transactions', 'HistoryController@index');

// Now Payments - Get currencies
Route::get('/now-payments/currencies', 'NowController@getNowPaymentsCurrencies');
// Now Payments - Get minimum payment amount
Route::get('/now-payments/min-amount', 'NowController@getNowPaymentsMinAmount');
// Now Payments - Get estimated price
Route::get('/now-payments/estimate', 'NowController@getNowPaymentsEstimate');

// Store crypto wallet in DB
Route::post('crypto-wallets', 'CryptoWalletController@store');
// Get Users crypto wallet Lists
Route::get('crypto-wallets', 'CryptoWalletController@index');
// Delete user crypto wallet
Route::delete('crypto-wallets/{id}', 'CryptoWalletController@destroy');

// Create connected account
Route::post('/stripe/connect', 'StripeController@createStripeConnect');
// Upload identity document
Route::post('/stripe/identity-document', 'StripeController@uploadIdentityDocument');
// Create connected account
Route::get('/stripe/status', 'StripeController@status');
// Get payment info
Route::get('/stripe/info', 'StripeController@paymentInfo');
// Withdraw
Route::post('/stripe/withdraw', 'StripeController@withdraw');
// Create connected account
Route::put('/stripe/connect', 'StripeController@update');
