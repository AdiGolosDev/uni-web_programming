# PokéPlanner

A Pokémon team builder and weakness analyzer. Build a team of up to 6 Pokémon for a specific game (like Pokémon Emerald Version) and instantly see what it is weak to and where it has gaps. Trainers can share teams publicly and comment on each other's teams, and anyone can browse the Pokédex without an account.

**Author:** Adi Golos

**Course:** Web Programming

**Status:** Milestone 1 / 5 (draft).

---

## Project description

In the main Pokémon games, a player builds a team of 6. Every Pokémon has one or two types (Fire, Water, Grass...), and every type is strong or weak against others, which is hard for beginners to keep track of. Many players rely on spreadsheets or outdated apps, especially for community challenge runs such as Nuzlockes.

PokéPlanner solves this by:

- letting trainers build teams for a chosen game and note the challenge they are playing (for example a Nuzlocke),
- calculating the team's weaknesses, resistances and immunities automatically and showing them as a chart and warnings,
- letting trainers share teams publicly and comment on them,
- offering a public Pokédex, with extra details (height, weight, abilities, evolutions, artwork) fetched live from PokéAPI for logged-in users.

It is a single-page application with a pure PHP REST backend, and it is designed to work on phones as well as desktops.

## Features

- Public Pokédex with search, type and generation filters, and a base stat radar chart
- "Show more details" on every Pokémon (logged-in users): live data from PokéAPI
- Public teams and comments, readable without an account
- Registration and login with JWT authentication
- Team builder with 6 slots (duplicates allowed, like in the games), game selection, an optional challenge field and live weakness analysis
- Profile with a bio, a favorite Pokémon (its sprite is the avatar) and the game currently being played
- Admin panel for Pokémon (with PokéAPI import), games, the type chart, users and comment moderation

## Roles and access

Side-note: Guests can view the Pokédex, public teams and related comments from users, but all other features (including the live Pokémon details) are locked behind sign-in.

Trainer (User):

- add/edit/delete own teams for available games, with an optional challenge name (e.g. Nuzlocke)
- view live Pokémon details ("Show more details")
- add/edit/delete own comments on other users' public teams
- edit profile (bio, favorite Pokémon)/delete own account

Admin (in addition to user permissions):

- delete any team, comment
- manage pokemon catalog, games list, types and type charts
- manage users and roles

### Test accounts

_TBD_

| Role    | Email | Password |
| ------- | ----- | -------- |
| Admin   | _TBD_ | _TBD_    |
| Trainer | _TBD_ | _TBD_    |

## Entities and relationships (Work in Progress)

**Core CRUD entities (5):** `users`, `pokemon`, `teams`, `games`, `comments`.
**Supporting tables (3):** `types`, `type_effectiveness`, `team_members`.

| Table              | Purpose                                                                                                      |
| ------------------ | ------------------------------------------------------------------------------------------------------------ |
| users              | Accounts, with a role (`trainer` or `admin`) and optional profile info (bio, favorite Pokémon, current game) |
| pokemon            | Catalog entries: dex number, name, generation, one or two types, sprite and six base stats                   |
| types              | The 18 Pokémon types                                                                                         |
| type_effectiveness | The type chart: attacker type, defender type and multiplier (0, 0.5, 1 or 2)                                 |
| games              | The games a team can be built for (name, generation, release year), managed by admins                        |
| teams              | A user's named team for a game, with an optional challenge (e.g. Nuzlocke), public or private                |
| team_members       | Which Pokémon sit in which slot (1-6) of a team (many-to-many), plus nicknames                               |
| comments           | Comments by users on public teams                                                                            |

Key relationships:

- A user has many teams and many comments, and can pick one favorite Pokémon and one currently played game.
- A team belongs to one user and one game, and has up to 6 members (one per slot).
- A Pokémon has one or two types and can be in many teams, even twice in the same team.
- A game is used by many teams. A Pokémon is allowed in a game if its generation is not newer than the game's.

ERD: draft version located in docs/erd.png

## External API

**[PokéAPI](https://pokeapi.co)** - free and without an API key

Everything the application's own logic needs (name, types, base stats, generation, sprite) is stored in the project's database, so the Pokédex, teams and weakness analysis never depend on the API. Extra information is fetched live through the backend when a logged-in user clicks **Show more details** on a Pokémon: height, weight, abilities, Pokédex description, evolution chain and official artwork. Guests see a "log in to see more details" prompt instead. Admins can also import a Pokémon by name or number to prefill the catalog form.

Calls use a 5 second timeout. Successes are written to the info log and failures to the error log. If PokéAPI is unavailable, the details panel shows a "details temporarily unavailable" message, and the stored data and the rest of the application keep working.

## Tech stack

- **Backend:** PHP, FlightPHP, PDO, MySQL, JWT, OpenAPI (Swagger)
- **Frontend:** HTML, CSS, JavaScript, Bootstrap 5 (Bootswatch theme), Highcharts, DataTables
- **Architecture:** single-page application, REST API, routes -> services -> DAOs, with middleware for authentication, authorization, request logging, error handling and rate limiting

## Project structure

```
backend/
  config/        config.example.php (config.php is not committed)
  dao/
  middleware/
  routes/
  services/
  logs/          (not committed)
  index.php
frontend/
  css/
  js/            router and service files
  views/         HTML templates
  assets/
  index.html
docs/
  AI_LOG.md
  erd.png
AGENTS.md
README.md
```

## AI usage

AI tools are used as documented in `AGENTS.md` (context rules for AI assistants) and `docs/AI_LOG.md` (tools used, key prompts, and cases where the AI was wrong).

## Credits

- Pokémon data and sprites from [PokéAPI](https://pokeapi.co). Pokémon and related names are trademarks of their respective owners, and this is a non-commercial student project.
- Bootswatch theme: [Darkly](https://bootswatch.com/darkly/)
- Charts: Highcharts. Tables: DataTables.
