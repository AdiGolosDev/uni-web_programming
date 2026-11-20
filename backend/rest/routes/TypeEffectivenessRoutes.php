<?php

Flight::route('GET /type-effectiveness', function () {
    Flight::json(Flight::type_effectiveness_service()->get_all());
});

Flight::route('GET /type-effectiveness/@attacker:[0-9]+/@defender:[0-9]+', function ($attacker, $defender) {
    Flight::json(Flight::type_effectiveness_service()->get_pair($attacker, $defender));
});

Flight::route('POST /type-effectiveness', function () {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::type_effectiveness_service()->add($data), 201);
});

Flight::route('PUT /type-effectiveness/@attacker:[0-9]+/@defender:[0-9]+', function ($attacker, $defender) {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::type_effectiveness_service()->update_pair($data, $attacker, $defender));
});

Flight::route('DELETE /type-effectiveness/@attacker:[0-9]+/@defender:[0-9]+', function ($attacker, $defender) {
    Flight::type_effectiveness_service()->delete_pair($attacker, $defender);
    Flight::json(['message' => 'Type matchup deleted.']);
});