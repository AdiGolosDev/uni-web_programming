<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/TeamDao.php';
require_once __DIR__ . '/../dao/UserDao.php';
require_once __DIR__ . '/../dao/GameDao.php';
require_once __DIR__ . '/../dao/TeamMemberDao.php';
require_once __DIR__ . '/../dao/PokemonDao.php';

class TeamService extends BaseService {
    protected $entity_name = 'Team';
    private $user_dao;
    private $game_dao;
    private $member_dao;
    private $pokemon_dao;

    public function __construct() {
        parent::__construct(new TeamDao());
        $this->user_dao = new UserDao();
        $this->game_dao = new GameDao();
        $this->member_dao = new TeamMemberDao();
        $this->pokemon_dao = new PokemonDao();
    }

    public function get_by_id($id) {
        $team = $this->dao->get_details($id);
        if (!$team) {
            throw new NotFoundException("{$this->entity_name} not found.");     // %%% the 404 pattern
        }
        return $team;
    }

    public function get_public($limit = 20, $offset = 0) {
        Validator::integer($limit, 'limit', 1, 100);        // RULE: at most 100 per page
        Validator::integer($offset, 'offset', 0);           // RULE: offset cannot be negative
        return $this->dao->get_public((int) $limit, (int) $offset);
    }

    public function get_by_user($user_id) {
        Validator::integer($user_id, 'user_id', 1);
        if (!$this->user_dao->get_by_id($user_id)) {
            throw new NotFoundException('User not found.');
        }
        return $this->dao->get_by_user($user_id);
    }

    public function add($entity) {
        $created = parent::add($this->empty_to_null($entity, ['challenge', 'description']));
        return $this->dao->get_details($created['id']);
    }

    public function update($entity, $id) {
        $current = $this->get_by_id($id);
        $entity['user_id'] = $current['user_id'];
        parent::update($this->empty_to_null($entity, ['challenge', 'description']), $id);
        return $this->dao->get_details($id);
    }

    protected function validate($entity, $id = null) {
        Validator::required($entity, ['user_id', 'game_id', 'name']);
        Validator::length($entity['name'], 'name', 1, 100);
        Validator::integer($entity['user_id'], 'user_id', 1);
        Validator::integer($entity['game_id'], 'game_id', 1);

        if (!$this->user_dao->get_by_id($entity['user_id'])) {
            throw new ValidationException("Field 'user_id' does not match an existing user.");
        }
        $game = $this->game_dao->get_by_id($entity['game_id']);
        if (!$game) {
            throw new ValidationException("Field 'game_id' does not match an existing game.");
        }

        if (isset($entity['challenge']) && trim((string) $entity['challenge']) !== '') {
            Validator::length($entity['challenge'], 'challenge', 1, 100);
        }
        if (isset($entity['description']) && trim((string) $entity['description']) !== '') {
            Validator::length($entity['description'], 'description', 1, 1000);
        }

        if (array_key_exists('is_public', $entity)
            && !in_array($entity['is_public'], [true, false, 0, 1, '0', '1'], true)) {
            throw new ValidationException("Field 'is_public' must be true or false.");
        }

        if ($id !== null) {
            $current = $this->dao->get_by_id($id);
            if ($current['game_id'] != $entity['game_id']) {
                $too_new = $this->count_members_newer_than($id, (int) $game['generation']);
                if ($too_new > 0) {
                    throw new ValidationException(
                        "Cannot switch to {$game['name']}: $too_new Pokémon in this team are too new for that game. Remove them first.");
                }
            }
        }
    }

    private function count_members_newer_than($team_id, $generation) {
        $count = 0;
        foreach ($this->member_dao->get_by_team($team_id) as $member) {
            $pokemon = $this->pokemon_dao->get_by_id($member['pokemon_id']);
            if ((int) $pokemon['generation'] > $generation) {
                $count++;
            }
        }
        return $count;
    }
}