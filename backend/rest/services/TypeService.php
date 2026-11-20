<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/TypeDao.php';

class TypeService extends BaseService {
    protected $entity_name = 'Type';

    public function __construct() {
        parent::__construct(new TypeDao());
    }

    public function delete($id) {
        $this->get_by_id($id);
        try {
            $this->dao->delete($id);
        } catch (PDOException $e) {
            if (($e->errorInfo[1] ?? null) == 1451) {
                throw new ValidationException('This type is used by existing Pokémon and cannot be deleted.');
            }
            throw $e;
        }
    }

    protected function validate($entity, $id = null) {
        Validator::required($entity, ['name']);
        Validator::length($entity['name'], 'name', 1, 30);

        $same_name = $this->dao->get_by_name(trim($entity['name']));
        if ($same_name && $same_name['id'] != $id) {
            throw new ValidationException("A type named '" . trim($entity['name']) . "' already exists.");
        }
    }
}