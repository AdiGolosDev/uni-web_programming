<?php
require_once __DIR__ . '/BaseDao.php';

class TeamDao extends BaseDao {
    public function __construct() {
        parent::__construct('teams', ['user_id', 'game_id', 'name', 'challenge', 'description', 'is_public']);
    }

    public function get_by_user($user_id) {
        return $this->query(
            "SELECT t.*, g.name AS game_name
             FROM teams t
             JOIN games g ON t.game_id = g.id
             WHERE t.user_id = :user_id
             ORDER BY t.updated_at DESC",
            ['user_id' => $user_id]
        );
    }

    public function get_public($limit = 20, $offset = 0) {
        return $this->query(
            "SELECT t.*, u.username, g.name AS game_name
             FROM teams t
             JOIN users u ON t.user_id = u.id
             JOIN games g ON t.game_id = g.id
             WHERE t.is_public = 1
             ORDER BY t.created_at DESC
             LIMIT " . (int) $limit . " OFFSET " . (int) $offset
        );
    }

    public function get_details($id) {
        return $this->query_unique(
            "SELECT t.*, u.username, g.name AS game_name
             FROM teams t
             JOIN users u ON t.user_id = u.id
             JOIN games g ON t.game_id = g.id
             WHERE t.id = :id",
            ['id' => $id]
        );
    }
}
