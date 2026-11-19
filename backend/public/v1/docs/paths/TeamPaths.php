<?php
use OpenApi\Attributes as OA;

class TeamPaths
{
    #[OA\Get(
        path: '/teams',
        tags: ['teams'],
        summary: 'List all teams, public and private',
        description: 'Admin overview. Public pages should use /teams/public.',
        responses: [
            new OA\Response(response: 200, description: 'List of teams', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/Team')))
        ]
    )]
    public function getTeams() {}

    #[OA\Get(
        path: '/teams/public',
        tags: ['teams'],
        summary: 'Public teams (the "Public teams" tab)',
        parameters: [
            new OA\Parameter(name: 'limit', in: 'query', required: false, example: 20, schema: new OA\Schema(type: 'integer', minimum: 1, maximum: 100, default: 20)),
            new OA\Parameter(name: 'offset', in: 'query', required: false, example: 0, schema: new OA\Schema(type: 'integer', minimum: 0, default: 0))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Public teams, newest first', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/Team'))),
            new OA\Response(response: 400, description: 'Invalid limit or offset', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function getTeamsPublic() {}

    #[OA\Get(
        path: '/teams/{id}',
        tags: ['teams'],
        summary: 'Get one team (with owner username and game name)',
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, example: 1, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'The team', content: new OA\JsonContent(ref: '#/components/schemas/Team')),
            new OA\Response(response: 404, description: 'Team not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function getTeamsById() {}

    #[OA\Get(
        path: '/teams/{id}/members',
        tags: ['teams', 'team members'],
        summary: 'Members of a team with Pokémon details, ordered by slot',
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, example: 1, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'The members', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/TeamMemberDetail'))),
            new OA\Response(response: 404, description: 'Team not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function getTeamsByIdMembers() {}

    #[OA\Get(
        path: '/teams/{id}/comments',
        tags: ['teams', 'comments'],
        summary: 'Comments under a team, oldest first',
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, example: 1, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'The comments with the author\'s username', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/CommentWithAuthor'))),
            new OA\Response(response: 404, description: 'Team not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function getTeamsByIdComments() {}

    #[OA\Post(
        path: '/teams/{id}/members/swap',
        tags: ['teams', 'team members'],
        summary: 'Swap the Pokémon in two slots',
        description: 'Both slots must contain a Pokémon. Reload the member list afterwards: the Pokémon exchange rows, so row ids now belong to the other Pokémon.',
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, example: 1, schema: new OA\Schema(type: 'integer'))],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/SwapInput')),
        responses: [
            new OA\Response(response: 200, description: 'The members in their new order', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/TeamMemberDetail'))),
            new OA\Response(response: 400, description: 'Same slot twice, slot out of range, or an empty slot', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 404, description: 'Team not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function postTeamsByIdMembersSwap() {}

    #[OA\Post(
        path: '/teams',
        tags: ['teams'],
        summary: 'Create a team',
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/TeamInput')),
        responses: [
            new OA\Response(response: 201, description: 'Created team', content: new OA\JsonContent(ref: '#/components/schemas/Team')),
            new OA\Response(response: 400, description: 'Invalid data, unknown user or game', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function postTeams() {}

    #[OA\Put(
        path: '/teams/{id}',
        tags: ['teams'],
        summary: 'Update a team (send all required fields)',
        description: 'The owner never changes. Switching to another game is refused if a member is too new for it.',
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, example: 1, schema: new OA\Schema(type: 'integer'))],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/TeamInput')),
        responses: [
            new OA\Response(response: 200, description: 'Updated team', content: new OA\JsonContent(ref: '#/components/schemas/Team')),
            new OA\Response(response: 400, description: 'Invalid data, or members too new for the chosen game', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 404, description: 'Team not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function putTeamsById() {}

    #[OA\Delete(
        path: '/teams/{id}',
        tags: ['teams'],
        summary: 'Delete a team',
        description: 'Also deletes its members and comments.',
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, example: 1, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'Deleted', content: new OA\JsonContent(ref: '#/components/schemas/Message')),
            new OA\Response(response: 404, description: 'Team not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function deleteTeamsById() {}
}