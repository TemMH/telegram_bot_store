<?php

namespace App\Handlers;

use App\Handlers\Actions\ChoosePartAction;
use App\Handlers\Actions\OrderAction;
use App\Handlers\Actions\SearchByOriginalCodeAction;
use App\Handlers\Commands\StartCommand;
use App\Models\Part;
use App\Services\StateService;
use DefStudio\Telegraph\Facades\Telegraph;
use DefStudio\Telegraph\Handlers\WebhookHandler;
use DefStudio\Telegraph\Models\TelegraphChat;
use Illuminate\Support\Facades\Log;
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

//    public function forwardPart(?int $message_id): void
//    {
//        $chat = $this->chat;
//        if (!$chat->chat_id) {
//            \Log::error("forwardPart: chat_id is null, cannot forward");
//            return;
//        }
//
//        if (!$message_id) {
//            \Log::error("forwardPart: message_id is null, cannot forward");
//            $chat->markdown("❌ Ошибка: ID сообщения не найден")->send();
//            return;
//        }
//
//        $adminChatId = 8422144169;
//        $chat->forwardMessage($adminChatId, $message_id);
//        $chat->markdown("✅ Сообщение переслано админу")->send();
//    }


    public function start(): void
    {
        app(StartCommand::class)->execute($this->chat);
    }
}
