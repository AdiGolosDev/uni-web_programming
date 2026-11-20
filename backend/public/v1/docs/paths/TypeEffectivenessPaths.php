<?php
use OpenApi\Attributes as OA;

class TypeEffectivenessPaths
{
    #[OA\Get(
        path: '/type-effectiveness',
        tags: ['type chart'],
        summary: 'The whole type chart',
        responses: [
            new OA\Response(response: 200, description: 'All attacker/defender pairs', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/TypeMatchup')))
        ]
    )]
    public function getTypeEffectiveness() {}

    #[OA\Get(
        path: '/type-effectiveness/{attacker}/{defender}',
        tags: ['type chart'],
        summary: 'One cell of the type chart',
        parameters: [
            new OA\Parameter(name: 'attacker', in: 'path', required: true, example: 10, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'defender', in: 'path', required: true, example: 12, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'The matchup', content: new OA\JsonContent(ref: '#/components/schemas/TypeMatchup')),
            new OA\Response(response: 404, description: 'Matchup not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function getTypeEffectivenessPair() {}

    #[OA\Post(
        path: '/type-effectiveness',
        tags: ['type chart'],
        summary: 'Add a matchup',
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/TypeMatchupInput')),
        responses: [
            new OA\Response(response: 201, description: 'Created matchup', content: new OA\JsonContent(ref: '#/components/schemas/TypeMatchup')),
            new OA\Response(response: 400, description: 'Invalid data, unknown type or pair already exists', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function postTypeEffectiveness() {}

    #[OA\Put(
        path: '/type-effectiveness/{attacker}/{defender}',
        tags: ['type chart'],
        summary: 'Change the multiplier of a matchup',
        parameters: [
            new OA\Parameter(name: 'attacker', in: 'path', required: true, example: 10, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'defender', in: 'path', required: true, example: 12, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['multiplier'],
                properties: [new OA\Property(property: 'multiplier', type: 'number', enum: [0, 0.5, 1, 2], example: 2)]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Updated matchup', content: new OA\JsonContent(ref: '#/components/schemas/TypeMatchup')),
            new OA\Response(response: 400, description: 'Invalid multiplier', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 404, description: 'Matchup not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function putTypeEffectivenessPair() {}

    #[OA\Delete(
        path: '/type-effectiveness/{attacker}/{defender}',
        tags: ['type chart'],
        summary: 'Delete a matchup',
        parameters: [
            new OA\Parameter(name: 'attacker', in: 'path', required: true, example: 10, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'defender', in: 'path', required: true, example: 12, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Deleted', content: new OA\JsonContent(ref: '#/components/schemas/Message')),
            new OA\Response(response: 404, description: 'Matchup not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function deleteTypeEffectivenessPair() {}
}