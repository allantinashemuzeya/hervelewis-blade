<?php

namespace App\Facades;

use App\Managers\AnitaConfigManager;
use Illuminate\Support\Facades\Facade;

/**
 * @method static array getMenu()
 * @method static array getConfig()
 */
class AnitaConfig extends Facade
{
    protected static function getFacadeAccessor()
    {
        return AnitaConfigManager::class;
    }
}
