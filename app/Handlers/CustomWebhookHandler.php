<?php

namespace App\Handlers;

use Illuminate\Support\Stringable;
use DefStudio\Telegraph\Handlers\WebhookHandler;

class CustomWebhookHandler extends WebhookHandler
{
    protected function handleChatMessage(Stringable $text): void
    {
        // полученное сообщение отправляется обратно в чат
        $this->chat->html("Вы написали: $text")->send();
    }
}
