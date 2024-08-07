<?php

namespace App\Http\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Config;
use stdClass;

class Album implements AlbumInterface
{

    protected static mixed $included;
    protected static mixed $id;
    protected static mixed $data;

    public function getAlbums()
    {
        $client = $this->getAuthenticatedClient();
        $base_url = Config::get('app.api_base_endpoint');
        $response = $client->get($base_url . '/node/album');
        $response = json_decode($response->getBody()->getContents(), true);

        return $response['data'];
    }

    public function getAlbum($id)
    {
        $includes = ['field_media.field_media_image', 'field_media.field_media_video_file', 'field_background_cover.field_media_image'];
        $client = $this->getAuthenticatedClient();
        $base_url = Config::get('app.api_base_endpoint');
        $response = $client->get($base_url . '/node/album/' . $id . "?include=" . implode(',', $includes));
        $response = json_decode($response->getBody()->getContents(), true);

        self::$id = $response['data']['id'];
        self::$data = $response['data'];

        if (isset($response['included'])) {
            self::$included = $response['included'];
        }

        $response['data']['attributes']['media'] = $this->getAlbumMedia($id);
        $response['data']['attributes']['cover'] = $this->getAlbumCoverImage();
        $response['data']['attributes']['nextAlbum'] = $this->getNextAlbum();
        return $response['data'];
    }

    public function getAlbumMedia($id): array
    {
        $media = [];
        if ($id == self::$id) {
            foreach (self::$included as $item) {
                if ($item['type'] == 'file--file') {
                    $file = new stdClass();
                    $file->id = $item['id'];
                    $file->type = $item['attributes']['filemime'];
                    $file->url = '/cms-files/web' . $item['attributes']['uri']['url'];
                    $file->meta = $this->getFileMetaData($item['id']);
                    $media[] = $file;
                }
            }
        }
        return $media;
    }

    public function getFileMetaData($id)
    {
        foreach (self::$included as $item) {
            if ($item['type'] == 'media--image') {
                $mediaFileId = $item['relationships']['field_media_image']['data']['id'];
                if ($mediaFileId === $id) {
                    return $item['relationships']['field_media_image']['data']['meta'];
                }
            }
        }
        return null;
    }

    public function generateAlbumMenu(): array
    {
        $menuLinks = [];

        //menu links
        $albums = $this->getAlbums();

        foreach ($albums as $album) {
            $album = (object)$album;
            $menuLinks[] = ['title' => $album->attributes['title'], 'url' => $album->attributes['path']['alias'], 'id' => $album->id];
        }

        return $menuLinks;
    }

    public function getAuthenticatedClient(): Client
    {

        $headers = ['Authorization' => 'Basic ' . base64_encode(Config::get('app.api_username') . ':' . Config::get('app.api_password')), 'Content-Type' => 'application/json', 'Accept' => 'application/json',];

        return new Client(['headers' => $headers]);
    }

    private function getAlbumCoverImage()
    {
        $mediaData = self::$data['relationships']['field_background_cover']['data'];

        if (!isset($mediaData)) {

            return null;
        }

        $mediaId = self::$data['relationships']['field_background_cover']['data']['id'];

        $coverImage = new stdClass();
        $coverImage->fileId = 0;

        foreach (self::$included as $item) {
            if ($item['id'] === $mediaId) {
                $coverImage->fileId = $item['relationships']['field_media_image']['data']['id'];
                $coverImage->meta = $item['relationships']['field_media_image']['data']['meta'];
            }

            if ($item['type'] == 'file--file' && $item['id'] === $coverImage->fileId) {
                $coverImage->url = '/cms-files/web' . $item['attributes']['uri']['url'];
            }
        }

        return $coverImage;
    }

    public function getRandomAlbum($except){

        $albums = $this->getAlbums();
        $randomAlbum = $albums[array_rand($albums)];
        if($randomAlbum['id'] !== $except){
            return $this->getRandomAlbum($except);
        }
        return $randomAlbum;
    }

    private function getNextAlbum()
    {
        $albums = $this->getAlbums();

        if(count($albums) > 1){
            $nextAlbum = $albums[array_rand($albums)];
            if($nextAlbum['id'] == self::$id){
                return $this->getNextAlbum();
            }
        }
        return $albums[array_rand($albums)];
    }
}
