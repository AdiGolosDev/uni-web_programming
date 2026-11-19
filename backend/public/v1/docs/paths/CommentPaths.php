<?php
use OpenApi\Attributes as OA;

class CommentPaths
{
    #[OA\Get(
        path: '/comments',
        tags: ['comments'],
        summary: 'List all comments',
        responses: [
            new OA\Response(response: 200, description: 'List of comments', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/Comment')))
        ]
    )]
    public function getComments() {}

    #[OA\Get(
        path: '/comments/{id}',
        tags: ['comments'],
        summary: 'Get one comment',
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, example: 1, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'The comment', content: new OA\JsonContent(ref: '#/components/schemas/Comment')),
            new OA\Response(response: 404, description: 'Comment not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function getCommentsById() {}

    #[OA\Post(
        path: '/comments',
        tags: ['comments'],
        summary: 'Comment on a public team',
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/CommentInput')),
        responses: [
            new OA\Response(response: 201, description: 'Created comment', content: new OA\JsonContent(ref: '#/components/schemas/Comment')),
            new OA\Response(response: 400, description: 'Invalid data, unknown team or user, or the team is private', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function postComments() {}

    #[OA\Put(
        path: '/comments/{id}',
        tags: ['comments'],
        summary: 'Edit the text of a comment',
        description: 'Only the body changes; the team and the author stay the same.',
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, example: 1, schema: new OA\Schema(type: 'integer'))],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/CommentInput')),
        responses: [
            new OA\Response(response: 200, description: 'Updated comment', content: new OA\JsonContent(ref: '#/components/schemas/Comment')),
            new OA\Response(response: 400, description: 'Invalid data', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 404, description: 'Comment not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function putCommentsById() {}

    #[OA\Delete(
        path: '/comments/{id}',
        tags: ['comments'],
        summary: 'Delete a comment',
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, example: 1, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'Deleted', content: new OA\JsonContent(ref: '#/components/schemas/Message')),
            new OA\Response(response: 404, description: 'Comment not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function deleteCommentsById() {}
}