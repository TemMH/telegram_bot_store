<?php

declare(strict_types=1);

namespace App\Handlers\Actions;

use App\Handlers\Commands\StartCommand;
use App\Keyboards\ReplyKeyboardBuilder;
use App\Models\Vin;
use App\Services\StateService;
use DefStudio\Telegraph\Models\TelegraphChat;
use NunoMaduro\Collision\Adapters\Phpunit\State;

class SearchByVinAction
{
    public function execute(TelegraphChat $chat): void
    {
        app(StateService::class)->set($chat, 'waiting_for_vin');

        $keyboard = app(ReplyKeyboardBuilder::class)->searchByVinKeyboard();

        $text = "Введите VIN-номер (17-символов)";
        $chat->markdown($text)->replyKeyboard($keyboard)->send();
    }


    public function search(string $vin, TelegraphChat $chat):void
    {
        $vin = strtoupper(trim($vin));


        if (strlen($vin) !== 17){
            $chat->markdown('Vin должен состоять из 17 символов.')->send();
            return;
        }

        $wmi = substr($vin, 0, 3);
        $wds = substr($vin, 3, 6);

        $match = Vin::query()
            ->where('wmi', $wmi)
            ->where('vds', $wds)
            ->first();


        if ($match) {
            $chat->markdown(
                "Найдено: \n" .
                "Марка: {$match->make} \n" .
                "Модель: {$match->model} \n" .
                "Год: {$match->year}"
            )->send();
        } else {
            $chat->markdown("Не удалось определить модель по VIN.")->send();
        }

        app(StateService::class)->clear($chat);
        app(StartCommand::class)->execute($chat);
    }
}
