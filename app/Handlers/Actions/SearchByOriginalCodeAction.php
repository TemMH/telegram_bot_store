<?php

declare(strict_types=1);

namespace App\Handlers\Actions;

use App\Handlers\Commands\StartCommand;
use App\Keyboards\ReplyKeyboardBuilder;
use App\Models\Part;
use App\Services\StateService;
use DefStudio\Telegraph\Models\TelegraphChat;

class SearchByOriginalCodeAction
{
    public function execute(TelegraphChat $chat): void
    {
        app(StateService::class)->set($chat, 'waiting_for_code');

        $keyboard = app(ReplyKeyboardBuilder::class)->searchByCodeKeyboard();

        $text = "Введите оригинальный код";
        $chat->markdown($text)->replyKeyboard($keyboard)->send();
    }


    public function search(string $text, TelegraphChat $chat): void
    {
        $text = strtoupper(trim($text));

        $match = Part::query()
            ->where('original_code', 'like', "%{$text}%")
            ->get();

        if (!$match || $match->isEmpty()) {
            $chat->markdown("Не удалось найти детали по коду *{$text}*")->send();
            return;
        }

        $response = "Найдено ".$match->count()." совпадений:\n\n";


        foreach ($match as $part) {
            $cleanCode = preg_replace('/[^a-zA-Zа-яА-Я0-9]/u', '', $part->part_code);

            $response .= "<b>{$part->name}</b>\n";
            $response .= "Подходит для: {$part->applicability}\n";
            $response .= "Посмотреть — /part_{$cleanCode}\n\n";
        }

        $chat->html($response)->send();

//        app(StateService::class)->clear($chat);
//        app(ChoosePartAction::class)->execute($chat);
    }

}
