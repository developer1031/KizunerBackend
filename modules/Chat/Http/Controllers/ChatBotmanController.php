<?php

namespace Modules\Chat\Http\Controllers;

use BotMan\BotMan\BotMan;

class ChatBotmanController
{
    public function chatBot(BotMan $bot, $message)
    {
        $bot->reply('Hello!', ['chatbot' => true]);
    }
}
