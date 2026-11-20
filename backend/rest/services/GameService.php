<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/GameDao.php';

class GameService extends BaseService {
    protected $entity_name = 'Game';

    public function __construct() {
        parent::__construct(new GameDao());
    }

    public function add($entity) {
        return parent::add($this->empty_to_null($entity, ['release_year']));
    }

    public function update($entity, $id) {
        return parent::update($this->empty_to_null($entity, ['release_year']), $id);
    }

    public function get_by_generation($generation) {
        Validator::integer($generation, 'generation', 1, 9);
        return $this->dao->get_by_generation($generation);
    }

    public function delete($id) {
        $this->get_by_id($id);
        try {
            $this->dao->delete($id);
        } catch (PDOException $e) {
            if (($e->errorInfo[1] ?? null) == 1451) {
                throw new ValidationException('This game is used by existing teams and cannot be deleted.');
            }
            throw $e;
        }
    }

    public function validate($entity, $id = null) {
        Validator::required($entity, ['name', 'generation']);
        Validator::length($entity['name'], 'name', 1, 100);
        Validator::integer($entity['generation'], 'generation', 1, 9);

        if (isset($entity['release_year']) && trim((string) $entity['release_year']) !== '') {
            Validator::integer($entity['release_year'], 'release_year', 1996, (int) date('Y') + 1);
        }

        $same_name = $this->dao->get_by_name(trim($entity['name']));
        if ($same_name && $same_name['id'] != $id) {
            throw new ValidationException("A game named '" . trim($entity['name']) . "' already exists.");
        }
    }
}