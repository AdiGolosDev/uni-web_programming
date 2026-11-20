<?php

Flight::route('GET /users', function () {
    Flight::json(Flight::user_service()->get_all());
});

Flight::route('GET /users/@id:[0-9]+', function ($id) {
    Flight::json(Flight::user_service()->get_by_id($id));
});

Flight::route('GET /users/@id:[0-9]+/profile', function ($id) {
    Flight::json(Flight::user_service()->get_profile($id));
});

Flight::route('GET /users/@id:[0-9]+/teams', function ($id) {
    Flight::json(Flight::team_service()->get_by_user($id));
});

Flight::route('GET /users/@id:[0-9]+/comments', function ($id) {
    Flight::json(Flight::comment_service()->get_by_user($id));
});

Flight::route('POST /users', function () {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::user_service()->add($data), 201);
});

Flight::route('PUT /users/@id:[0-9]+', function ($id) {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::user_service()->update($data, $id));
});

Flight::route('PUT /users/@id:[0-9]+/role', function ($id) {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::user_service()->change_role($id, $data['role'] ?? null));
});

Flight::route('DELETE /users/@id:[0-9]+', function ($id) {
    Flight::user_service()->delete($id);
    Flight::json(['message' => 'User deleted.']);
});