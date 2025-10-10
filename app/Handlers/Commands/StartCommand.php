<?php

namespace App\Handlers\Commands;


use App\Keyboards\ReplyKeyboardBuilder;
use App\Services\StateService;
use DefStudio\Telegraph\Enums\ChatActions;
use DefStudio\Telegraph\Models\TelegraphChat;


class StartCommand
{

    public function execute(TelegraphChat $chat): void
    {
        app(StateService::class)->clear($chat);

        $keyboard = app(ReplyKeyboardBuilder::class)->mainKeyboard();

        $chat->markdown("Выберите действие")
            ->replyKeyboard($keyboard)
            ->send();
    }
}
