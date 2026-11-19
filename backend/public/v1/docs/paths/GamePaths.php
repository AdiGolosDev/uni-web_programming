<?php
use OpenApi\Attributes as OA;

class GamePaths
{
    #[OA\Get(
        path: '/games',
        tags: ['games'],
        summary: 'List all games, optionally of one generation',
        parameters: [
            new OA\Parameter(
                name: 'generation',
                in: 'query',
                required: false,
                example: 3,
                schema: new OA\Schema(type: 'integer', minimum: 1, maximum: 9)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of games',
                content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/Game'))
            ),
            new OA\Response(
                response: 400,
                description: 'Invalid generation',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            )
        ]
    )]
    public function getGames() {}

    #[OA\Get(
        path: '/games/{id}',
        tags: ['games'],
        summary: 'Get one game',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 9, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'The game',
                content: new OA\JsonContent(ref: '#/components/schemas/Game')
            ),
            new OA\Response(
                response: 404,
                description: 'Game not found',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            )
        ]
    )]
    public function getGamesById() {}

    #[OA\Post(
        path: '/games',
        tags: ['games'],
        summary: 'Create a game',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/GameInput')
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Created game',
                content: new OA\JsonContent(ref: '#/components/schemas/Game')
            ),
            new OA\Response(
                response: 400,
                description: 'Invalid data or name already exists',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            )
        ]
    )]
    public function postGames() {}

    #[OA\Put(
        path: '/games/{id}',
        tags: ['games'],
        summary: 'Update a game (send all required fields)',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 9, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/GameInput')
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Updated game',
                content: new OA\JsonContent(ref: '#/components/schemas/Game')
            ),
            new OA\Response(
                response: 400,
                description: 'Invalid data or name already exists',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            ),
            new OA\Response(
                response: 404,
                description: 'Game not found',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            )
        ]
    )]
    public function putGamesById() {}

    #[OA\Delete(
        path: '/games/{id}',
        tags: ['games'],
        summary: 'Delete a game',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 38, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Deleted',
                content: new OA\JsonContent(ref: '#/components/schemas/Message')
            ),
            new OA\Response(
                response: 400,
                description: 'The game is used by existing teams',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            ),
            new OA\Response(
                response: 404,
                description: 'Game not found',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            )
        ]
    )]
    public function deleteGamesById() {}
}