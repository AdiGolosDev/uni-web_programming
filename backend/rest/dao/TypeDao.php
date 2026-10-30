<?php
require_once __DIR__ . '/BaseDao.php';

class TypeDao extends BaseDao {
    public function __construct(){
        parent::__construct('types', ['name']);
    }

    public function get_by_name($name) {
        return $this->query_unique("SELECT * FROM types WHERE name = :name", ['name' => $name]);
    }
}
