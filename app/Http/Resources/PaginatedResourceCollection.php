<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PaginatedResourceCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        return $this->collection
            ->map(fn ($item) => $item instanceof JsonResource ? $item->toArray($request) : $item)
            ->all();
    }

    public function paginationInformation($request, array $paginated, array $default): array
    {
        return [
            'meta' => [
                'current_page' => $paginated['current_page'],
                'last_page' => $paginated['last_page'],
                'total' => $paginated['total'],
            ],
        ];
    }
}
