<?php

Flight::route('GET /comments', function () {
    Flight::json(Flight::comment_service()->get_all());
});

Flight::route('GET /comments/@id:[0-9]+', function ($id) {
    Flight::json(Flight::comment_service()->get_by_id($id));
});

Flight::route('POST /comments', function () {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::comment_service()->add($data), 201);
});

Flight::route('PUT /comments/@id:[0-9]+', function ($id) {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::comment_service()->update($data, $id));
});

Flight::route('DELETE /comments/@id:[0-9]+', function ($id) {
    Flight::comment_service()->delete($id);
    Flight::json(['message' => 'Comment deleted.']);
});