<?php

namespace App\Http\Services;

use App\Http\Services\AlbumInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Config;

class Album implements AlbumInterface
{

    public static function getAlbums()
    {

        $client = (new Album)->getAuthenticatedClient();
        $base_url = Config::get('app.api_base_endpoint');
        $response = $client->get($base_url. '/node/album');
        $response =  json_decode($response->getBody()->getContents(), true);
        return $response['data'];
    }

    public function getAlbum($id)
    {
        // TODO: Implement getAlbum() method.
    }

    public function getAlbumMedia($id)
    {
        // TODO: Implement getAlbumMedia() method.
    }

    public static function generateAlbumMenu()
    {
        // TODO: Implement generateAlbumMenu() method.
        $menuLinks = [];

        //menu links
        $albums = self::getAlbums();

        foreach ($albums as $album) {
            $album = (object)$album;
            $menuLinks[] = [
                'title' => $album->attributes['title'],
                'url' => $album->attributes['path']['alias'],
                'id' => $album->id
            ];
        }

      return $menuLinks;
    }

    public function getAuthenticatedClient(): Client
    {

        $headers = [
            'Authorization' => 'Basic ' . base64_encode(Config::get('app.api_username') . ':' . Config::get('app.api_password')),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];

        return new Client(['headers' => $headers]);
    }
}
