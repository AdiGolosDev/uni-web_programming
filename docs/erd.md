# PokéPlanner ER Diagram

```mermaid
erDiagram
    USER_ROLE {
        string trainer
        string admin
    }

    USERS {
        int id PK
        varchar username UK
        varchar email UK
        varchar password_hash
        user_role role
        varchar bio "nullable"
        int favorite_pokemon_id FK "nullable"
        int currently_playing_game_id FK "nullable"
        datetime created_at
    }

    GAMES {
        int id PK
        varchar name UK
        tinyint generation
        smallint release_year "nullable"
    }

    TYPES {
        int id PK
        varchar name UK
    }

    TYPE_EFFECTIVENESS {
        int attacker_type_id PK, FK
        int defender_type_id PK, FK
        decimal multiplier
    }

    POKEMON {
        int id PK
        int dex_number UK
        varchar name
        tinyint generation
        int type1_id FK
        int type2_id FK "nullable"
        varchar sprite_url "nullable"
        smallint hp
        smallint attack
        smallint defense
        smallint sp_atk
        smallint sp_def
        smallint speed
    }

    TEAMS {
        int id PK
        int user_id FK
        int game_id FK
        varchar name
        varchar challenge "nullable"
        text description "nullable"
        boolean is_public
        datetime created_at
        datetime updated_at
    }

    TEAM_MEMBERS {
        int id PK
        int pokemon_id FK
        int team_id FK
        tinyint slot
        varchar nickname "nullable"
    }

    COMMENTS {
        int id PK
        int team_id FK
        int user_id FK
        text body
        datetime created_at
        datetime updated_at
    }

    USERS o|--o{ POKEMON : "favorite Pokémon"
    USERS o|--o| GAMES : "currently playing"
    TYPES ||--o{ POKEMON : "primary type"
    TYPES o|--o{ POKEMON : "secondary type"
    TYPES ||--o{ TYPE_EFFECTIVENESS : "attacker type"
    TYPES ||--o{ TYPE_EFFECTIVENESS : "defender type"
    USERS ||--o{ TEAMS : owns
    GAMES ||--o{ TEAMS : "built for"
    TEAMS ||--o{ TEAM_MEMBERS : contains
    POKEMON ||--o{ TEAM_MEMBERS : appears_in
    TEAMS ||--o{ COMMENTS : has
    USERS ||--o{ COMMENTS : writes
```

## Notes

- `USER_ROLE` is shown as a conceptual enum; Mermaid ER diagrams do not model enum values as a database entity. The allowed values are `trainer` and `admin`.
- `TEAM_MEMBERS` is the join table between `TEAMS` and `POKEMON`. A Pokémon may appear more than once in a team, while each team slot is unique within that team.
- The SQL constraints described in the schema (such as valid generations, slots 1–6, distinct primary/secondary types, and allowed effectiveness multipliers) should be enforced in the actual database.
- The original final `team_members.pokemon_id <> team_members.team_id` reference is not a valid relationship between those columns; the diagram instead models the intended foreign keys to `POKEMON` and `TEAMS`.
