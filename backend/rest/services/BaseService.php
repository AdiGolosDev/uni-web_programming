<?php
require_once __DIR__ . '/../../utils/exceptions.php';
require_once __DIR__ . '/../../utils/Validator.php';

class BaseService {
    protected $dao;
    protected $entity_name = 'Entity';

    public function __construct($dao) {
        $this->dao = $dao;
    }

    public function get_all() {
        return $this->dao->get_all();
    }

    public function get_by_id($id) {
        $entity = $this->dao->get_by_id($id);
        if (!$entity) {
            throw new NotFoundException("{$this->entity_name} not found.");
        }
        return $entity;
    }

    public function add($entity) {
        $this->validate($entity);
        $created = $this->dao->add($entity);
        return $this->dao->get_by_id($created['id']);
    }

    public function update($entity, $id) {
        $this->get_by_id($id);
        $this->validate($entity, $id);
        $this->dao->update($entity, $id);
        return $this->dao->get_by_id($id);
    }

    public function delete($id) {
        $this->get_by_id($id);
        $this->dao->delete($id);
    }

    protected function validate($entity, $id = null) {}

    protected function empty_to_null($entity, $fields) {
        foreach ($fields as $field) {
            if (array_key_exists($field, $entity) && trim((string) $entity[$field]) === '') {
                $entity[$field] = null;
            }
        }
        return $entity;
    }
}