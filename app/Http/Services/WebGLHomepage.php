<?php

namespace App\Http\Services;

use FFMpeg\FFProbe;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Config;

class WebGLHomepage implements WebGLHomepageInterface
{


    protected array $included = [];
    protected array $data = [];

    public function getHomepageData(): \stdClass
    {
        $base_url = Config::get('app.api_base_endpoint');
        $client = $this-> getAuthenticatedClient();
        $componentInclude = "field_components";

        $componentSlidesInclude           = $componentInclude . ".field_webgl_slide";
        $componentSlidesAlbum             = $componentSlidesInclude.".field_album";
        $componentSlidesMediaInclude      = $componentSlidesInclude.".field_media_file";
        $componentSlidesMediaImageInclude = $componentSlidesMediaInclude.".field_media_image";
        $componentSlidesMediaVideoInclude = $componentSlidesMediaInclude.".field_media_video_file";

        $includes = "include=$componentInclude,$componentSlidesInclude,$componentSlidesMediaInclude,
        $componentSlidesMediaImageInclude,$componentSlidesMediaVideoInclude,$componentSlidesAlbum";

        $response = $client->get($base_url. "/node/homepage?$includes");
        $response =  json_decode($response->getBody()->getContents(), true);
        return $this->transformHomepage($response);
    }


    public function transformHomepage($data): \stdClass
    {
        $this->included = $data['included'];
        $this->data = $data['data'][0];

        $homepage = new \stdClass();
        $homepage->title = $data['data'][0]['attributes']['title'];
        $homepage->components = $this->processComponents();
        return $homepage;
    }

    public function getAuthenticatedClient(): Client
    {
        $headers = [
            'Authorization' => 'Basic ' . base64_encode(Config::get('app.api_username') . ':' . Config::get('app.api_password')),
            'Content-Type'  => 'application/json',
            'Accept'        => 'application/json',
        ];

        return new Client(['headers' => $headers]);
    }

    private function processComponents(): array
    {
        $components = [];
        foreach ($this->included as $component) {
            if($component['type'] === 'paragraph--webgl_carousel_gallery'){
                $new_component         = new \stdClass();
                $slidesData            = $component['relationships']['field_webgl_slide']['data'];
                $new_component->slides = $this->processCarouselSlides($slidesData);
                $components[]          = $new_component;
                $new_component->type   = $component['type'];
                $new_component->id     = $component['id'];
            }
        }
        return $components;
    }

    private function processCarouselSlides($data): array
    {
        $slides = [];
        foreach ($data as $slide) {
            if($slide['type'] === "paragraph--webgl_carousel_slide"){
                $slideData = $this->getSlideData($slide['id']);
                $slides[] = $slideData;
            }

        }
        return $slides;
    }

    private function getSlideData($id)
    {
        $slideData = new \stdClass();
        foreach ($this->included as $pSlide) {
            if($pSlide['id'] === $id){
                $slideData->title      = $pSlide['attributes']['field_title'];
                $slideData->order      = $pSlide['attributes']['field_order'];
                $slideData->resolution = $this->cleanDimensions($pSlide['attributes']['field_media_resolution']);
                $slideData->media      = $this->getSlideMedia($pSlide['relationships']['field_media_file']['data']['id']);
                $slideData->album      = $this->getAlbum();
                return $slideData;
            }
        }
    }

    private function getAlbum(): \stdClass
    {
        $nb = new \stdClass();
        foreach ($this->included as $album) {

            if($album['type'] === 'node--album'){
                $nb->id   = $album['id'];
                $nb->name = $album['attributes']['title'];
                $nb->description = $album['attributes']['field_description'];
                $nb->url = $album['attributes']['path']['alias'] . "/".$album['id'];
                return $nb;
            }
        }
        return $nb;
    }

    private function getSlideMedia(mixed $id)
    {
        foreach ($this->included as $media) {
            if($media['id'] === $id){
                $mediaData = new \stdClass();
                $relationships = $media['relationships'];

                if(isset($relationships['field_media_image'])){
                    $meta = $relationships['field_media_image']['data']['meta'];
                    $mediaData->fileId  = $media['relationships']['field_media_image']['data']['id'];
                    $mediaData->alt     = $meta['alt'];
                    $mediaData->title   = $meta['title'];
                    $mediaData->width   = $meta['width'];
                    $mediaData->height  = $meta['height'];
                    $mediaData->type    = 'image';

                } else if (isset($relationships['field_media_video_file'])){
                    $mediaData->fileId  = $media['relationships']['field_media_video_file']['data']['id'];
                    $mediaData->type    = 'video';
                }

                $mediaData->url  = $this->getSlideMediaFileUrl($mediaData->fileId);
                return $mediaData;
            }
        }
    }

    private function getSlideMediaFileUrl(mixed $fileId)
    {
        foreach ($this->included as $mediaFile) {
            if($mediaFile['id'] === $fileId && $mediaFile['type'] === 'file--file'){
                return '/cms-files/web'.$mediaFile['attributes']['uri']['url'];
            }
        }

    }

   public function cleanDimensions($input)
   {
       // Remove unwanted Unicode characters and spaces
       $cleaned = preg_replace('/[^\d×]/u', '', $input);

       // Ensure the multiplication sign is in the right place
       return preg_replace('/(\d+)×(\d+)/', '$1×$2', $cleaned);
   }

}
