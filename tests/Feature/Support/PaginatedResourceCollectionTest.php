<?php

use App\Http\Resources\PaginatedResourceCollection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

it('formats paginated responses as data + simplified meta, without the default links block', function () {
    $paginator = new LengthAwarePaginator(
        items: [['id' => 1], ['id' => 2]],
        total: 25,
        perPage: 2,
        currentPage: 3,
    );

    $resource = new PaginatedResourceCollection($paginator);

    $payload = $resource->response(Request::create('/'))->getData(true);

    expect($payload)->toBe([
        'data' => [
            ['id' => 1],
            ['id' => 2],
        ],
        'meta' => [
            'current_page' => 3,
            'last_page' => 13,
            'total' => 25,
        ],
    ]);
});
