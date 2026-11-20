<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../utils/exceptions.php';
require_once __DIR__ . '/../../utils/HttpClient.php';
require_once __DIR__ . '/../../utils/Logger.php';

/**
 * Extra Pokémon details from PokéAPI (free, no key), looked up by Pokédex number.
 * Order of work: 1) look in database/pokemon_details.json  2) if missing, call PokéAPI  3) save complete results.
 * Three parts are loaded separately: basic_info (/pokemon), description (/pokemon-species), evolution_chain.
 * A failed part is reported in "missing" and the others are still returned. Only when everything fails: 503.
 */
class PokeApiService {
    const UNAVAILABLE = 'Pokémon details are temporarily unavailable. Please try again later.';

    public function details($dex_number) {
        $dex_number = (int) $dex_number;

        $cached = $this->cache_get($dex_number);
        if ($cached !== null) {
            Logger::info("PokeAPI details #$dex_number: found in cache, no API call");
            return $cached + ['missing' => [], 'source' => 'cache'];
        }
        Logger::info("PokeAPI details #$dex_number: not in cache, calling PokeAPI");

        $missing = [];
        $basic = $this->attempt('basic_info', fn() => $this->fetch_basic($dex_number), $missing);
        $species = $this->attempt('description', fn() => $this->fetch_species($dex_number), $missing);

        $evolution = null;
        if ($species === null) {
            $missing[] = 'evolution_chain';                 // the chain id comes from the species data
        } elseif ($species['evolution_chain_id'] !== null) {
            $evolution = $this->attempt('evolution_chain', fn() => $this->fetch_evolution($species['evolution_chain_id']), $missing);
        }

        if ($basic === null && $species === null) {
            Logger::error("PokeAPI details #$dex_number: every call failed");
            throw new ExternalServiceException(self::UNAVAILABLE);
        }

        $details = [
            'height_m'        => $basic['height_m'] ?? null,
            'weight_kg'       => $basic['weight_kg'] ?? null,
            'abilities'       => $basic['abilities'] ?? null,
            'artwork_url'     => $basic['artwork_url'] ?? null,
            'description'     => $species['description'] ?? null,
            'evolution_chain' => $evolution
        ];

        // only complete results are cached, otherwise a half-empty answer would stay forever
        if ($missing === []) {
            $this->cache_put($dex_number, $details);
            Logger::info("PokeAPI details #$dex_number: loaded from PokeAPI and saved to cache");
        } else {
            Logger::info("PokeAPI details #$dex_number: loaded from PokeAPI, missing: " . implode(', ', $missing) . ' (not cached)');
        }
        return $details + ['missing' => $missing, 'source' => 'pokeapi'];
    }

    private function fetch_basic($dex) {
        $data = $this->call("pokemon/$dex");
        if (!isset($data['height'], $data['weight'])) {
            $this->bad_data("pokemon/$dex");
        }
        $abilities = [];
        foreach ($data['abilities'] ?? [] as $entry) {
            if (isset($entry['ability']['name'])) {
                $abilities[] = ['name' => $this->pretty($entry['ability']['name']), 'is_hidden' => (bool) ($entry['is_hidden'] ?? false)];
            }
        }
        return [
            'height_m'    => round($data['height'] / 10, 1),    // PokéAPI: decimetres
            'weight_kg'   => round($data['weight'] / 10, 1),    // PokéAPI: hectograms
            'abilities'   => $abilities,
            'artwork_url' => $data['sprites']['other']['official-artwork']['front_default'] ?? null
        ];
    }

    private function fetch_species($dex) {
        $data = $this->call("pokemon-species/$dex");
        if (!isset($data['flavor_text_entries']) || !is_array($data['flavor_text_entries'])) {
            $this->bad_data("pokemon-species/$dex");
        }

        $description = null;
        foreach (array_reverse($data['flavor_text_entries']) as $entry) {
            if (($entry['language']['name'] ?? '') === 'en' && isset($entry['flavor_text'])) {
                $text = str_replace(["\u{00AD}", "\f", "\n"], ['', ' ', ' '], $entry['flavor_text']);
                $description = trim(preg_replace('/\s+/u', ' ', $text));
                break;
            }
        }

        $chain_id = null;
        if (preg_match('#/evolution-chain/(\d+)/?$#', $data['evolution_chain']['url'] ?? '', $m)) {
            $chain_id = (int) $m[1];
        }
        return ['description' => $description, 'evolution_chain_id' => $chain_id];
    }

    private function fetch_evolution($chain_id) {
        $data = $this->call("evolution-chain/$chain_id");
        if (!isset($data['chain']['species']['name'])) {
            $this->bad_data("evolution-chain/$chain_id");
        }
        return $this->parse_chain_node($data['chain']);
    }

    private function parse_chain_node($node) {
        $detail = $node['evolution_details'][0] ?? [];
        $dex = preg_match('#/pokemon-species/(\d+)/?$#', $node['species']['url'] ?? '', $m) ? (int) $m[1] : null;

        $children = [];
        foreach ($node['evolves_to'] ?? [] as $child) {
            $children[] = $this->parse_chain_node($child);
        }
        return [
            'name'       => $this->pretty($node['species']['name'] ?? 'unknown'),
            'dex_number' => $dex,
            'min_level'  => $detail['min_level'] ?? null,
            'trigger'    => $detail['trigger']['name'] ?? null,
            'item'       => isset($detail['item']['name']) ? $this->pretty($detail['item']['name']) : null,
            'evolves_to' => $children
        ];
    }

    private function attempt($part, $callable, &$missing) {
        try {
            return $callable();
        } catch (ExternalServiceException $e) {
            $missing[] = $part;
            return null;
        }
    }

    private function call($path) {
        $url = rtrim(Config::POKEAPI_BASE_URL(), '/') . '/' . $path;
        $started = microtime(true);
        $data = HttpClient::get_json($url, Config::POKEAPI_TIMEOUT(), self::UNAVAILABLE);
        Logger::info(sprintf('PokeAPI GET %s OK (%d ms)', $path, (int) round((microtime(true) - $started) * 1000)));
        return $data;
    }

    private function bad_data($path)
    {
        Logger::error("PokeAPI $path returned data in an unexpected format");
        throw new ExternalServiceException(self::UNAVAILABLE);
    }

    private function pretty($name) {
        return ucwords(str_replace('-', ' ', (string) $name));
    }

    // ---------- cache: database/pokemon_details.json, one entry per Pokédex number ----------
    private function cache_get($dex) {
        $entry = $this->cache_read()[(string) $dex] ?? null;
        if (!is_array($entry)) {
            return null;
        }
        unset($entry['fetched_at']);
        return $entry;
    }

    private function cache_read() {
        $file = Config::POKEAPI_CACHE_FILE();
        if (!is_file($file)) {
            return [];
        }
        $json = @file_get_contents($file);
        if ($json !== false && trim($json) === '') {
            return [];
        }
        $data = $json === false ? null : json_decode($json, true);
        if (!is_array($data)) {
            Logger::error("PokeAPI cache file is unreadable or not valid JSON, ignoring it ($file)");
            return [];
        }
        return $data;
    }

    private function cache_put($dex, $details) {
        $file = Config::POKEAPI_CACHE_FILE();
        $cache = $this->cache_read();
        $cache[(string) $dex] = $details + ['fetched_at' => date('c')];
        ksort($cache, SORT_NUMERIC);

        $json = json_encode($cache, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($json === false || @file_put_contents($file, $json, LOCK_EX) === false) {
            Logger::error("PokeAPI cache could not be written ($file)");
        }
    }
}