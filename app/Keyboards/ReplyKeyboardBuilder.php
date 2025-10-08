<?php

namespace App\Keyboards;

use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\ReplyButton;
use DefStudio\Telegraph\Keyboard\ReplyKeyboard;

class ReplyKeyboardBuilder
{
    public function mainKeyboard(): ReplyKeyboard
    {

        return ReplyKeyboard::make()
            ->buttons([
                ReplyButton::make('Поиск по названию'),
                ReplyButton::make('Поиск по VIN'),
                ReplyButton::make('Мои заказы'),
                ReplyButton::make('Поддержка/Отзывы'),

            ])->chunk(2);

    }

    public function searchByNameKeyboard(): ReplyKeyboard
    {
            return ReplyKeyboard::make()->inputPlaceholder("Например:Масло Toyota...")->button("Отмена");
    }

    public function searchByVinKeyboard(): ReplyKeyboard
    {
            return ReplyKeyboard::make()->inputPlaceholder("Например:WVGZZZ00000000000...")->button("Отмена");
    }

}
