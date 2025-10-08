<?php

namespace App\Handlers\Actions;

use DefStudio\Telegraph\Models\TelegraphChat;

class OrderAction
{
    public function execute(TelegraphChat $chat): void
    {
        $text = "Catalog text";
        $chat->markdown($text)->send();
    }
}
