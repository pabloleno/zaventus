<?php

namespace App\Libraries;

final class ThirdPartyComposerLoader
{
    public static function loadWithoutPsrLog(string $autoloadPath): void
    {
        $loader = require $autoloadPath;

        if (is_object($loader) && method_exists($loader, 'setPsr4')) {
            $loader->setPsr4('Psr\\Log\\', []);
        }
    }
}
