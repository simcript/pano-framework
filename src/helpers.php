<?php

if (!function_exists('dd')) {
    function dd(...$args): void
    {
        highlight_string("<?php\n" . var_export($args, true) . ";\n?>");
        exit();
    }
}

if (!function_exists('url')) {
    function url(string $path): string
    {
        return trim(config('app.url'), '/') . '/' . $path;
    }
}

if (!function_exists('env')) {

    function env(string $key, mixed $default = null): mixed
    {
        return $_ENV[$key]
            ?? $_SERVER[$key]
            ?? $default;
    }
}
if (!function_exists('config')) {

    function config(string $key, mixed $default = null): mixed
    {
        static $configs = [];

        if (empty($configs)) {
            $configPath = BASE_PATH . '/config';

            if (is_dir($configPath)) {
                foreach (glob($configPath . '/*.php') as $file) {
                    $name = basename($file, '.php');
                    $configs[$name] = require $file;
                }
            }
        }

        $result = $configs;

        foreach (explode('.', $key) as $segment) {
            if (!is_array($result) || !array_key_exists($segment, $result)) {
                return $default;
            }
            $result = $result[$segment];
        }
        return $result;
    }
}

