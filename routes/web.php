<?php

use App\Http\Controllers\AlbumController;
use App\Http\Controllers\ConfigController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::get('/get-menu/', [ConfigController::class, 'getMenu']);

// Generate routes
$albumMenuItems  = ConfigController::getMenu();
foreach ($albumMenuItems as $albumMenuItem){
    $name = str_replace(' ', '-', $albumMenuItem['title']);
    $url = $albumMenuItem['url'];
    $id =  $albumMenuItem['id'];
   Route::get($url."/{id}", [AlbumController::class, 'show'])->name($name);
}
