<?php

namespace App\Modules\Health\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Health\Actions\CheckSystemHealthAction;
use App\Modules\Health\Http\Resources\HealthResource;
use Illuminate\Http\JsonResponse;

class HealthController extends Controller
{
    public function __invoke(CheckSystemHealthAction $action): JsonResponse
    {
        $result = $action->handle();

        return (new HealthResource($result))
            ->response()
            ->setStatusCode($result['ok'] ? 200 : 503);
    }
}
