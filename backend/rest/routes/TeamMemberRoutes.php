<?php

Flight::route('GET /team-members', function () {
    Flight::json(Flight::team_member_service()->get_all());
});

Flight::route('GET /team-members/@id:[0-9]+', function ($id) {
    Flight::json(Flight::team_member_service()->get_by_id($id));
});

Flight::route('POST /team-members', function () {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::team_member_service()->add($data), 201);
});

Flight::route('PUT /team-members/@id:[0-9]+', function ($id) {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::team_member_service()->update($data, $id));
});

Flight::route('DELETE /team-members/@id:[0-9]+', function ($id) {
    Flight::team_member_service()->delete($id);
    Flight::json(['message' => 'Team member deleted.']);
});