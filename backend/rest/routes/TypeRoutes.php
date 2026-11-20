<?php

Flight::route('GET /types', function () {
    Flight::json(Flight::type_service()->get_all());
});

Flight::route('GET /types/@id:[0-9]+', function ($id) {
    Flight::json(Flight::type_service()->get_by_id($id));
});

Flight::route('GET /types/@id:[0-9]+/attacks', function ($id) {
    Flight::json(Flight::type_effectiveness_service()->get_by_attacker($id));
});

Flight::route('GET /types/@id:[0-9]+/defenses', function ($id) {
    Flight::json(Flight::type_effectiveness_service()->get_by_defender($id));
});

Flight::route('POST /types', function () {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::type_service()->add($data), 201);
});

Flight::route('PUT /types/@id:[0-9]+', function ($id) {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::type_service()->update($data, $id));
});

Flight::route('DELETE /types/@id:[0-9]+', function ($id) {
    Flight::type_service()->delete($id);
    Flight::json(['message' => 'Type deleted.']);
});