<?php

namespace App\Managers;

use App\Http\Services\Album;
use App\Http\Services\SiteConfig;

class AnitaConfigManager
{
    public function __construct(
        protected Album $album,
    )
    {
    }

    public function getMenu (): array
    {
        return $this->album->generateAlbumMenu();
    }

    public  function getConfig(): array
    {

        $anita_config = (new SiteConfig())->getSiteConfig();

        $config = [
            /* --- Header and Main Menu --- */
            "main_menu" => [
                "About" => "page-about.html",
                "Services" => "page-services.html",
                "Testimonials" => "page-testimonials.html",
                "Contacts" => "page-contacts.html"
            ],
        ];

        $albumMenu = $this->getMenu();
        $albumMenu = $this->transformArray($albumMenu);
        $config['main_menu'] = array_merge($albumMenu, $config['main_menu']);

        return array_merge($config, $anita_config);
    }

    private function transformArray($inputArray): array
    {
        $outputArray = [];

        foreach ($inputArray as $item) {
            $urlParts = explode('/', trim($item['url'], '/'));
            $parent = ucwords(str_replace('-', ' ', $urlParts[0]));
            $title = $item['title'];
            $urlWithId = $item['url'] . '/' . $item['id'];

            if (!isset($outputArray[$parent])) {
                $outputArray[$parent] = [];
            }
            $outputArray[$parent][$title] = $urlWithId;
        }

        return $outputArray;
    }
}
