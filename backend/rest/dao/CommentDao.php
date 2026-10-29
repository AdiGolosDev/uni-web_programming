<?php
require_once __DIR__ . '/BaseDao.php';

class CommentDao extends BaseDao {
    public function __construct() {
        parent::__construct('comments', ['team_id', 'user_id', 'body']);
    }

    public function get_by_team($team_id) {
        return $this->query(
            "SELECT c.*, u.username
             FROM comments c
             JOIN users u ON c.user_id = u.id
             WHERE c.team_id = :team_id
             ORDER BY c.created_at, c.id",
            ['team_id' => $team_id]
        );
    }

    public function get_by_user($user_id) {
        return $this->query(
            "SELECT * FROM comments WHERE user_id = :user_id ORDER BY created_at DESC",
            ['user_id' => $user_id]
        );
    }
}
