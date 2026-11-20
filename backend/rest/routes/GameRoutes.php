<?php

Flight::route('GET /games', function () {
    $generation = Flight::request()->query['generation'];
    if ($generation !== null && $generation !== '') {
        Flight::json(Flight::game_service()->get_by_generation($generation));
    } else {
        Flight::json(Flight::game_service()->get_all());
    }
});

Flight::route('GET /games/@id:[0-9]+', function ($id) {
    Flight::json(Flight::game_service()->get_by_id($id));
});

Flight::route('POST /games', function () {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::game_service()->add($data), 201);
});

Flight::route('PUT /games/@id:[0-9]+', function ($id) {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::game_service()->update($data, $id));
});

Flight::route('DELETE /games/@id:[0-9]+', function ($id) {
    Flight::game_service()->delete($id);
    Flight::json(['message' => 'Game deleted.']);
});