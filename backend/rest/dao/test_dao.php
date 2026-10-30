<?php
/**
 * Quick check of the DAO layer. Run from the backend folder:
 *   php rest/dao/test_dao.php
 * (or open it in the browser through Apache). Needs the pokeplanner database to be filled with data.
 * The insert test runs inside a transaction that is rolled back, so nothing is left in the database.
 */
require_once __DIR__ . '/BaseDao.php';
require_once __DIR__ . '/GameDao.php';
require_once __DIR__ . '/TypeDao.php';
require_once __DIR__ . '/TypeEffectivenessDao.php';
require_once __DIR__ . '/PokemonDao.php';
require_once __DIR__ . '/UserDao.php';
require_once __DIR__ . '/TeamDao.php';
require_once __DIR__ . '/TeamMemberDao.php';
require_once __DIR__ . '/CommentDao.php';

if (php_sapi_name() !== 'cli') {
    header('Content-Type: text/plain; charset=utf-8');
}

function line($text = '')
{
    echo $text . "\n";
}

$games         = new GameDao();
$types         = new TypeDao();
$effectiveness = new TypeEffectivenessDao();
$pokemon       = new PokemonDao();
$users         = new UserDao();
$teams         = new TeamDao();
$members       = new TeamMemberDao();
$comments      = new CommentDao();

/* ---------- 1. row counts ---------- */
line("== Row counts (expected: games 37, types 18, type_effectiveness 324, pokemon 1025) ==");
foreach (['games' => $games, 'types' => $types, 'type_effectiveness' => $effectiveness, 'pokemon' => $pokemon,
          'users' => $users, 'teams' => $teams, 'team_members' => $members, 'comments' => $comments] as $name => $dao) {
    line(str_pad($name, 20) . $dao->count_all());
}

/* ---------- 2. reading with JOINs ---------- */
line("\n== Pikachu by dex number (expected: Electric, no second type, speed 90) ==");
$p = $pokemon->get_by_dex_number(25);
line("{$p['name']} | {$p['type1_name']} | " . ($p['type2_name'] ?? '-') . " | speed {$p['speed']}");

line("\n== Search \"fetch\" (expected: Farfetch’d and Sirfetch’d) ==");
foreach ($pokemon->search_by_name('fetch') as $row) {
    line("#{$row['dex_number']} {$row['name']}");
}

line("\n== SQL injection attempt: search \"' OR 1=1 -- \" (expected: 0 results) ==");
line(count($pokemon->search_by_name("' OR 1=1 -- ")) . " result(s)");

/* ---------- 3. composite-key table ---------- */
line("\n== Type chart (expected: Water vs Fire 2, Electric vs Ground 0) ==");
line("Water -> Fire:      " . $effectiveness->get_multiplier(11, 10));
line("Electric -> Ground: " . $effectiveness->get_multiplier(13, 5));

line("\n== get_by_id on type_effectiveness must be refused ==");
try {
    $effectiveness->get_by_id(1);
    line("NOT refused - something is wrong");
} catch (LogicException $e) {
    line("Refused: " . $e->getMessage());
}

/* ---------- 4. insert / read / rollback ---------- */
line("\n== Insert test (rolled back at the end) ==");
$users_before = $users->count_all();
$users->begin_transaction();
try {
    $suffix = uniqid();
    $user = $users->add([
        'username'      => "test_$suffix",
        'email'         => "test_$suffix@example.com",
        'password_hash' => password_hash('Test123!', PASSWORD_BCRYPT),
        'role'          => 'trainer',
        'hacker'        => 'this key is not a column',
    ]);
    line("Added user #{$user['id']} (unknown key 'hacker' dropped: " . (array_key_exists('hacker', $user) ? 'NO' : 'yes') . ")");

    $team = $teams->add(['user_id' => $user['id'], 'game_id' => 1, 'name' => 'Test team', 'is_public' => false]);
    $stored = $teams->get_by_id($team['id']);
    line("Added team #{$team['id']} (is_public stored as {$stored['is_public']}, expected 0)");

    $members->add(['team_id' => $team['id'], 'pokemon_id' => 25, 'slot' => 1, 'nickname' => 'Sparky']);
    $members->add(['team_id' => $team['id'], 'pokemon_id' => 1,  'slot' => 2]);
    line("Next free slot: " . $members->next_free_slot($team['id']) . " (expected 3)");

    try {
        $members->add(['team_id' => $team['id'], 'pokemon_id' => 4, 'slot' => 1]);
        line("Duplicate slot was NOT refused - something is wrong");
    } catch (PDOException $e) {
        line("Duplicate slot 1 refused by the database (expected)");
    }

    $comments->add(['team_id' => $team['id'], 'user_id' => $user['id'], 'body' => 'Nice team!']);

    line("\nTeam members:");
    foreach ($members->get_by_team($team['id']) as $m) {
        line("  slot {$m['slot']}: {$m['pokemon_name']} ({$m['type1_name']}" . ($m['type2_name'] ? "/{$m['type2_name']}" : "") . ")"
             . ($m['nickname'] ? " \"{$m['nickname']}\"" : ""));
    }
    line("Comments:");
    foreach ($comments->get_by_team($team['id']) as $c) {
        line("  {$c['username']}: {$c['body']}");
    }
} finally {
    $users->rollback();
}
line("\nUsers before: $users_before, after rollback: " . $users->count_all() . " (should be equal)");
