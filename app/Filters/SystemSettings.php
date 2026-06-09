<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\App;
use Config\SystemOptions;

class SystemSettings implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if ($request instanceof CLIRequest) {
            return null;
        }

        $settings = $this->settings();

        date_default_timezone_set($settings['fuso_horario']);

        $appConfig = config(App::class);
        $appConfig->defaultLocale = $settings['idioma'];
        $appConfig->appTimezone = $settings['fuso_horario'];

        if ($request instanceof IncomingRequest) {
            $request->setLocale($settings['idioma']);
        }

        service('language')->setLocale($settings['idioma']);

        if (class_exists('\Locale')) {
            \Locale::setDefault($settings['idioma']);
        }

        session()->set($settings);

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return null;
    }

    private function settings(): array
    {
        $options = config(SystemOptions::class);
        $defaults = [
            'idioma'       => $options->defaultLanguage,
            'fuso_horario' => $options->defaultTimezone,
            'favicon'       => 'favicon.ico',
            'logo_login'    => 'assets/img/zaventus-login-marca.png',
        ];

        try {
            $db = db_connect();

            if (! $db->tableExists('config_empresa')) {
                return $defaults;
            }

            $campos = array_values(array_filter(
                array_keys($defaults),
                static fn (string $campo): bool => $db->fieldExists($campo, 'config_empresa')
            ));

            if (empty($campos)) {
                return $defaults;
            }

            $row = $db->table('config_empresa')
                ->select($campos)
                ->where('id_config', 1)
                ->get(1)
                ->getRowArray() ?? [];
        } catch (\Throwable $exception) {
            return $defaults;
        }

        $language = (string) ($row['idioma'] ?? '');
        $timezone = (string) ($row['fuso_horario'] ?? '');
        $favicon = trim((string) ($row['favicon'] ?? ''));
        $logoLogin = trim((string) ($row['logo_login'] ?? ''));

        return [
            'idioma'       => $this->validLanguage($language, $options) ? $language : $defaults['idioma'],
            'fuso_horario' => $this->validTimezone($timezone, $options) ? $timezone : $defaults['fuso_horario'],
            'favicon'       => $favicon !== '' ? $favicon : $defaults['favicon'],
            'logo_login'    => $logoLogin !== '' ? $logoLogin : $defaults['logo_login'],
        ];
    }

    private function validLanguage(string $language, SystemOptions $options): bool
    {
        return array_key_exists($language, $options->languages);
    }

    private function validTimezone(string $timezone, SystemOptions $options): bool
    {
        return array_key_exists($timezone, $options->timezones)
            && in_array($timezone, timezone_identifiers_list(), true);
    }
}
