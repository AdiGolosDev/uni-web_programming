<?php
require_once __DIR__ . '/BaseDao.php';

class PokemonDao extends BaseDao {
    protected const SELECT_WITH_TYPES =
        "SELECT p.*, t1.name AS type1_name, t2.name AS type2_name
         FROM pokemon p
         JOIN types t1 ON p.type1_id = t1.id
         LEFT JOIN types t2 ON p.type2_id = t2.id";

    public function __construct() {
        parent::__construct('pokemon', ['dex_number', 'name', 'generation', 'type1_id', 'type2_id', 'sprite_url',
            'hp', 'attack', 'defense', 'sp_atk', 'sp_def', 'speed']);
    }

    public function get_all_with_types() {
        return $this->query(self::SELECT_WITH_TYPES . " ORDER BY p.dex_number");
    }

    public function get_by_id_with_types($id) {
        return $this->query_unique(self::SELECT_WITH_TYPES . " WHERE p.id = :id", ['id' => $id]);
    }

    public function get_by_dex_number($dex_number) {
        return $this->query_unique(self::SELECT_WITH_TYPES . " WHERE p.dex_number = :dex", ['dex' => $dex_number]);
    }

    public function search_by_name($term) {
        return $this->query(
            self::SELECT_WITH_TYPES . " WHERE p.name LIKE :term ORDER BY p.dex_number", ['term' => '%' . $term . '%']);
    }

    public function get_by_type($type_id) {
        return $this->query(
            self::SELECT_WITH_TYPES . " WHERE p.type1_id = :type1 OR p.type2_id = :type2 ORDER BY p.dex_number",
            ['type1' => $type_id, 'type2' => $type_id]
        );
    }

    public function get_by_generation($generation) {
        return $this->query(self::SELECT_WITH_TYPES . " WHERE p.generation = :generation ORDER BY p.dex_number", ['generation' => $generation]);
    }
}
