<?php

namespace App\Handlers;

use App\Handlers\Actions\OrderAction;
use App\Handlers\Commands\StartCommand;
use DefStudio\Telegraph\Handlers\WebhookHandler;
use Illuminate\Support\Stringable;
use App\Handlers\Actions\SupportAction;
use App\Handlers\Actions\SearchByNameAction;
use App\Handlers\Actions\SearchByVinAction;
use App\Keyboards\ReplyKeyboardBuilder;

class CustomWebhookHandler extends WebhookHandler
{
    protected function handleChatMessage(Stringable $text): void
    {
        // Обрабатываем нажатия кнопок reply keyboard
        switch ($text->lower()) {
            case 'поиск по названию':
                app(SearchByNameAction::class)->execute($this->chat);
                break;
            case 'поиск по vin':
                app(SearchByVinAction::class)->execute($this->chat);
                break;
            case 'мои заказы':
                app(OrderAction::class)->execute($this->chat);
                break;
            case 'поддержка/отзывы':
                app(SupportAction::class)->execute($this->chat);
                break;
            case 'отмена':
                app(StartCommand::class)->execute($this->chat);
                break;
            default:
                $this->chat->markdown("Вы написали: $text")->send();
        }
    }

    public function start(): void
    {
        app(StartCommand::class)->execute($this->chat);
    }

}
