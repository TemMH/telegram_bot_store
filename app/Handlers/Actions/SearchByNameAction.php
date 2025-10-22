<?php

//declare(strict_types=1);

namespace App\Handlers\Actions;

use App\Handlers\Commands\StartCommand;
use App\Keyboards\ReplyKeyboardBuilder;
use App\Services\StateService;
use Cocur\Slugify\Slugify;
use DefStudio\Telegraph\Models\TelegraphChat;
use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;
use App\Models\Part;

class SearchByNameAction
{
    public function execute(TelegraphChat $chat): void
    {
        app(StateService::class)->set($chat, 'waiting_for_model');

        $keyboard = app(ReplyKeyboardBuilder::class)->searchByNameKeyboard();

        $chat->markdown("Введите деталь, марку и/или модель авто:")
            ->replyKeyboard($keyboard)
            ->send();
    }


    public function search(string $text, TelegraphChat $chat): void
    {
        $text = mb_strtolower(trim($text));
        $transliteration = config('transliteration_lower');

        $transliterated = $text;

        foreach ($transliteration as $ru => $en) {
            if (str_contains($text, $ru)) {
                $transliterated = str_replace($ru, $en, $text);
                break;
            }
        }

        $slugify = new Slugify();
        $slugified = $slugify->slugify($transliterated, ' ');

        $matchesOriginal = Part::search($text)->get();
        $matchesTranslit = $slugified !== $text ? Part::search($slugified)->get() : collect();

        $matches = $matchesOriginal->merge($matchesTranslit)->unique('part_code')->values();


        $total = $matches->count();


        if ($matches->isEmpty()) {
            $chat->markdown("❌ Не удалось найти детали для *{$text}*")->send();
            return;
        }


        if ($total > 12) {
            $response = "Найдено много совпадений ({$total}) уточните запрос";
        } else {
            $response = "Найдено {$total} совпадений:\n\n";

            foreach ($matches as $part) {
                $cleanCode = preg_replace('/[^a-zA-Zа-яА-Я0-9]/u', '', $part->part_code);

                $response .= "<b>{$part->name}</b>\n";
                $response .= "Подходит для: {$part->applicability}\n";
                $response .= "Посмотреть — /part_{$cleanCode}\n\n";
            }
        }

        $chat->html($response)->send();
    }

}
