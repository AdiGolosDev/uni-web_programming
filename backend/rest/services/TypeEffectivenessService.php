<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/TypeEffectivenessDao.php';
require_once __DIR__ . '/../dao/TypeDao.php';

/**
 * SPECIFIC: this table has a COMPOSITE primary key (attacker_type_id + defender_type_id) and no id column.
 * The inherited get_by_id / update / delete throw a LogicException here (the DAO refuses them),
 * so ROUTES MUST USE the *_pair methods below instead. get_all() and add() work (add is overridden).
 * They are named *_pair and not get_by_id/update/delete because PHP does not allow an override
 * to require extra parameters (here: two ids instead of one).
 */
class TypeEffectivenessService extends BaseService {
    protected $entity_name = 'Type matchup';
    private const ALLOWED_MULTIPLIERS = [0.0, 0.5, 1.0, 2.0];
    private $type_dao;

    public function __construct() {
        parent::__construct(new TypeEffectivenessDao());
        $this->type_dao = new TypeDao();
    }

    public function get_pair($attacker_id, $defender_id) {
        Validator::integer($attacker_id, 'attacker_type_id', 1);
        Validator::integer($defender_id, 'defender_type_id', 1);
        $row = $this->dao->get_pair($attacker_id, $defender_id);
        if (!$row) {
            throw new NotFoundException("{$this->entity_name} not found.");
        }
        return $row;
    }

    public function get_by_attacker($type_id) {
        Validator::integer($type_id, 'type_id', 1);
        $this->type_or_404($type_id);
        return $this->dao->get_by_attacker($type_id);
    }

    public function get_by_defender($type_id) {
        Validator::integer($type_id, 'type_id', 1);
        $this->type_or_404($type_id);
        return $this->dao->get_by_defender($type_id);
    }

    public function add($entity) {
        $this->validate($entity);
        $entity['multiplier'] = (float) $entity['multiplier'];
        $this->dao->add($entity);
        return $this->dao->get_pair($entity['attacker_type_id'], $entity['defender_type_id']);
    }

    public function update_pair($entity, $attacker_id, $defender_id) {
        $this->get_pair($attacker_id, $defender_id);
        Validator::required($entity, ['multiplier']);
        $this->validate_multiplier($entity['multiplier']);
        $this->dao->update_multiplier($attacker_id, $defender_id, (float) $entity['multiplier']);
        return $this->dao->get_pair($attacker_id, $defender_id);
    }

    public function delete_pair($attacker_id, $defender_id) {
        $this->get_pair($attacker_id, $defender_id);
        $this->dao->delete_pair($attacker_id, $defender_id);
    }

    protected function validate($entity, $id = null) {
        Validator::required($entity, ['attacker_type_id', 'defender_type_id', 'multiplier']);
        Validator::integer($entity['attacker_type_id'], 'attacker_type_id', 1);
        Validator::integer($entity['defender_type_id'], 'defender_type_id', 1);
        $this->validate_multiplier($entity['multiplier']);

        if (!$this->type_dao->get_by_id($entity['attacker_type_id'])) {
            throw new ValidationException("Field 'attacker_type_id' does not match an existing type.");
        }
        if (!$this->type_dao->get_by_id($entity['defender_type_id'])) {
            throw new ValidationException("Field 'defender_type_id' does not match an existing type.");
        }

        if ($this->dao->get_pair($entity['attacker_type_id'], $entity['defender_type_id'])) {
            throw new ValidationException('This type matchup already exists. Change its multiplier with PUT instead.');
        }
    }

    private function validate_multiplier($value) {
        if (!is_numeric($value) || !in_array((float) $value, self::ALLOWED_MULTIPLIERS, true)) {
            throw new ValidationException("Field 'multiplier' must be one of: 0, 0.5, 1, 2.");
        }
    }

    private function type_or_404($type_id) {
        if (!$this->type_dao->get_by_id($type_id)) {
            throw new NotFoundException('Type not found.');
        }
    }
}