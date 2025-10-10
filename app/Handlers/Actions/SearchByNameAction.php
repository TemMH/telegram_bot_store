<?php

declare(strict_types=1);

namespace App\Handlers\Actions;

use App\Handlers\Commands\StartCommand;
use App\Keyboards\ReplyKeyboardBuilder;
use App\Services\StateService;
use DefStudio\Telegraph\Models\TelegraphChat;
use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;
use App\Models\Part;

class SearchByNameAction
{
    protected int $perPage = 4; // сколько деталей на страницу

    public function execute(TelegraphChat $chat): void
    {
        app(StateService::class)->set($chat, 'waiting_for_model');

        $keyboard = app(ReplyKeyboardBuilder::class)->searchByNameKeyboard();

        $chat->markdown("Введите модель автомобиля:")
            ->replyKeyboard($keyboard)
            ->send();
    }

    /**
     * Поиск и вывод результатов с пагинацией
     */
    public function search(string $text, TelegraphChat $chat, int $page = 1): void
    {
        $text = strtolower(trim($text));

        $matches = Part::query()
            ->whereRaw('LOWER(applicability) LIKE ?', ["%{$text}%"])
            ->get();

        if ($matches->isEmpty()) {
            $chat->markdown("❌ Не удалось найти детали для *{$text}*")->send();
            return;
        }

        $total = $matches->count();
        $pages = ceil($total / $this->perPage);


        $items = $matches->forPage($page, $this->perPage);

        $buttons = $items->map(fn($part) => Button::make($part->name)->action(""))->toArray();
        $keyboard = Keyboard::make()->buttons($buttons)->chunk(2);

        $detailButtons = $items->map(fn($part
        ) => Button::make($part->name)->action(""))->toArray(); //->action("read")->param('id', $notification->id)

        // Кнопки навигации
        $navButtons = [];
        if ($page > 1) {
            $prevPage = $page - 1;
            $navButtons[] = Button::make('⬅️ Назад')->action("");
        }
        if ($page < $pages) {
            $nextPage = $page + 1;
            $navButtons[] = Button::make('➡️ Далее')->action("");
        }

        $allButtons = array_merge($detailButtons, $navButtons);

        $keyboard = Keyboard::make()->buttons($allButtons)->chunk(2);

        $chat->message("Страница {$page}/{$pages}, всего найдено {$total} деталей:")->keyboard($keyboard)->send();
    }

}
