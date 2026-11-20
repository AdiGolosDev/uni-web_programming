<?php
use OpenApi\Attributes as OA;

class TeamMemberPaths
{
    #[OA\Get(
        path: '/team-members',
        tags: ['team members'],
        summary: 'List all team members of all teams',
        responses: [
            new OA\Response(response: 200, description: 'List of team members', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/TeamMember')))
        ]
    )]
    public function getTeamMembers() {}

    #[OA\Get(
        path: '/team-members/{id}',
        tags: ['team members'],
        summary: 'Get one team member',
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, example: 1, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'The team member', content: new OA\JsonContent(ref: '#/components/schemas/TeamMember')),
            new OA\Response(response: 404, description: 'Team member not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function getTeamMembersById() {}

    #[OA\Post(
        path: '/team-members',
        tags: ['team members'],
        summary: 'Add a Pokémon to a team',
        description: 'The Pokémon must not be newer than the team\'s game. Without a slot, the first free slot is used. The same Pokémon may appear in several slots.',
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/TeamMemberInput')),
        responses: [
            new OA\Response(response: 201, description: 'Created team member', content: new OA\JsonContent(ref: '#/components/schemas/TeamMember')),
            new OA\Response(response: 400, description: 'Invalid data, Pokémon too new for the game, slot taken, or the team is full', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function postTeamMembers() {}

    #[OA\Put(
        path: '/team-members/{id}',
        tags: ['team members'],
        summary: 'Change the Pokémon, slot or nickname of a member',
        description: 'The team never changes. To exchange two members, use POST /teams/{id}/members/swap.',
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, example: 1, schema: new OA\Schema(type: 'integer'))],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/TeamMemberInput')),
        responses: [
            new OA\Response(response: 200, description: 'Updated team member', content: new OA\JsonContent(ref: '#/components/schemas/TeamMember')),
            new OA\Response(response: 400, description: 'Invalid data, Pokémon too new for the game, or slot taken', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 404, description: 'Team member not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function putTeamMembersById() {}

    #[OA\Delete(
        path: '/team-members/{id}',
        tags: ['team members'],
        summary: 'Remove a Pokémon from a team',
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, example: 1, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'Deleted', content: new OA\JsonContent(ref: '#/components/schemas/Message')),
            new OA\Response(response: 404, description: 'Team member not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function deleteTeamMembersById() {}
}