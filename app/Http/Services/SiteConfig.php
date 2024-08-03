<?php

namespace App\Http\Services;

use App\Http\Services\SiteConfigInterface;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Config;

class SiteConfig implements SiteConfigInterface
{

    public function getSiteConfig(): array
    {
        $base_url = Config::get('app.api_base_endpoint');
        $client = $this->getAuthenticatedClient();
        $includes = "include=field_anita_social_media_links";
        $response = $client->get($base_url. "/node/site_config?$includes");
        $response =  json_decode($response->getBody()->getContents(), true);
        return $this->transformConfig($response);
    }

    public function getSiteConfigByKey(string $key): string
    {
        // TODO: Implement getSiteConfigByKey() method.
    }

    public function updateSiteConfig(array $data): bool
    {
        // TODO: Implement updateSiteConfig() method.
    }

    public function getAuthenticatedClient(): Client
    {
        // TODO: Implement getAuthenticatedClient() method.


        $headers = [
            'Authorization' => 'Basic ' . base64_encode(Config::get('app.api_username') . ':' . Config::get('app.api_password')),
            'Content-Type'  => 'application/json',
            'Accept'        => 'application/json',
        ];

        return new Client(['headers' => $headers]);
    }

    public function transformConfig(mixed $response){
        $data = $response['data'][0]['attributes'];
        $social_media_links = $response['included'][0]['attributes']['field_social_media_link'];

        $config = new \stdClass();
        $config->back2top               = $data['field_anita_back2top'];
        $config->back2top_label         = $data['field_anita_back2top_label'];
        $config->fs_menu_delay          = $data['field_anita_fs_menu_delay'];
        $config->image_drag_protection  = $data['field_anita_image_drag_protectio'];
        $config->int_cursor             = $data['field_anita_interactive_cursor'];
        $config->disable_right_click    = $data['field_anita_disable_right_click'];
        $config->spotlight              = $data['field_anita_spotlight'];
        $config->sticky_header          = $data['field_anita_sticky_header'];

        $config->l10n = [
            "copyright"   => $data['field_anita_copyright'],
            "rcp_message" => $data['field_anita_back2top_label'],
            "b2t_label"   => $data['field_anita_back2top_label']
        ];

        $socials = [];

        foreach ($social_media_links as $social){
            $socials[$social['title']] = [
                "url" => $social['uri'],
                "label" => $social['title']
            ];
        }

        $config->socials = $socials;
        return json_decode(json_encode($config), true);
    }
}
