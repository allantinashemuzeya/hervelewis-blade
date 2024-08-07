<?php

namespace App\Http\Controllers;

use App\Facades\AnitaConfig;
use App\Http\Services\WebGLHomepage;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;

class HomeController extends Controller
{
    //

    public function index(WebGLHomepage $webGLHomepage): View|Factory|Application
    {
        $homePageData = $webGLHomepage->getHomepageData();

        return view('Home', [
            'config' => AnitaConfig::getConfig(),
            'menu' => AnitaConfig::getMenu(),
            'data' => $homePageData,
        ]);
    }

}
