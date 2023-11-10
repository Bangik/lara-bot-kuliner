<?php

namespace App\Telegram\Commands;

use Telegram\Bot\Commands\Command;
use Illuminate\Support\Facades\Http;

class ListRestaurantCommand extends Command
{
    protected string $name = 'list';
    protected string $description = 'List Restaurant Command to get you started';

    public function handle()
    {
        $response = Http::get('https://restaurant-api.dicoding.dev/list');
        $result = json_decode($response->getBody()->getContents(), true);
        $restaurants = $result['restaurants'];
        $text = "List Restaurant : \n";
        foreach ($restaurants as $restaurant) {
            $text .= $restaurant['name'] . "/detail_" . $restaurant['id'] . "\n";
        }
        $text .= "\n Pilih salah satu restoran di atas untuk melihat detailnya ya!";
        $this->replyWithMessage([
            'text' => $text
        ]);
    }
}