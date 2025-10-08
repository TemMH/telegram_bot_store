<?php

namespace App\Handlers\Commands;


use App\Keyboards\ReplyKeyboardBuilder;
use DefStudio\Telegraph\Enums\ChatActions;
use DefStudio\Telegraph\Models\TelegraphChat;


class StartCommand
{

    public function execute(TelegraphChat $chat): void
    {
        $keyboard = app(ReplyKeyboardBuilder::class)->mainKeyboard();

        $chat->markdown("Выберите действие")
            ->replyKeyboard($keyboard)
            ->send();
    }
}
