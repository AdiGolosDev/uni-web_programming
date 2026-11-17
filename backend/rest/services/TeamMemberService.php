<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/TeamMemberDao.php';
require_once __DIR__ . '/../dao/TeamDao.php';
require_once __DIR__ . '/../dao/PokemonDao.php';
require_once __DIR__ . '/../dao/GameDao.php';

class TeamMemberService extends BaseService {
    protected $entity_name = 'Team member';
    private $team_dao;
    private $pokemon_dao;
    private $game_dao;

    public function __construct() {
        parent::__construct(new TeamMemberDao());
        $this->team_dao = new TeamDao();
        $this->pokemon_dao = new PokemonDao();
        $this->game_dao = new GameDao();
    }

    public function get_by_team($team_id) {
        Validator::integer($team_id, 'team_id', 1);
        if (!$this->team_dao->get_by_id($team_id)) {
            throw new NotFoundException('Team not found.');
        }
        return $this->dao->get_by_team($team_id);
    }

    public function add($entity) {
        if (!isset($entity['slot']) || trim((string) $entity['slot']) === '') {
            if (isset($entity['team_id'])) {
                Validator::integer($entity['team_id'], 'team_id', 1);
                $entity['slot'] = $this->dao->next_free_slot($entity['team_id']);
                if ($entity['slot'] === null) {
                    throw new ValidationException('This team already has 6 Pokémon.');
                }
            }
        }
        return parent::add($this->empty_to_null($entity, ['nickname']));
    }

    public function update($entity, $id) {
        $current = $this->get_by_id($id);
        $entity['team_id'] = $current['team_id'];
        return parent::update($this->empty_to_null($entity, ['nickname']), $id);
    }

    protected function validate($entity, $id = null) {
        Validator::required($entity, ['pokemon_id', 'team_id', 'slot']);
        Validator::integer($entity['pokemon_id'], 'pokemon_id', 1);
        Validator::integer($entity['team_id'], 'team_id', 1);
        Validator::integer($entity['slot'], 'slot', 1, 6);

        $team = $this->team_dao->get_by_id($entity['team_id']);
        if (!$team) {
            throw new ValidationException("Field 'team_id' does not match an existing team.");
        }
        $pokemon = $this->pokemon_dao->get_by_id($entity['pokemon_id']);
        if (!$pokemon) {
            throw new ValidationException("Field 'pokemon_id' does not match an existing Pokémon.");
        }

        $game = $this->game_dao->get_by_id($team['game_id']);
        if ((int) $pokemon['generation'] > (int) $game['generation']) {
            throw new ValidationException(
                "{$pokemon['name']} (generation {$pokemon['generation']}) is not available in {$game['name']} (generation {$game['generation']}).");
        }

        foreach ($this->dao->get_by_team($entity['team_id']) as $member) {
            if ((int) $member['slot'] === (int) $entity['slot'] && $member['id'] != $id) {
                throw new ValidationException("Slot {$entity['slot']} of this team is already taken.");
            }
        }

        if (isset($entity['nickname']) && trim((string) $entity['nickname']) !== '') {
            Validator::length($entity['nickname'], 'nickname', 1, 50);
        }
    }
}