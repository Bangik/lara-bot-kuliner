<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TelegramBotController extends Controller
{
    public function sendStartMessage($chatId)
    {
        $text = "Halo, selamat datang di bot restoran. \n";
        $text .= "Ketik /help untuk melihat daftar perintah yang tersedia";

        $response = Http::post(env('TELEGRAM_API_URL') . '/sendMessage', [
            'chat_id' => $chatId,
            'text' => $text
        ]);

        return $response->json();
    }

    public function sendHelpMessage($chatId)
    {
        $text = "Daftar perintah yang tersedia : \n";
        $text .= "/start - Untuk memulai bot \n";
        $text .= "/help - Untuk melihat daftar perintah \n";
        $text .= "/list - Untuk melihat daftar restoran \n";
        $text .= "/detail nama_restoran - Untuk melihat detail restoran \n";

        $response = Http::post(env('TELEGRAM_API_URL') . '/sendMessage', [
            'chat_id' => $chatId,
            'text' => $text
        ]);

        return $response->json();
    }

    public function sendListRestaurant($chatId)
    {
        $response = Http::get('https://restaurant-api.dicoding.dev/list');
        $result = json_decode($response->getBody()->getContents(), true);
        $restaurants = $result['restaurants'];
        $text = "List Restaurant : \n";
        foreach ($restaurants as $restaurant) {
            $text .= $restaurant['name'] . " " . $restaurant['city'] . "\n";
        }
        $text .= "\n Pilih salah satu restoran di atas untuk melihat detailnya ya!";
        $keyboard = [];
        foreach ($restaurants as $restaurant) {
            $keyboard[] = ["/detail ".$restaurant['name']];
        }

        $reply_markup = [
            'keyboard' => $keyboard,
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ];

        $response = Http::post(env('TELEGRAM_API_URL') . '/sendMessage', [
            'chat_id' => $chatId,
            'text' => $text,
            'reply_markup' => json_encode($reply_markup)
        ]);

        return $response->json();
    }

    public function sendDetailRestaurant($chatId, $text)
    {
        if (strpos($text, ' ') === false) {
            $text = "Silahkan ketik /detail nama_restoran";
            $response = Http::post(env('TELEGRAM_API_URL') . '/sendMessage', [
                'chat_id' => $chatId,
                'text' => $text
            ]);
            return $response->json();
        }

        $restaurantName = explode(' ', $text)[1];
        $response = Http::get('https://restaurant-api.dicoding.dev/search?q=' . $restaurantName);
        $result = json_decode($response->getBody()->getContents(), true);
        if($result['founded'] == 0) {
            $text = "Restoran tidak ditemukan";
            $response = Http::post(env('TELEGRAM_API_URL') . '/sendMessage', [
                'chat_id' => $chatId,
                'text' => $text
            ]);
            return $response->json();
        }
        $restaurantId = $result['restaurants'][0]['id'];

        $response = Http::get('https://restaurant-api.dicoding.dev/detail/' . $restaurantId);
        $result = json_decode($response->getBody()->getContents(), true);
        $restaurant = $result['restaurant'];
        $text = "Detail Restaurant : \n";
        $text .= "Nama : " . $restaurant['name'] . "\n";
        $text .= "Kota : " . $restaurant['city'] . "\n";
        $text .= "Alamat : " . $restaurant['address'] . "\n";
        $text .= "\n";
        $text .= "Kategory : \n";
        foreach ($restaurant['categories'] as $category) {
            $text .= $category['name'] . "\n";
        }
        $text .= "\n";
        $text .= "Menu : \n";
        $text .= "Makan : \n";
        foreach ($restaurant['menus']['foods'] as $food) {
            $text .= $food['name'] . "\n";
        }
        $text .= "\n";
        $text .= "Minum : \n";
        foreach ($restaurant['menus']['drinks'] as $drink) {
            $text .= $drink['name'] . "\n";
        }
        $text .= "\n";
        $text .= "Rating : " . $restaurant['rating'] . "\n";
        $text .= "\n";
        $text .= "Review Customer : \n";
        foreach ($restaurant['customerReviews'] as $customerReview) {
            $text .= $customerReview['name'] . " : " . $customerReview['review'] . "\n";
        }
        
        $response = Http::post(env('TELEGRAM_API_URL') . '/sendMessage', [
            'chat_id' => $chatId,
            'text' => $text
        ]);

        return $response->json();
    }

    public function sendDefaultMessage($chatId)
    {
        $text = "Maaf, perintah tidak ditemukan. \n";
        $text .= "Ketik /help untuk melihat daftar perintah yang tersedia";

        $response = Http::post(env('TELEGRAM_API_URL') . '/sendMessage', [
            'chat_id' => $chatId,
            'text' => $text
        ]);

        return $response->json();
    }

    public function webhook(Request $request)
    {
        $chatId = $request->message['chat']['id'];
        $text = $request->message['text'];

        if ($text == '/start') {
            $this->sendStartMessage($chatId);
        } elseif ($text == '/help') {
            $this->sendHelpMessage($chatId);
        } elseif ($text == '/list') {
            $this->sendListRestaurant($chatId);
        } elseif (strpos($text, '/detail') !== false) {
            $this->sendDetailRestaurant($chatId, $text);
        } else {
            $this->sendDefaultMessage($chatId);
        }
    }
}
