<?php

Flight::route('GET /teams', function () {
    Flight::json(Flight::team_service()->get_all());
});

Flight::route('GET /teams/public', function () {
    $query = Flight::request()->query;
    Flight::json(Flight::team_service()->get_public($query['limit'] ?? 20, $query['offset'] ?? 0));
});

Flight::route('GET /teams/@id:[0-9]+', function ($id) {
    Flight::json(Flight::team_service()->get_by_id($id));
});

Flight::route('GET /teams/@id:[0-9]+/members', function ($id) {
    Flight::json(Flight::team_member_service()->get_by_team($id));
});

Flight::route('GET /teams/@id:[0-9]+/comments', function ($id) {
    Flight::json(Flight::comment_service()->get_by_team($id));
});

Flight::route('POST /teams/@id:[0-9]+/members/swap', function ($id) {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::team_member_service()->swap_slots($id, $data['slot_a'] ?? null, $data['slot_b'] ?? null));
});

Flight::route('POST /teams', function () {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::team_service()->add($data), 201);
});

Flight::route('PUT /teams/@id:[0-9]+', function ($id) {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::team_service()->update($data, $id));
});

Flight::route('DELETE /teams/@id:[0-9]+', function ($id) {
    Flight::team_service()->delete($id);
    Flight::json(['message' => 'Team deleted.']);
});