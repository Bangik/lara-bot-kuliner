<?php

namespace App\Telegram\Commands;

use Telegram\Bot\Commands\Command;
use Illuminate\Support\Facades\Http;

class DetailRestaurantCommand extends Command
{
  // get detail restorant from https://restaurant-api.dicoding.dev/detail/:id and display the name, city, address, categories, menu, rating, and customerReviews.
  protected string $name = 'detail_{id}';
  protected string $pattern = '{id}';
  protected string $description = 'Detail Restaurant Command to get you started';

  public function handle()
  {
    $id = $this->argument('id');
    $response = Http::get('https://restaurant-api.dicoding.dev/detail/' . $id);
    $result = json_decode($response->getBody()->getContents(), true);
    $restaurant = $result['restaurant'];
    $text = "Detail Restaurant : \n";
    $text .= "Name : " . $restaurant['name'] . "\n";
    $text .= "City : " . $restaurant['city'] . "\n";
    $text .= "Address : " . $restaurant['address'] . "\n";
    $text .= "Categories : \n";
    foreach ($restaurant['categories'] as $category) {
      $text .= $category['name'] . "\n";
    }
    $text .= "Menu : \n";
    $text .= "Foods : \n";
    foreach ($restaurant['menus']['foods'] as $food) {
      $text .= $food['name'] . "\n";
    }
    $text .= "Drinks : \n";
    foreach ($restaurant['menus']['drinks'] as $drink) {
      $text .= $drink['name'] . "\n";
    }
    $text .= "Rating : " . $restaurant['rating'] . "\n";
    $text .= "Customer Reviews : \n";
    foreach ($restaurant['customerReviews'] as $customerReview) {
      $text .= $customerReview['name'] . " : " . $customerReview['review'] . "\n";
    }
    $this->replyWithMessage([
      'text' => $text
    ]);
  }
}