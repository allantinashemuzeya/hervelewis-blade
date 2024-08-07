<?php

namespace App\Http\Services;

use GuzzleHttp\Client;

interface AlbumInterface
{
    public function getAlbums();
    public function getAlbum($id);

    public function getAlbumMedia($id);

    public function generateAlbumMenu();

    public function getAuthenticatedClient(): Client;

}
