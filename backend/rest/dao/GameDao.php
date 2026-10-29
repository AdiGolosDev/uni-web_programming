<?php
require_once __DIR__ . '/BaseDao.php';

class GameDao extends BaseDao {
    public function __construct() {
        parent::__construct('games', ['name', 'generation', 'release_year']);
    }

    public function get_by_name($name) {
        return $this->query_unique("SELECT * FROM games WHERE name = :name", ['name' => $name]);
    }

    public function get_by_generation($generation) {
        return $this->query("SELECT * FROM games WHERE generation = :generation ORDER BY release_year, name", ['generation' => $generation]);
    }
}
