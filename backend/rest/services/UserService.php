<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../../utils/Roles.php';
require_once __DIR__ . '/../dao/UserDao.php';
require_once __DIR__ . '/../dao/PokemonDao.php';
require_once __DIR__ . '/../dao/GameDao.php';

class UserService extends BaseService {
    protected $entity_name = 'User';
    private $pokemon_dao;
    private $game_dao;

    public function __construct() {
        parent::__construct(new UserDao());
        $this->pokemon_dao = new PokemonDao();
        $this->game_dao = new GameDao();
    }

    public function get_all() {
        return array_map(fn($user) => $this->without_hash($user), $this->dao->get_all());
    }

    public function get_by_id($id) {
        return $this->without_hash(parent::get_by_id($id));
    }

    public function get_profile($id) {
        Validator::integer($id, 'id', 1);
        $profile = $this->dao->get_profile($id);
        if (!$profile) {
            throw new NotFoundException('User not found.');
        }
        return $profile;
    }

    public function add($entity) {
        $entity = $this->prepare($entity);
        if (!isset($entity['role']) || trim((string) $entity['role']) === '') {
            $entity['role'] = Roles::TRAINER;
        }
        if (isset($entity['password']) && is_string($entity['password'])) {
            $entity['password_hash'] = password_hash($entity['password'], PASSWORD_DEFAULT);
        }
        return $this->without_hash(parent::add($entity));
    }

    public function register($entity) {
        $entity['role'] = Roles::TRAINER;
        return $this->add($entity);
    }

    public function update($entity, $id) {
        $current = $this->get_by_id($id);
        $entity = $this->prepare($entity);
        $entity['role'] = $current['role'];

        if (isset($entity['password']) && trim((string) $entity['password']) !== '') {
            if (is_string($entity['password'])) {
                $entity['password_hash'] = password_hash($entity['password'], PASSWORD_DEFAULT);
            }
        } else {
            unset($entity['password']);
        }

        parent::update($entity, $id);
        return $this->get_by_id($id);
    }

    public function change_role($id, $role) {
        $user = $this->get_by_id($id);
        Validator::one_of($role, 'role', Roles::all());
        if ($user['role'] === Roles::ADMIN && $role !== Roles::ADMIN) {
            $this->assert_not_last_admin();
        }
        $this->dao->update(['role' => $role], $id);
        return $this->get_by_id($id);
    }

    public function delete($id) {
        $user = $this->get_by_id($id);
        if ($user['role'] === Roles::ADMIN) {
            $this->assert_not_last_admin();
        }
        $this->dao->delete($id);
    }

    protected function validate($entity, $id = null) {
        Validator::required($entity, ['username', 'email']);
        Validator::length($entity['username'], 'username', 3, 50);
        if (!preg_match('/^[A-Za-z0-9_.-]+$/', trim($entity['username']))) {
            throw new ValidationException("Field 'username' may only contain letters, numbers, '_', '.' and '-'.");
        }

        Validator::email($entity['email']);
        Validator::length($entity['email'], 'email', 1, 100);

        if ($id === null) {
            Validator::required($entity, ['password']);
        }
        if (isset($entity['password'])) {
            Validator::password($entity['password']);
            if (strlen($entity['password']) > 72) {
                throw new ValidationException("Field 'password' must be at most 72 characters long.");
            }
        }

        if (isset($entity['role'])) {
            Validator::one_of($entity['role'], 'role', Roles::all());
        }

        $same_username = $this->dao->get_by_username(trim($entity['username']));
        if ($same_username && $same_username['id'] != $id) {
            throw new ValidationException("The username '" . trim($entity['username']) . "' is already taken.");
        }
        $same_email = $this->dao->get_by_email(trim($entity['email']));
        if ($same_email && $same_email['id'] != $id) {
            throw new ValidationException('An account with this email already exists.');
        }

        if (isset($entity['bio']) && trim((string) $entity['bio']) !== '') {
            Validator::length($entity['bio'], 'bio', 1, 500);
        }
        if (isset($entity['favorite_pokemon_id']) && trim((string) $entity['favorite_pokemon_id']) !== '') {
            Validator::integer($entity['favorite_pokemon_id'], 'favorite_pokemon_id', 1);
            if (!$this->pokemon_dao->get_by_id($entity['favorite_pokemon_id'])) {
                throw new ValidationException("Field 'favorite_pokemon_id' does not match an existing Pokémon.");
            }
        }
        if (isset($entity['currently_playing_game_id']) && trim((string) $entity['currently_playing_game_id']) !== '') {
            Validator::integer($entity['currently_playing_game_id'], 'currently_playing_game_id', 1);
            if (!$this->game_dao->get_by_id($entity['currently_playing_game_id'])) {
                throw new ValidationException("Field 'currently_playing_game_id' does not match an existing game.");
            }
        }
    }

    private function prepare($entity) {
        unset($entity['password_hash']);
        foreach (['username', 'email'] as $field) {
            if (isset($entity[$field]) && is_string($entity[$field])) {
                $entity[$field] = trim($entity[$field]);
            }
        }
        return $this->empty_to_null($entity, ['bio', 'favorite_pokemon_id', 'currently_playing_game_id']);
    }

    private function without_hash($user) {
        unset($user['password_hash']);
        return $user;
    }

    private function assert_not_last_admin() {
        $admins = array_filter($this->dao->get_all(), fn($user) => $user['role'] === Roles::ADMIN);
        if (count($admins) <= 1) {
            throw new ValidationException('This is the last admin account. Make another user an admin first.');
        }
    }
}