<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/PokemonDao.php';
require_once __DIR__ . '/../dao/TypeDao.php';
require_once __DIR__ . '/PokeApiService.php';

class PokemonService extends BaseService {
    protected $entity_name = 'Pokémon';
    private const STATS = ['hp', 'attack', 'defense', 'sp_atk', 'sp_def', 'speed'];
    private $type_dao;
    private $pokeapi;

    public function __construct() {
        parent::__construct(new PokemonDao());
        $this->type_dao = new TypeDao();
        $this->pokeapi = new PokeApiService();
    }

    /** GET /pokemon/{id}/details – the id is OUR database id; PokéAPI is asked by Pokédex number. */
    public function get_details($id) {
        $pokemon = $this->get_by_id($id);                   // 404 if it is not in our catalog
        $extras = $this->pokeapi->details($pokemon['dex_number']);
        return array_merge([
            'pokemon_id' => (int) $pokemon['id'],
            'dex_number' => (int) $pokemon['dex_number'],
            'name'       => $pokemon['name']
        ], $extras);
    }

    public function get_all() {
        return $this->dao->get_all_with_types();
    }

    public function get_by_id($id) {
        $pokemon = $this->dao->get_by_id_with_types($id);
        if (!$pokemon) {
            throw new NotFoundException("{$this->entity_name} not found.");
        }
        return $pokemon;
    }

    public function search($term) {
        Validator::length($term, 'q', 1, 100);
        return $this->dao->search_by_name(trim($term));
    }

    public function get_by_type($type_id) {
        Validator::integer($type_id, 'type_id', 1);
        return $this->dao->get_by_type($type_id);
    }

    public function get_by_generation($generation) {
        Validator::integer($generation, 'generation', 1, 9);
        return $this->dao->get_by_generation($generation);
    }

    public function add($entity) {
        $created = parent::add($this->empty_to_null($entity, ['type2_id', 'sprite_url']));
        return $this->dao->get_by_id_with_types($created['id']);
    }

    public function update($entity, $id) {
        parent::update($this->empty_to_null($entity, ['type2_id', 'sprite_url']), $id);
        return $this->dao->get_by_id_with_types($id);
    }

    public function delete($id) {
        $this->get_by_id($id);
        try {
            $this->dao->delete($id);
        } catch (PDOException $e) {
            if (($e->errorInfo[1] ?? null) == 1451) {
                throw new ValidationException('This Pokémon is used in existing teams and cannot be deleted.');
            }
            throw $e;
        }
    }

    protected function validate($entity, $id = null) {
        Validator::required($entity, array_merge(['dex_number', 'name', 'generation', 'type1_id'], self::STATS));

        Validator::integer($entity['dex_number'], 'dex_number', 1, 9999);
        Validator::length($entity['name'], 'name', 1, 100);
        Validator::integer($entity['generation'], 'generation', 1, 9);
        foreach (self::STATS as $stat) {
            Validator::integer($entity[$stat], $stat, 1, 255);
        }

        Validator::integer($entity['type1_id'], 'type1_id', 1);
        $this->assert_type_exists($entity['type1_id'], 'type1_id');

        if (isset($entity['type2_id']) && trim((string) $entity['type2_id']) !== '') {
            Validator::integer($entity['type2_id'], 'type2_id', 1);
            $this->assert_type_exists($entity['type2_id'], 'type2_id');
            if ((int) $entity['type2_id'] === (int) $entity['type1_id']) {
                throw new ValidationException("Field 'type2_id' must be different from 'type1_id'.");
            }
        }

        if (isset($entity['sprite_url']) && trim((string) $entity['sprite_url']) !== '') {
            Validator::length($entity['sprite_url'], 'sprite_url', 1, 255);
            $url = trim($entity['sprite_url']);
            $scheme = parse_url($url, PHP_URL_SCHEME);
            if (!filter_var($url, FILTER_VALIDATE_URL) || !in_array($scheme, ['http', 'https'], true)) {
                throw new ValidationException("Field 'sprite_url' must be a valid http(s) URL.");
            }
        }

        $same_dex = $this->dao->get_by_dex_number((int) $entity['dex_number']);
        if ($same_dex && $same_dex['id'] != $id) {
            throw new ValidationException("Dex number {$entity['dex_number']} already exists.");
        }
    }

    private function assert_type_exists($type_id, $field) {
        if (!$this->type_dao->get_by_id($type_id)) {
            throw new ValidationException("Field '$field' does not match an existing type.");
        }
    }
}