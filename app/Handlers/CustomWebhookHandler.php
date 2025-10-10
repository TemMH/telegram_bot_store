<?php

namespace App\Handlers;

use App\Handlers\Actions\ChoosePartAction;
use App\Handlers\Actions\OrderAction;
use App\Handlers\Actions\SearchByOriginalCodeAction;
use App\Handlers\Commands\ChooseCommand;
use App\Handlers\Commands\StartCommand;
use App\Services\StateService;
use DefStudio\Telegraph\Handlers\WebhookHandler;
use Illuminate\Support\Stringable;
use App\Handlers\Actions\SupportAction;
use App\Handlers\Actions\SearchByNameAction;
use App\Handlers\Actions\SearchByVinAction;
use App\Keyboards\ReplyKeyboardBuilder;

class CustomWebhookHandler extends WebhookHandler
{
    protected function handleUnknownCommand(Stringable $text): void
    {
        $textStr = (string) $text;
        if (str_starts_with(strtolower($textStr), '/part_')) {
            $partCode = substr($textStr, 6);
            app(ChoosePartAction::class)->execute($this->chat, $partCode);
            return;
        }
        parent::handleUnknownCommand($text);
    }
    protected function handleChatMessage(Stringable $text): void
    {
        $stateService = app(StateService::class);
        $state = $stateService->get($this->chat);

        switch ($text->lower()) {
            case 'поиск по названию':
                app(SearchByNameAction::class)->execute($this->chat);
                break;
            case 'поиск по оригинальному коду':
                app(SearchByOriginalCodeAction::class)->execute($this->chat);
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
                if ($state === 'waiting_for_code') {
                    app(SearchByOriginalCodeAction::class)->search($text, $this->chat);
                    return;
                } elseif ($state === 'waiting_for_model') {
                    app(SearchByNameAction::class)->search($text, $this->chat);
                    return;
                }
                $this->chat->markdown("Вы написали: $text")->send();
                break;
        }
    }



    public function start(): void
    {
        app(StartCommand::class)->execute($this->chat);
    }
}
