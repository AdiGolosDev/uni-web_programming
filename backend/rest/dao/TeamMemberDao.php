<?php
require_once __DIR__ . '/BaseDao.php';

class TeamMemberDao extends BaseDao {
    public function __construct() {
        parent::__construct('team_members', ['pokemon_id', 'team_id', 'slot', 'nickname']);
    }

    public function get_by_team($team_id) {
        return $this->query(
            "SELECT tm.*, p.dex_number, p.name AS pokemon_name, p.sprite_url,
                    p.hp, p.attack, p.defense, p.sp_atk, p.sp_def, p.speed,
                    p.type1_id, p.type2_id, t1.name AS type1_name, t2.name AS type2_name
             FROM team_members tm
             JOIN pokemon p ON tm.pokemon_id = p.id
             JOIN types t1 ON p.type1_id = t1.id
             LEFT JOIN types t2 ON p.type2_id = t2.id
             WHERE tm.team_id = :team_id
             ORDER BY tm.slot",
            ['team_id' => $team_id]
        );
    }

    public function count_by_team($team_id) {
        return (int) $this->query_unique("SELECT COUNT(*) AS total FROM team_members WHERE team_id = :team_id", ['team_id' => $team_id])['total'];
    }

    public function next_free_slot($team_id) {
        $used = array_column($this->query("SELECT slot FROM team_members WHERE team_id = :team_id", ['team_id' => $team_id]), 'slot');
        for ($slot = 1; $slot <= 6; $slot++) {
            if (!in_array($slot, $used)) {
                return $slot;
            }
        }
        return null;
    }
}
