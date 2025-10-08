<?php

namespace App\Handlers\Actions;

use App\Keyboards\ReplyKeyboardBuilder;
use DefStudio\Telegraph\Models\TelegraphChat;

class SearchByNameAction
{
    public function execute(TelegraphChat $chat): void
    {
        $keyboard = app(ReplyKeyboardBuilder::class)->searchByNameKeyboard();

        $text = "Введите название детали";
        $chat->markdown($text)->replyKeyboard($keyboard)->send();
}
}
