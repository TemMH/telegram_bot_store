<?php

namespace App\Handlers\Actions;

use DefStudio\Telegraph\Models\TelegraphChat;

class SupportAction
{
    public function execute(TelegraphChat $chat): void
    {
        $text = "Свои вопросы/фидбек о боте можно оставить в [этом чате](https://t.me/Auto_Drop_MSK)";
        $chat->markdown($text)->send();
    }
}
