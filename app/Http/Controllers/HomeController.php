<?php

namespace App\Http\Controllers;

use App\Http\Services\Album;
use App\Http\Services\WebGLHomepage;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    //

    public function index()
    {
        $menu = ConfigController::getMenu();

        $homePageData = (new WebGLHomepage())->getHomepageData();
        // Get the homepage data from the WebGLHomepage service

        return view('Home', [
            'config' => ConfigController::getConfig(),
            'menu' => $menu,
            'homePageData' => $homePageData,
        ]);
    }

}
