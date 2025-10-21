<?php

declare(strict_types=1);

namespace App\Handlers\Actions;

use App\Models\Part;
use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;
use DefStudio\Telegraph\Models\TelegraphChat;
use Illuminate\Support\Facades\Log;

class ChoosePartAction
{
    public function execute(TelegraphChat $chat, string $partCode): void
    {
        $part = Part::query()->where('part_code', $partCode)->first();

        if (!$part) {
            $chat->markdown("Деталь с кодом *{$partCode}* не найдена")->send();
            return;
        }

        // Вспомогательная функция для Google Drive ссылок
        function driveLinkToThumbnail(string $link): string
        {
            if (preg_match('#/d/([a-zA-Z0-9_-]+)#', $link, $matches)) {
                return "https://drive.google.com/thumbnail?id={$matches[1]}";
            }
            return $link;
        }

        $media = [];

        if (!empty($part->path_to_photo1)) {
            $media[] = ['type' => 'photo', 'media' => driveLinkToThumbnail($part->path_to_photo1), 'caption' => $this->buildCaption($part), 'parse_mode' => 'HTML'];
        }
        if (!empty($part->path_to_photo2)) {
            $media[] = ['type' => 'photo', 'media' => driveLinkToThumbnail($part->path_to_photo2)];
        }
        if (!empty($part->path_to_photo3)) {
            $media[] = ['type' => 'photo', 'media' => driveLinkToThumbnail($part->path_to_photo3)];
        }

        if (count($media) > 0) {
        // Отправляем мультимедиа (или одно фото)
            if (count($media) > 1) {
                $chat->mediaGroup($media)->send();
            } else {
                $chat->photo($media[0]['media'])
                    ->html($this->buildCaption($part))
                    ->send();
            }
        } else {
            // Если фото нет, отправляем только текст
            $chat->html($this->buildCaption($part))->send();
        }

//        $chatId = $chat->chat_id;
//        $keyboard = Keyboard::make()
//            ->buttons([
//                Button::make('📤 Переслать админу')
//                    ->action('forwardPart')
//                    ->param('message_id', $messageId)
//                    ->param('chat_id', $chatId),
//            ]);
//
//        $chat->message('Действия с деталью:')->keyboard($keyboard)->send();




    }

// Вспомогательный метод для генерации текста сообщения
    protected function buildCaption(Part $part): string
    {
        return "🆔Код товара:\n{$part->part_code}\n\n"
            . "🛞Наименование\n<b>{$part->name}</b>\n\n"
            . "🛠️Производитель\n{$part->factory} {$part->country}\n\n"
            . "#️⃣Оригинальный код\n<code>{$part->original_code}</code>\n\n"
            . "🚗Подходит для\n<i>{$part->applicability}</i>\n\n"
            . (!empty($part->price) ? "Цена\n{$part->price} ₽\n\n" : '')
            . "<blockquote>📷 Все фотографии собственные и соответствуют описанию товара
🚚 Доставка по всей России в течении 7-ми дней
🏠 Самовывоз - город Москва ст м.Нагатинская
❔ Оперативно ответим на любые возникшие вопросы</blockquote>\n\n"
            ."<a href='https://t.me/YuryShirko'>Для покупки и продробностей нажмите сюда</a>";
    }

}
