<?php

declare(strict_types=1);

namespace App\Handlers\Actions;

use App\Models\Part;
use DefStudio\Telegraph\Models\TelegraphChat;

class ChoosePartAction
{
    public function execute(TelegraphChat $chat, string $partCode): void
    {
        $part = Part::query()->where('part_code', $partCode)->first();

        if (!$part) {
            $chat->markdown("Деталь с кодом *{$partCode}* не найдена")->send();
            return;
        }




        function driveLinkToThumbnail(string $link): string
        {
            if (preg_match('#/d/([a-zA-Z0-9_-]+)#', $link, $matches)) {
                $fileId = $matches[1];
                return "https://drive.google.com/thumbnail?id={$fileId}";
            }
            return $link;
        }

        $originalLink1 = $part->path_to_photo1;
        $thumbnailLink1 = driveLinkToThumbnail($originalLink1);

        $response =
              "🆔Код товара:\n{$part->part_code}\n\n"

            . "🛞Наименование\n<b>{$part->name}</b>\n\n"

            . "🛠️Производитель\n{$part->factory} {$part->country}\n\n"

            . "#️⃣Оригинальный код\n<code>{$part->original_code}</code>\n\n"

            . "🚗Подходит для\n<i>{$part->applicability}</i>\n\n"

            . "<blockquote>📷 все фотографии собственные и соответствуют описанию товара
🚚 доставка по всей России любой транспортной компанией
🏠 самовывоз осуществляется со склада м.Нагорная
❔оперативно ответим на любые возникшие вопросы</blockquote>";

        $media = [
            ['type' => 'photo', 'media' => $thumbnailLink1, 'caption' => $response, 'parse_mode' => 'HTML'],
        ];

        if (!empty($part->path_to_photo2)) {
            $thumbnailLink2 = driveLinkToThumbnail($part->path_to_photo2);
            $media[] = ['type' => 'photo', 'media' => $thumbnailLink2];
        }

        if (!empty($part->path_to_photo3)) {
            $thumbnailLink3 = driveLinkToThumbnail($part->path_to_photo3);
            $media[] = ['type' => 'photo', 'media' => $thumbnailLink3];
        }

        if (count($media) > 1) {
            $chat->mediaGroup($media)->send();
        } else {
            $chat->photo($thumbnailLink1)->html($response)->send();
        }
    }



}
