<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class SystemOptions extends BaseConfig
{
    public string $defaultLanguage = 'pt-BR';
    public string $defaultTimezone = 'America/Manaus';

    public array $languages = [
        'pt-BR' => 'Portugues (Brasil)',
        'en'    => 'English',
        'es'    => 'Espanol',
        'fr'    => 'Francais',
        'de'    => 'Deutsch',
        'it'    => 'Italiano',
    ];

    public array $timezones = [
        'America/Manaus'        => 'Manaus',
        'America/Rio_Branco'    => 'Rio Branco / Acre',
        'America/Sao_Paulo'     => 'Sao Paulo / Brasilia',
        'America/Noronha'       => 'Fernando de Noronha',
        'UTC'                   => 'UTC',
        'Pacific/Auckland'      => 'Auckland',
        'Australia/Sydney'      => 'Sydney',
        'Asia/Tokyo'            => 'Tokyo',
        'Asia/Dubai'            => 'Dubai',
        'Africa/Maputo'         => 'Maputo',
        'Africa/Luanda'         => 'Luanda',
        'Europe/Berlin'         => 'Berlin',
        'Europe/Madrid'         => 'Madrid',
        'Europe/Paris'          => 'Paris',
        'Europe/Lisbon'         => 'Lisboa',
        'Europe/London'         => 'Londres',
        'Atlantic/Azores'       => 'Acores',
        'America/Buenos_Aires'  => 'Buenos Aires',
        'America/Santiago'      => 'Santiago',
        'America/Bogota'        => 'Bogota',
        'America/Lima'          => 'Lima',
        'America/New_York'      => 'New York',
        'America/Chicago'       => 'Chicago',
        'America/Mexico_City'   => 'Mexico City',
        'America/Denver'        => 'Denver',
        'America/Los_Angeles'   => 'Los Angeles',
    ];
}
