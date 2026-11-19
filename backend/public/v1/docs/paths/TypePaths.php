<?php
use OpenApi\Attributes as OA;

class TypePaths
{
    #[OA\Get(
        path: '/types',
        tags: ['types'],
        summary: 'List all types',
        responses: [
            new OA\Response(response: 200, description: 'List of types', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/Type')))
        ]
    )]
    public function getTypes() {}

    #[OA\Get(
        path: '/types/{id}',
        tags: ['types'],
        summary: 'Get one type',
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, example: 10, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'The type', content: new OA\JsonContent(ref: '#/components/schemas/Type')),
            new OA\Response(response: 404, description: 'Type not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function getTypesById() {}

    #[OA\Get(
        path: '/types/{id}/attacks',
        tags: ['types', 'type chart'],
        summary: 'What one attacking type does to every defending type',
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, example: 10, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'One row per defending type', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/TypeAttackRow'))),
            new OA\Response(response: 404, description: 'Type not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function getTypesByIdAttacks() {}

    #[OA\Get(
        path: '/types/{id}/defenses',
        tags: ['types', 'type chart'],
        summary: 'What every attacking type does to one defending type',
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, example: 8, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'One row per attacking type', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/TypeDefenseRow'))),
            new OA\Response(response: 404, description: 'Type not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function getTypesByIdDefenses() {}

    #[OA\Post(
        path: '/types',
        tags: ['types'],
        summary: 'Create a type',
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/TypeInput')),
        responses: [
            new OA\Response(response: 201, description: 'Created type', content: new OA\JsonContent(ref: '#/components/schemas/Type')),
            new OA\Response(response: 400, description: 'Invalid data or name already exists', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function postTypes() {}

    #[OA\Put(
        path: '/types/{id}',
        tags: ['types'],
        summary: 'Update a type',
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, example: 19, schema: new OA\Schema(type: 'integer'))],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/TypeInput')),
        responses: [
            new OA\Response(response: 200, description: 'Updated type', content: new OA\JsonContent(ref: '#/components/schemas/Type')),
            new OA\Response(response: 400, description: 'Invalid data or name already exists', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 404, description: 'Type not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function putTypesById() {}

    #[OA\Delete(
        path: '/types/{id}',
        tags: ['types'],
        summary: 'Delete a type',
        description: 'Also deletes the type\'s rows in the type chart.',
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, example: 19, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'Deleted', content: new OA\JsonContent(ref: '#/components/schemas/Message')),
            new OA\Response(response: 400, description: 'The type is used by existing Pokémon', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 404, description: 'Type not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function deleteTypesById() {}
}