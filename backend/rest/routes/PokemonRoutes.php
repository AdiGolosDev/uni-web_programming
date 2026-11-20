<?php

Flight::route('GET /pokemon', function () {
    $query = Flight::request()->query;
    if ($query['q'] !== null && $query['q'] !== '') {
        Flight::json(Flight::pokemon_service()->search($query['q']));
    } elseif ($query['type_id'] !== null && $query['type_id'] !== '') {
        Flight::json(Flight::pokemon_service()->get_by_type($query['type_id']));
    } elseif ($query['generation'] !== null && $query['generation'] !== '') {
        Flight::json(Flight::pokemon_service()->get_by_generation($query['generation']));
    } else {
        Flight::json(Flight::pokemon_service()->get_all());
    }
});

Flight::route('GET /pokemon/@id:[0-9]+', function ($id) {
    Flight::json(Flight::pokemon_service()->get_by_id($id));
});

Flight::route('POST /pokemon', function () {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::pokemon_service()->add($data), 201);
});

Flight::route('PUT /pokemon/@id:[0-9]+', function ($id) {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::pokemon_service()->update($data, $id));
});

Flight::route('DELETE /pokemon/@id:[0-9]+', function ($id) {
    Flight::pokemon_service()->delete($id);
    Flight::json(['message' => 'Pokémon deleted.']);
});

Flight::route('GET /pokemon/@id:[0-9]+/details', function ($id) {
    Flight::json(Flight::pokemon_service()->get_details($id));
});