SET NAMES utf8mb4;

DROP DATABASE IF EXISTS pokeplanner;
CREATE DATABASE pokeplanner CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE pokeplanner;

CREATE TABLE games (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    name         VARCHAR(100) NOT NULL UNIQUE,
    generation   TINYINT      NOT NULL,
    release_year SMALLINT     NULL,
    CONSTRAINT chk_games_generation CHECK (generation BETWEEN 1 AND 9)
);

CREATE TABLE types (
    id    INT AUTO_INCREMENT PRIMARY KEY,
    name  VARCHAR(30) NOT NULL UNIQUE
);

CREATE TABLE type_effectiveness (
    attacker_type_id INT          NOT NULL,
    defender_type_id INT          NOT NULL,
    multiplier       DECIMAL(2,1) NOT NULL,                -- 0, 0.5, 1 or 2
    PRIMARY KEY (attacker_type_id, defender_type_id),
    CONSTRAINT fk_te_attacker FOREIGN KEY (attacker_type_id) REFERENCES types(id) ON DELETE CASCADE,
    CONSTRAINT fk_te_defender FOREIGN KEY (defender_type_id) REFERENCES types(id) ON DELETE CASCADE,
    CONSTRAINT chk_te_multiplier CHECK (multiplier IN (0.0, 0.5, 1.0, 2.0))
);

CREATE TABLE pokemon (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    dex_number  INT          NOT NULL UNIQUE,
    name        VARCHAR(100) NOT NULL,
    generation  TINYINT      NOT NULL,
    type1_id    INT          NOT NULL,
    type2_id    INT          NULL,                         -- NULL for single-type Pokémon
    sprite_url  VARCHAR(255) NULL,
    hp          SMALLINT     NOT NULL,
    attack      SMALLINT     NOT NULL,
    defense     SMALLINT     NOT NULL,
    sp_atk      SMALLINT     NOT NULL,
    sp_def      SMALLINT     NOT NULL,
    speed       SMALLINT     NOT NULL,
    CONSTRAINT fk_pokemon_type1 FOREIGN KEY (type1_id) REFERENCES types(id) ON DELETE RESTRICT,
    CONSTRAINT fk_pokemon_type2 FOREIGN KEY (type2_id) REFERENCES types(id) ON DELETE RESTRICT,
    CONSTRAINT chk_pokemon_generation CHECK (generation BETWEEN 1 AND 9),
    CONSTRAINT chk_pokemon_types CHECK (type2_id IS NULL OR type1_id <> type2_id),
    CONSTRAINT chk_pokemon_stats CHECK (
        hp      BETWEEN 1 AND 255 AND
        attack  BETWEEN 1 AND 255 AND
        defense BETWEEN 1 AND 255 AND
        sp_atk  BETWEEN 1 AND 255 AND
        sp_def  BETWEEN 1 AND 255 AND
        speed   BETWEEN 1 AND 255
    )
);

CREATE TABLE users (
    id                        INT AUTO_INCREMENT PRIMARY KEY,
    username                  VARCHAR(50)  NOT NULL UNIQUE,
    email                     VARCHAR(100) NOT NULL UNIQUE,
    password_hash             VARCHAR(255) NOT NULL,
    role                      ENUM('trainer', 'admin') NOT NULL DEFAULT 'trainer',
    bio                       VARCHAR(500) NULL,
    favorite_pokemon_id       INT          NULL,
    currently_playing_game_id INT          NULL,
    created_at                TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_users_favorite_pokemon FOREIGN KEY (favorite_pokemon_id)       REFERENCES pokemon(id) ON DELETE SET NULL,
    CONSTRAINT fk_users_current_game     FOREIGN KEY (currently_playing_game_id) REFERENCES games(id)   ON DELETE SET NULL
);

CREATE TABLE teams (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT          NOT NULL,
    game_id     INT          NOT NULL,
    name        VARCHAR(100) NOT NULL,
    challenge   VARCHAR(100) NULL,
    description TEXT         NULL,
    is_public   BOOLEAN      NOT NULL DEFAULT FALSE,
    created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_teams_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_teams_game FOREIGN KEY (game_id) REFERENCES games(id) ON DELETE RESTRICT
);

CREATE TABLE team_members (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    pokemon_id  INT         NOT NULL,
    team_id     INT         NOT NULL,
    slot        TINYINT     NOT NULL,
    nickname    VARCHAR(50) NULL,
    CONSTRAINT fk_team_members_pokemon FOREIGN KEY (pokemon_id) REFERENCES pokemon(id) ON DELETE RESTRICT,
    CONSTRAINT fk_team_members_team    FOREIGN KEY (team_id)    REFERENCES teams(id)   ON DELETE CASCADE,
    CONSTRAINT uq_team_members_team_slot UNIQUE (team_id, slot),   -- one Pokémon per slot in a team
    CONSTRAINT chk_team_members_slot CHECK (slot BETWEEN 1 AND 6)
);

CREATE TABLE comments (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    team_id     INT  NOT NULL,
    user_id     INT  NOT NULL,
    body        TEXT NOT NULL,
    created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_comments_team FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE CASCADE,
    CONSTRAINT fk_comments_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);