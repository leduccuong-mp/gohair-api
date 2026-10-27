<?php

namespace App\Modules\Health\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property array{ok: bool, checks: array<string, bool>} $resource
 */
class HealthResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'status' => $this->resource['ok'] ? 'ok' : 'down',
            'checks' => $this->resource['checks'],
            'timestamp' => now()->toIso8601String(),
        ];
    }
}
