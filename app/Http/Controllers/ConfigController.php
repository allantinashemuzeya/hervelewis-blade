<?php

namespace App\Http\Controllers;

use App\Http\Services\Album;
use App\Http\Services\SiteConfig;
use Illuminate\Http\Request;

class ConfigController extends Controller
{
    //

    public static function getMenu ()
    {
        return Album::generateAlbumMenu();
    }

    public static function getConfig(): array
    {

        $anita_config = (new SiteConfig())->getSiteConfig();


        $config =  [
            /* --- Header and Main Menu --- */

            // Main Menu config for quick and same result for all pages.
            // Use 'Label' : 'url' for menu items and 'Label' : array( ... ) for Submenus. Don't forget about commas after each item.
            "main_menu" => [
                "Galleries" => [
                    "WebGL Carousel" => "albums-gl-carousel.html",
                    "Flat Carousel" => "albums-carousel.html",
                    "Slider" => [
                        "Parallax Movement" => "albums-slider-parallax.html",
                        "Fade Sliding" => "albums-slider-fade.html",
                        "Diagonally Sliced" => "albums-slider-sliced.html",
                        "Pixel Storm" => "albums-slider-pixels.html"
                    ],
                    "Adjusted Grid" => [
                        "Adjusted: 2 Columns" => "albums-adjusted-2col.html",
                        "Adjusted: 3 Columns" => "albums-adjusted-3col.html",
                        "Adjusted: 4 Columns" => "albums-adjusted-4col.html"
                    ],
                    "Strong Grid" => [
                        "Grid: 2 Columns" => "albums-grid-2col.html",
                        "Grid: 3 Columns" => "albums-grid-3col.html",
                        "Grid: 4 Columns" => "albums-grid-4col.html"
                    ],
                    "Masonry Grid" => [
                        "Masonry: 2 Columns" => "albums-masonry-2col.html",
                        "Masonry: 3 Columns" => "albums-masonry-3col.html",
                        "Masonry: 4 Columns" => "albums-masonry-4col.html"
                    ],
                    "Justified" => "albums-justified.html",
                    "Bricks 1x2" => "albums-bricks-1x2.html",
                    "Bricks 2x3" => "albums-bricks-2x3.html"
                ],
                "About" => "page-about.html",
                "Services" => "page-services.html",
                "Testimonials" => "page-testimonials.html",
                "Contacts" => "page-contacts.html"
            ],

            // Option to stick the header to the top of the page
            "sticky_header" => true,

            // Menu items appear delay in milliseconds
            "fs_menu_delay" => 100,

            /* --- Social Links --- */
            "socials" => [
                "facebook" => [
                    "url" => "#",
                    "label" => "Fb."
                ],
                "twitter" => [
                    "url" => "https://x.com",
                    "label" => "X."
                ],
                "instagram" => [
                    "url" => "#",
                    "label" => "In."
                ],
                "youtube" => [
                    "url" => "#",
                    "label" => "Yt."
                ],
            ],

            /* --- Content Features --- */
            // Page background Spotlight Effect
            "spotlight" => true,

            // Back to Top Button
            "back2top" => true,

            // Interactive Cursor
            "int_cursor" => true,

            /* --- Protection Options --- */
            // Right Click Protection
            "disable_right_click" => true,

            // Protect Images from Drag
            "image_drag_protection" => true,

            /* --- Localization --- */
            "l10n" => [
                // Footer Copyright string
                "copyright" => "Copyright &copy; 2024. All Rights Reserved.",

                // The message that appears when visitors try to open context menu
                "rcp_message" => "Context menu is not allowed on this website",

                // The Button Label for Context Menu blocker
                "rcp_button" => "Got It!",

                // Back to Top Label
                "b2t_label" => "Back to Top"
            ]
        ];

        $albumMenu = self::getMenu();
        $albumMenu = self::transformArray($albumMenu);
        $config['main_menu'] = array_merge($albumMenu, $config['main_menu']);

        return array_merge($config, $anita_config);
    }

    public static function transformArray($inputArray): array
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
