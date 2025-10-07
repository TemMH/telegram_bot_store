<?php

use Illuminate\Support\Facades\Route;
use DefStudio\Telegraph\Models\TelegraphChat;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/send-telegram', function () {
    $chat = TelegraphChat::where('chat_id', '2136005799')->first();
    $chat->message('Привет!')->send();
    return response()->json(['Сообщение успешно отправлено'], 200);
});


