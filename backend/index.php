<?php

require __DIR__ . '/vendor/autoload.php';

if (!file_exists(__DIR__ . '/config/config.php')) {
    http_response_code(500);
    exit('Missing backend/config/config.php – copy config.example.php to config.php and set your database credentials.');
}

require_once __DIR__ . '/rest/services/GameService.php';
require_once __DIR__ . '/rest/services/PokemonService.php';
require_once __DIR__ . '/rest/services/TypeService.php';
require_once __DIR__ . '/rest/services/TypeEffectivenessService.php';
require_once __DIR__ . '/rest/services/UserService.php';
require_once __DIR__ . '/rest/services/TeamService.php';
require_once __DIR__ . '/rest/services/TeamMemberService.php';
require_once __DIR__ . '/rest/services/CommentService.php';

Flight::register('game_service', 'GameService');
Flight::register('pokemon_service', 'PokemonService');
Flight::register('comment_service', 'CommentService');
Flight::register('team_member_service', 'TeamMemberService');
Flight::register('team_service', 'TeamService');
Flight::register('type_effectiveness_service', 'TypeEffectivenessService');
Flight::register('type_service', 'TypeService');
Flight::register('user_service', 'UserService');

Flight::map('error', function (Throwable $e) {
    if ($e instanceof HttpException) {
        $status = $e->get_status();
        $message = $e->getMessage();
    } else {
        $status = 500;
        $message = 'Something went wrong on the server. Please try again later.';
        error_log($e);
    }
    Flight::response()
        ->clearBody()
        ->status($status)
        ->header('Content-Type', 'application/json')
        ->write(json_encode(['error' => $message]))
        ->send();
});

Flight::map('notFound', function () {
    Flight::response()
        ->clearBody()
        ->status(404)
        ->header('Content-Type', 'application/json')
        ->write(json_encode(['error' => 'Endpoint not found.']))
        ->send();
});

Flight::route('GET /', function () {
    Flight::json(['app' => 'PokéPlanner API', 'message' => 'The backend is running.', 'docs' => 'public/v1/docs/']);
});

require_once __DIR__ . '/rest/routes/GameRoutes.php';
require_once __DIR__ . '/rest/routes/PokemonRoutes.php';
require_once __DIR__ . '/rest/routes/CommentRoutes.php';
require_once __DIR__ . '/rest/routes/TeamMemberRoutes.php';
require_once __DIR__ . '/rest/routes/TeamRoutes.php';
require_once __DIR__ . '/rest/routes/TypeEffectivenessRoutes.php';
require_once __DIR__ . '/rest/routes/TypeRoutes.php';
require_once __DIR__ . '/rest/routes/UserRoutes.php';

Flight::start();