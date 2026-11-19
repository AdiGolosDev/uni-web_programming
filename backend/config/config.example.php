<?php

class Config
{
    public static function DB_HOST()     { return self::get_env('DB_HOST', '127.0.0.1'); }
    public static function DB_PORT()     { return self::get_env('DB_PORT', 3306); }
    public static function DB_NAME()     { return self::get_env('DB_NAME', 'pokeplanner'); }
    public static function DB_USER()     { return self::get_env('DB_USER', 'root'); }
    public static function DB_PASSWORD() { return self::get_env('DB_PASSWORD', ''); }

        /* ---------- External API: PokéAPI (https://pokeapi.co/docs/v2 – free, no API key) ---------- */
    public static function POKEAPI_BASE_URL()   { return self::get_env('POKEAPI_BASE_URL', 'https://pokeapi.co/api/v2'); }
    public static function POKEAPI_TIMEOUT()    { return self::get_env('POKEAPI_TIMEOUT', 5); }
    /** Local copy of every Pokémon's details, so each one is fetched from PokéAPI only once. */
    public static function POKEAPI_CACHE_FILE() { return self::get_env('POKEAPI_CACHE_FILE', __DIR__ . '/../../database/pokemon_details.json'); }

    public static function get_env($name, $default) {
        $value = getenv($name);
        return ($value !== false && trim($value) !== '') ? $value : $default;
    }
}