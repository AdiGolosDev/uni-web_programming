# AGENTS.md – context for AI coding assistants

> This file tells AI tools (Claude, Copilot, Cursor, ChatGPT...) how this project is built.
> Read it completely before suggesting or writing code. Keep it up to date in every milestone.

## Project

**PokéPlanner** – a single-page application where Pokémon trainers build teams for a specific
game and see what the team is weak to (IT 2001 Web Programming course project, student: Adi Golos).

Access levels:

- **Guest** (not logged in): browse the Pokédex, public teams, team analyses and comments – read only.
- **Trainer** (registered user): everything a guest can do, plus create/edit/delete own teams, choose the
  Pokémon in each slot, comment, edit own profile, and open live Pokémon details from the external PokéAPI.
- **Admin**: everything a trainer can do, plus manage Pokémon, games, the type chart, users and comments.

Guest is **not** a database role. Roles are `trainer` and `admin` (enum on `users.role`).
New registrations are always `trainer`.

## Technology stack

- **Frontend:** HTML5, CSS3, JavaScript, jQuery 3, Bootstrap 5 (Bootswatch **Darkly** theme),
  spapp, DataTables, Toastr, Highcharts.
  No frameworks (React/Vue/Angular are NOT allowed).
- **Backend:** PHP 8.2+, FlightPHP 3, MySQL, JWT for authentication. No other PHP framework.
- **Database:** MySQL 8.
- **External API:** PokéAPI (https://pokeapi.co)
- **Local server:** XAMPP (Apache + MySQL).

## Folder structure

```
frontend/            static SPA – only HTML, CSS and JS
  index.html         app shell, every <section> in <main id="spapp"> is one view
  views/             HTML templates loaded by spapp
  css/               spapp.css (from the course, unchanged except the animation), style.css (our styles)
  js/                app.js, lib/jquery.spapp.js (from the course), services/ (all fetch calls, from M3/M4)
  assets/            images (img/)
backend/             REST API – only PHP
  index.php          entry point (plain "hello world" JSON in M1, FlightPHP from week 05)
  config/            config.example.php (committed), config.php (NOT committed)
  rest/routes/       presentation layer – FlightPHP routes
  rest/services/     business logic layer
  rest/dao/          data access layer (PDO)
  middleware/        request logger, error handler, auth (401), role check (403)
  logs/              info.log / error.log (NOT committed)
docs/                ERD (ERD.md + erd.png), AI_LOG.md
```

## Views (one `<section>` in `index.html` per view)

| Section id          | File                          | Who                                                               | Status                            |
| ------------------- | ----------------------------- | ----------------------------------------------------------------- | --------------------------------- |
| `home` (last)       | `home.html`                   | everyone                                                          | static draft                      |
| `pokedex`           | `pokedex.html`                | everyone (live PokéAPI details: trainers only)                    | static draft, becomes a DataTable |
| `teams`             | `teams.html`                  | tabs: **Your teams** (trainer/admin), **Public teams** (everyone) | static draft                      |
| `analysis`          | `analysis.html`               | everyone; owner-only Change buttons                               | static draft (one sample team)    |
| `profile`           | `profile.html`                | trainer, admin                                                    | undecided / optional              |
| `administration`    | `admin.html`                  | admin only (tabs: Users, Pokémon, Teams, Games, Comments, Types, Type chart, Team members) | static mockup; access is not enforced yet |
| `login`, `register` | `login.html`, `register.html` | guests                                                            | static draft                      |
| `error_404`         | `404.html`                    | everyone                                                          | done                              |

Hiding a menu item or button is only cosmetic. Access is enforced by the backend (401 / 403), never only in the UI.

## Database (8 tables)

`users`, `games`, `types`, `type_effectiveness`, `pokemon`, `teams`, `team_members`, `comments`
(see `docs/ERD.md`):

- One modern type chart for all games; Pokémon are species only (no regional forms).
- A Pokémon can be used in a game only if `pokemon.generation <= games.generation`.
- Catalog starts with generations 1–3. Duplicate Pokémon in one team are allowed. Slots are 1–6.
- `teams.challenge` is free text (e.g. Nuzlocke). Avatar = sprite of the user's favorite Pokémon (no upload).
- `type_effectiveness` has a composite primary key, so it does not use the generic `getById`.
- Planned (not confirmed): `pokemon.details_json` + `details_fetched_at` as the PokéAPI cache.

## External API rules (PokéAPI)

- Called only from the backend, only for logged-in users, with a **5 second timeout**.
- Success → `info.log`. Failure → `error.log` and a friendly "details temporarily unavailable" fallback.
- The app must keep working (stored stats, teams, analysis) when PokéAPI is down.
- Responses are cached in my database so each Pokémon is fetched at most once per refresh period,
  as PokéAPI's fair-use policy asks.

## Rules the AI must follow

1. Never mix backend and frontend code. No PHP in `frontend/`, no HTML output from `backend/`.
2. Keep the existing code style (see naming conventions). Do not introduce new libraries without asking.
3. Explain every change so that the student can defend it without AI help. Work in small steps.
4. Never claim something works without it having been tested; say what was and was not checked.
