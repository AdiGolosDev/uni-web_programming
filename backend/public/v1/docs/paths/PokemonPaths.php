<?php
use OpenApi\Attributes as OA;

class PokemonPaths
{
    #[OA\Get(
        path: '/pokemon',
        tags: ['pokemon'],
        summary: 'List all Pokémon, or filter by name, type or generation',
        description: 'Use at most one filter. If several are sent, q wins, then type_id, then generation.',
        parameters: [
            new OA\Parameter(name: 'q', in: 'query', required: false, example: 'char', description: 'Part of the name', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'type_id', in: 'query', required: false, example: 10, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'generation', in: 'query', required: false, example: 3, schema: new OA\Schema(type: 'integer', minimum: 1, maximum: 9))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of Pokémon with their type names',
                content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/Pokemon'))
            ),
            new OA\Response(
                response: 400,
                description: 'Invalid filter value',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            )
        ]
    )]
    public function getPokemon() {}

    #[OA\Get(
        path: '/pokemon/{id}',
        tags: ['pokemon'],
        summary: 'Get one Pokémon',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 6, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'The Pokémon',
                content: new OA\JsonContent(ref: '#/components/schemas/Pokemon')
            ),
            new OA\Response(
                response: 404,
                description: 'Pokémon not found',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            )
        ]
    )]
    public function getPokemonById() {}

    #[OA\Post(
        path: '/pokemon',
        tags: ['pokemon'],
        summary: 'Create a Pokémon',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/PokemonInput')
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Created Pokémon',
                content: new OA\JsonContent(ref: '#/components/schemas/Pokemon')
            ),
            new OA\Response(
                response: 400,
                description: 'Invalid data, unknown type or dex number already exists',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            )
        ]
    )]
    public function postPokemon() {}

    #[OA\Put(
        path: '/pokemon/{id}',
        tags: ['pokemon'],
        summary: 'Update a Pokémon (send all required fields)',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 6, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/PokemonInput')
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Updated Pokémon',
                content: new OA\JsonContent(ref: '#/components/schemas/Pokemon')
            ),
            new OA\Response(
                response: 400,
                description: 'Invalid data, unknown type or dex number already exists',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            ),
            new OA\Response(
                response: 404,
                description: 'Pokémon not found',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            )
        ]
    )]
    public function putPokemonById() {}

    #[OA\Delete(
        path: '/pokemon/{id}',
        tags: ['pokemon'],
        summary: 'Delete a Pokémon',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 1026, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Deleted',
                content: new OA\JsonContent(ref: '#/components/schemas/Message')
            ),
            new OA\Response(
                response: 400,
                description: 'The Pokémon is used in existing teams',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            ),
            new OA\Response(
                response: 404,
                description: 'Pokémon not found',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            )
        ]
    )]
    public function deletePokemonById() {}
    
        #[OA\Get(
        path: '/pokemon/{id}/details',
        tags: ['pokemon', 'external API'],
        summary: 'Extra details from PokéAPI (height, weight, abilities, description, evolutions, artwork)',
        description: 'Uses PokéAPI through the backend (5 second timeout). Results are cached in database/pokemon_details.json. If only some parts fail, the rest is returned and the failed parts are listed in "missing".',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 6, description: 'Pokémon id in our database', schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'The details', content: new OA\JsonContent(ref: '#/components/schemas/PokemonDetails')),
            new OA\Response(response: 404, description: 'Pokémon not found', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 503, description: 'PokéAPI is not available (every call failed)', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function getPokemonByIdDetails() {}
}