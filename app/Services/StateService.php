<?php

namespace App\Services;

use DefStudio\Telegraph\Models\TelegraphChat;
use Illuminate\Support\Facades\Cache;

class StateService
{
    protected $prefix = 'chat_state';

    protected int $ttl = 3600;


    public function set(TelegraphChat $chat, string $state):void
    {
        Cache::put($this->prefix . $chat->id, $state, $this->ttl);
    }

    public function get(TelegraphChat $chat): ?string
    {
        return Cache::get($this->prefix . $chat->id);
    }


    public function clear(TelegraphChat $chat): void
    {
        Cache::forget($this->prefix . $chat->id);
    }
}
