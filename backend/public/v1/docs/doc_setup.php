<?php
use OpenApi\Attributes as OA;

#[OA\Info(
    title: 'PokéPlanner API',
    description: 'REST API of PokéPlanner – Pokémon team builder and weakness analyzer – IT 2001 Web Programming.',
    version: '1.0',
    contact: new OA\Contact(name: 'Adi Golos')
)]
#[OA\Server(url: API_BASE_URL, description: 'This server')]
#[OA\Schema(
    schema: 'Error',
    properties: [
        new OA\Property(property: 'error', type: 'string', example: 'Field \'name\' is required.')
    ]
)]
#[OA\Schema(
    schema: 'Message',
    properties: [new OA\Property(property: 'message', type: 'string', example: 'Game deleted.')]
)]
#[OA\Schema(
    schema: 'GameInput',
    required: ['name', 'generation'],
    properties: [
        new OA\Property(property: 'name', type: 'string', maxLength: 100, example: 'Pokémon Emerald'),
        new OA\Property(property: 'generation', type: 'integer', minimum: 1, maximum: 9, example: 3),
        new OA\Property(property: 'release_year', type: 'integer', nullable: true, example: 2004)
    ]
)]
#[OA\Schema(
    schema: 'Game',
    allOf: [new OA\Schema(ref: '#/components/schemas/GameInput')],
    properties: [new OA\Property(property: 'id', type: 'integer', example: 9)]
)]
#[OA\Schema(
    schema: 'PokemonInput',
    required: ['dex_number', 'name', 'generation', 'type1_id', 'hp', 'attack', 'defense', 'sp_atk', 'sp_def', 'speed'],
    properties: [
        new OA\Property(property: 'dex_number', type: 'integer', example: 6),
        new OA\Property(property: 'name', type: 'string', example: 'Charizard'),
        new OA\Property(property: 'generation', type: 'integer', minimum: 1, maximum: 9, example: 1),
        new OA\Property(property: 'type1_id', type: 'integer', example: 10),
        new OA\Property(property: 'type2_id', type: 'integer', nullable: true, example: 3, description: 'Must differ from type1_id'),
        new OA\Property(property: 'sprite_url', type: 'string', nullable: true, example: 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/6.png', description: 'http(s) URL'),
        new OA\Property(property: 'hp', type: 'integer', minimum: 1, maximum: 255, example: 78),
        new OA\Property(property: 'attack', type: 'integer', minimum: 1, maximum: 255, example: 84),
        new OA\Property(property: 'defense', type: 'integer', minimum: 1, maximum: 255, example: 78),
        new OA\Property(property: 'sp_atk', type: 'integer', minimum: 1, maximum: 255, example: 109),
        new OA\Property(property: 'sp_def', type: 'integer', minimum: 1, maximum: 255, example: 85),
        new OA\Property(property: 'speed', type: 'integer', minimum: 1, maximum: 255, example: 100)
    ]
)]
#[OA\Schema(
    schema: 'Pokemon',
    allOf: [new OA\Schema(ref: '#/components/schemas/PokemonInput')],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 6),
        new OA\Property(property: 'type1_name', type: 'string', example: 'Fire'),
        new OA\Property(property: 'type2_name', type: 'string', nullable: true, example: 'Flying')
    ]
)]
#[OA\Schema(
    schema: 'TypeInput',
    required: ['name'],
    properties: [new OA\Property(property: 'name', type: 'string', maxLength: 30, example: 'Fire')]
)]
#[OA\Schema(
    schema: 'Type',
    allOf: [new OA\Schema(ref: '#/components/schemas/TypeInput')],
    properties: [new OA\Property(property: 'id', type: 'integer', example: 10)]
)]
#[OA\Schema(
    schema: 'TypeMatchupInput',
    required: ['attacker_type_id', 'defender_type_id', 'multiplier'],
    properties: [
        new OA\Property(property: 'attacker_type_id', type: 'integer', example: 10),
        new OA\Property(property: 'defender_type_id', type: 'integer', example: 12),
        new OA\Property(property: 'multiplier', type: 'number', enum: [0, 0.5, 1, 2], example: 2)
    ]
)]
#[OA\Schema(
    schema: 'TypeMatchup',
    properties: [
        new OA\Property(property: 'attacker_type_id', type: 'integer', example: 10),
        new OA\Property(property: 'defender_type_id', type: 'integer', example: 12),
        new OA\Property(property: 'multiplier', type: 'string', example: '2.0', description: 'MySQL DECIMAL, returned as a string: 0.0, 0.5, 1.0 or 2.0')
    ]
)]
#[OA\Schema(
    schema: 'TypeAttackRow',
    allOf: [new OA\Schema(ref: '#/components/schemas/TypeMatchup')],
    properties: [new OA\Property(property: 'defender_name', type: 'string', example: 'Grass')]
)]
#[OA\Schema(
    schema: 'TypeDefenseRow',
    allOf: [new OA\Schema(ref: '#/components/schemas/TypeMatchup')],
    properties: [new OA\Property(property: 'attacker_name', type: 'string', example: 'Fire')]
)]
#[OA\Schema(
    schema: 'UserInput',
    required: ['username', 'email'],
    properties: [
        new OA\Property(property: 'username', type: 'string', minLength: 3, maxLength: 50, example: 'ash_ketchum', description: 'Letters, numbers, _ . -'),
        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'ash@example.com'),
        new OA\Property(property: 'password', type: 'string', format: 'password', example: 'Pikachu25', description: 'Required when creating, optional when updating. 8-72 characters, a letter and a number.'),
        new OA\Property(property: 'role', type: 'string', enum: ['trainer', 'admin'], example: 'trainer', description: 'Only used when creating (default trainer). Ignored on update; use PUT /users/{id}/role.'),
        new OA\Property(property: 'bio', type: 'string', maxLength: 500, nullable: true, example: 'Gotta catch them all'),
        new OA\Property(property: 'favorite_pokemon_id', type: 'integer', nullable: true, example: 25),
        new OA\Property(property: 'currently_playing_game_id', type: 'integer', nullable: true, example: 9)
    ]
)]
#[OA\Schema(
    schema: 'User',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 2),
        new OA\Property(property: 'username', type: 'string', example: 'ash_ketchum'),
        new OA\Property(property: 'email', type: 'string', example: 'ash@example.com'),
        new OA\Property(property: 'role', type: 'string', enum: ['trainer', 'admin'], example: 'trainer'),
        new OA\Property(property: 'bio', type: 'string', nullable: true),
        new OA\Property(property: 'favorite_pokemon_id', type: 'integer', nullable: true, example: 25),
        new OA\Property(property: 'currently_playing_game_id', type: 'integer', nullable: true, example: 9),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time')
    ]
)]
#[OA\Schema(
    schema: 'UserProfile',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 2),
        new OA\Property(property: 'username', type: 'string', example: 'ash_ketchum'),
        new OA\Property(property: 'email', type: 'string', example: 'ash@example.com'),
        new OA\Property(property: 'role', type: 'string', example: 'trainer'),
        new OA\Property(property: 'bio', type: 'string', nullable: true),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'favorite_pokemon_name', type: 'string', nullable: true, example: 'Pikachu'),
        new OA\Property(property: 'favorite_pokemon_sprite', type: 'string', nullable: true),
        new OA\Property(property: 'currently_playing_game_name', type: 'string', nullable: true, example: 'Pokémon Emerald')
    ]
)]
#[OA\Schema(
    schema: 'RoleInput',
    required: ['role'],
    properties: [new OA\Property(property: 'role', type: 'string', enum: ['trainer', 'admin'], example: 'admin')]
)]
#[OA\Schema(
    schema: 'TeamInput',
    required: ['user_id', 'game_id', 'name'],
    properties: [
        new OA\Property(property: 'user_id', type: 'integer', example: 2, description: 'Owner. Ignored on update (the owner never changes).'),
        new OA\Property(property: 'game_id', type: 'integer', example: 9),
        new OA\Property(property: 'name', type: 'string', maxLength: 100, example: 'Hoenn Nuzlocke'),
        new OA\Property(property: 'challenge', type: 'string', maxLength: 100, nullable: true, example: 'Nuzlocke'),
        new OA\Property(property: 'description', type: 'string', maxLength: 1000, nullable: true),
        new OA\Property(property: 'is_public', type: 'boolean', example: true, description: 'Default false (private)')
    ]
)]
#[OA\Schema(
    schema: 'Team',
    allOf: [new OA\Schema(ref: '#/components/schemas/TeamInput')],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'is_public', type: 'integer', example: 1, description: '0 or 1'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'username', type: 'string', example: 'ash_ketchum', description: 'Owner (not in GET /teams)'),
        new OA\Property(property: 'game_name', type: 'string', example: 'Pokémon Emerald', description: 'Not in GET /teams')
    ]
)]
#[OA\Schema(
    schema: 'TeamMemberInput',
    required: ['pokemon_id', 'team_id'],
    properties: [
        new OA\Property(property: 'pokemon_id', type: 'integer', example: 6),
        new OA\Property(property: 'team_id', type: 'integer', example: 1, description: 'Ignored on update (a member never moves to another team)'),
        new OA\Property(property: 'slot', type: 'integer', minimum: 1, maximum: 6, example: 1, description: 'Optional when creating (first free slot is used); required when updating'),
        new OA\Property(property: 'nickname', type: 'string', maxLength: 50, nullable: true, example: 'Zard')
    ]
)]
#[OA\Schema(
    schema: 'TeamMember',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'pokemon_id', type: 'integer', example: 6),
        new OA\Property(property: 'team_id', type: 'integer', example: 1),
        new OA\Property(property: 'slot', type: 'integer', example: 1),
        new OA\Property(property: 'nickname', type: 'string', nullable: true)
    ]
)]
#[OA\Schema(
    schema: 'TeamMemberDetail',
    allOf: [new OA\Schema(ref: '#/components/schemas/TeamMember')],
    properties: [
        new OA\Property(property: 'dex_number', type: 'integer', example: 6),
        new OA\Property(property: 'pokemon_name', type: 'string', example: 'Charizard'),
        new OA\Property(property: 'sprite_url', type: 'string', nullable: true),
        new OA\Property(property: 'hp', type: 'integer', example: 78),
        new OA\Property(property: 'attack', type: 'integer', example: 84),
        new OA\Property(property: 'defense', type: 'integer', example: 78),
        new OA\Property(property: 'sp_atk', type: 'integer', example: 109),
        new OA\Property(property: 'sp_def', type: 'integer', example: 85),
        new OA\Property(property: 'speed', type: 'integer', example: 100),
        new OA\Property(property: 'type1_id', type: 'integer', example: 10),
        new OA\Property(property: 'type2_id', type: 'integer', nullable: true, example: 3),
        new OA\Property(property: 'type1_name', type: 'string', example: 'Fire'),
        new OA\Property(property: 'type2_name', type: 'string', nullable: true, example: 'Flying')
    ]
)]
#[OA\Schema(
    schema: 'SwapInput',
    required: ['slot_a', 'slot_b'],
    properties: [
        new OA\Property(property: 'slot_a', type: 'integer', minimum: 1, maximum: 6, example: 1),
        new OA\Property(property: 'slot_b', type: 'integer', minimum: 1, maximum: 6, example: 3)
    ]
)]
#[OA\Schema(
    schema: 'CommentInput',
    required: ['team_id', 'user_id', 'body'],
    properties: [
        new OA\Property(property: 'team_id', type: 'integer', example: 1, description: 'Must be a public team; ignored on update'),
        new OA\Property(property: 'user_id', type: 'integer', example: 3, description: 'Author; ignored on update'),
        new OA\Property(property: 'body', type: 'string', maxLength: 1000, example: 'Great team!')
    ]
)]
#[OA\Schema(
    schema: 'Comment',
    allOf: [new OA\Schema(ref: '#/components/schemas/CommentInput')],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time')
    ]
)]
#[OA\Schema(
    schema: 'CommentWithAuthor',
    allOf: [new OA\Schema(ref: '#/components/schemas/Comment')],
    properties: [new OA\Property(property: 'username', type: 'string', example: 'misty')]
)]
class OpenApiSpec {}