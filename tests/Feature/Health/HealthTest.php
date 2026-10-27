<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

it('returns 200 when database and redis are reachable', function () {
    $connection = Mockery::mock();
    $connection->shouldReceive('getPdo')->andReturn(Mockery::mock(PDO::class));
    DB::shouldReceive('connection')->andReturn($connection);

    $redisConnection = Mockery::mock();
    $redisConnection->shouldReceive('ping')->andReturn(true);
    Redis::shouldReceive('connection')->andReturn($redisConnection);

    $this->getJson('/api/v1/health')
        ->assertOk()
        ->assertJson([
            'data' => [
                'status' => 'ok',
                'checks' => [
                    'database' => true,
                    'redis' => true,
                ],
            ],
        ]);
});

it('returns 503 when redis connection fails', function () {
    $connection = Mockery::mock();
    $connection->shouldReceive('getPdo')->andReturn(Mockery::mock(PDO::class));
    DB::shouldReceive('connection')->andReturn($connection);

    Redis::shouldReceive('connection')->andThrow(new RuntimeException('connection refused'));

    $this->getJson('/api/v1/health')
        ->assertStatus(503)
        ->assertJson([
            'data' => [
                'status' => 'down',
                'checks' => [
                    'database' => true,
                    'redis' => false,
                ],
            ],
        ]);
});

it('returns 503 when database connection fails', function () {
    DB::shouldReceive('connection')->andThrow(new RuntimeException('connection refused'));

    $redisConnection = Mockery::mock();
    $redisConnection->shouldReceive('ping')->andReturn(true);
    Redis::shouldReceive('connection')->andReturn($redisConnection);

    $this->getJson('/api/v1/health')
        ->assertStatus(503)
        ->assertJson([
            'data' => [
                'status' => 'down',
                'checks' => [
                    'database' => false,
                    'redis' => true,
                ],
            ],
        ]);
});
