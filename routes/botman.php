<?php
/*
 * Chatbot
 */
$botman = resolve('botman');
/*
$botman->hears('{message}', function ($bot, $message) {
    //$bot->reply('Hello! I am Thuan Bui');
    dd($message);
});
*/

$botman->hears('{message}', \Modules\Chat\Http\Controllers\ChatBotmanController::class.'@chatBot');
