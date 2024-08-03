<?php

namespace App\Http\Services;

use GuzzleHttp\Client;

interface SiteConfigInterface
{
    public function getSiteConfig(): array;
    public function getSiteConfigByKey(string $key): string;
    public function updateSiteConfig(array $data): bool;

    public function getAuthenticatedClient (): Client;

    public function transformConfig(mixed $response);


}
