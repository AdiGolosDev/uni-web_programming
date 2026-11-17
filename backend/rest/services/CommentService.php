<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/CommentDao.php';
require_once __DIR__ . '/../dao/TeamDao.php';
require_once __DIR__ . '/../dao/UserDao.php';

class CommentService extends BaseService {
    protected $entity_name = 'Comment';
    private $team_dao;
    private $user_dao;

    public function __construct() {
        parent::__construct(new CommentDao());
        $this->team_dao = new TeamDao();
        $this->user_dao = new UserDao();
    }

    public function get_by_team($team_id){
        Validator::integer($team_id, 'team_id', 1);
        if (!$this->team_dao->get_by_id($team_id)) {
            throw new NotFoundException('Team not found.');
        }
        return $this->dao->get_by_team($team_id);
    }

    public function get_by_user($user_id) {
        Validator::integer($user_id, 'user_id', 1);
        if (!$this->user_dao->get_by_id($user_id)) {
            throw new NotFoundException('User not found.');
        }
        return $this->dao->get_by_user($user_id);
    }

    public function update($entity, $id) {
        $current = $this->get_by_id($id);
        $entity['team_id'] = $current['team_id'];
        $entity['user_id'] = $current['user_id'];
        return parent::update($entity, $id);
    }

    protected function validate($entity, $id = null) {
        Validator::required($entity, ['team_id', 'user_id', 'body']);
        Validator::integer($entity['team_id'], 'team_id', 1);
        Validator::integer($entity['user_id'], 'user_id', 1);
        Validator::length($entity['body'], 'body', 1, 1000);

        $team = $this->team_dao->get_by_id($entity['team_id']);
        if (!$team) {
            throw new ValidationException("Field 'team_id' does not match an existing team.");
        }
        if (!$this->user_dao->get_by_id($entity['user_id'])) {
            throw new ValidationException("Field 'user_id' does not match an existing user.");
        }

        if ($id === null && !$team['is_public']) {
            throw new ValidationException('Comments can only be added to public teams.');
        }
    }
}