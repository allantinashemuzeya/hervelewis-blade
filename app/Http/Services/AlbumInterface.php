<?php

namespace App\Http\Services;

use GuzzleHttp\Client;

interface AlbumInterface
{
    public static function getAlbums();
    public function getAlbum($id);

    public function getAlbumMedia($id);

    public static function generateAlbumMenu();

    public function getAuthenticatedClient(): Client;

}
