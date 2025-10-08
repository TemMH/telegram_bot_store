<?php

namespace App\Handlers\Actions;

use App\Keyboards\ReplyKeyboardBuilder;
use DefStudio\Telegraph\Models\TelegraphChat;

class SearchByVinAction
{
    public function execute(TelegraphChat $chat): void
    {
        $keyboard = app(ReplyKeyboardBuilder::class)->searchByVinKeyboard();

        $text = "Введите VIN-номер";
        $chat->markdown($text)->replyKeyboard($keyboard)->send();
    }
}
