<?php
require_once __DIR__ . '/BaseDao.php';

class UserDao extends BaseDao {
    public function __construct() {
        parent::__construct('users', ['username', 'email', 'password_hash', 'role', 'bio', 'favorite_pokemon_id', 'currently_playing_game_id']);
    }

    public function get_by_email($email) {
        return $this->query_unique("SELECT * FROM users WHERE email = :email", ['email' => $email]);
    }

    public function get_by_username($username) {
        return $this->query_unique("SELECT * FROM users WHERE username = :username", ['username' => $username]);
    }

    public function get_profile($id) {
        return $this->query_unique(
            "SELECT u.id, u.username, u.email, u.role, u.bio, u.created_at,
                    p.name AS favorite_pokemon_name, p.sprite_url AS favorite_pokemon_sprite,
                    g.name AS currently_playing_game_name
             FROM users u
             LEFT JOIN pokemon p ON u.favorite_pokemon_id = p.id
             LEFT JOIN games g ON u.currently_playing_game_id = g.id
             WHERE u.id = :id",
            ['id' => $id]
        );
    }

}