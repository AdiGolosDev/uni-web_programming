<?php
require_once __DIR__ . '/BaseDao.php';

/**
 * type_effectiveness has a COMPOSITE primary key (attacker_type_id, defender_type_id) and no id column,
 * so the id-based methods of BaseDao (get_by_id, update, delete) do not apply and throw an error.
 * Use the *_pair methods below instead.
 */
class TypeEffectivenessDao extends BaseDao {
    public function __construct() {
        parent::__construct('type_effectiveness', ['attacker_type_id', 'defender_type_id', 'multiplier'], null);
    }

    public function get_all() {
        return $this->query("SELECT * FROM type_effectiveness ORDER BY attacker_type_id, defender_type_id");
    }

    /** The row for one attacker/defender pair, or null. */
    public function get_pair($attacker_type_id, $defender_type_id) {
        return $this->query_unique(
            "SELECT * FROM type_effectiveness WHERE attacker_type_id = :attacker AND defender_type_id = :defender",
            ['attacker' => $attacker_type_id, 'defender' => $defender_type_id]
        );
    }

    /** Just the multiplier (0, 0.5, 1 or 2) as a float, or null if the pair does not exist. */
    public function get_multiplier($attacker_type_id, $defender_type_id) {
        $row = $this->get_pair($attacker_type_id, $defender_type_id);
        return $row ? (float) $row['multiplier'] : null;
    }

    /** Everything one attacking type does to every defending type, with type names. */
    public function get_by_attacker($attacker_type_id) {
        return $this->query(
            "SELECT te.attacker_type_id, te.defender_type_id, te.multiplier, d.name AS defender_name
             FROM type_effectiveness te
             JOIN types d ON te.defender_type_id = d.id
             WHERE te.attacker_type_id = :attacker
             ORDER BY d.id",
            ['attacker' => $attacker_type_id]
        );
    }

    /** Everything every attacking type does to one defending type, with type names. */
    public function get_by_defender($defender_type_id) {
        return $this->query(
            "SELECT te.attacker_type_id, te.defender_type_id, te.multiplier, a.name AS attacker_name
             FROM type_effectiveness te
             JOIN types a ON te.attacker_type_id = a.id
             WHERE te.defender_type_id = :defender
             ORDER BY a.id",
            ['defender' => $defender_type_id]
        );
    }

    public function update_multiplier($attacker_type_id, $defender_type_id, $multiplier) {
        return $this->execute(
            "UPDATE type_effectiveness SET multiplier = :multiplier
             WHERE attacker_type_id = :attacker AND defender_type_id = :defender",
            ['multiplier' => $multiplier, 'attacker' => $attacker_type_id, 'defender' => $defender_type_id]
        );
    }

    public function delete_pair($attacker_type_id, $defender_type_id) {
        return $this->execute(
            "DELETE FROM type_effectiveness WHERE attacker_type_id = :attacker AND defender_type_id = :defender",
            ['attacker' => $attacker_type_id, 'defender' => $defender_type_id]
        );
    }
}
