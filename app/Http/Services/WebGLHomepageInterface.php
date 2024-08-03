<?php

namespace App\Http\Services;

use GuzzleHttp\Client;

interface WebGLHomepageInterface
{

    public function getHomepageData(): \stdClass;
    public function getAuthenticatedClient(): Client;
}
