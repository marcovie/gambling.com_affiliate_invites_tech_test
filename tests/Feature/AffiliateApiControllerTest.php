<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\DTOs\AffiliateDTO;
use App\Services\AffiliateService;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class AffiliateApiControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_api_returns_json_response_with_affiliate_data(): void
    {
        $testData = collect([
            new AffiliateDTO(1, 'John Doe', 53.35, -6.25, 25.5),
            new AffiliateDTO(2, 'Jane Smith', 53.4, -6.3, 45.2),
        ]);

        $this->mock(AffiliateService::class, function ($mock) use ($testData) {
            $mock->shouldReceive('getAffiliatesWithinDistance')
                ->once()
                ->andReturn($testData);
        });

        $response = $this->getJson('/api/affiliates');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'affiliate_id',
                    'name',
                    'latitude',
                    'longitude',
                    'distance',
                ],
            ],
        ]);

        $response->assertJsonCount(2, 'data');
        $response->assertJsonFragment(['name' => 'John Doe']);
        $response->assertJsonFragment(['name' => 'Jane Smith']);
    }

    public function test_api_returns_empty_array_when_no_affiliates_found(): void
    {
        $this->mock(AffiliateService::class, function ($mock) {
            $mock->shouldReceive('getAffiliatesWithinDistance')
                ->once()
                ->andReturn(collect([]));
        });

        $response = $this->getJson('/api/affiliates');

        $response->assertStatus(200);
        $response->assertJson(['data' => []]);
        $response->assertJsonCount(0, 'data');
    }

    public function test_api_returns_404_when_service_throws_exception(): void
    {
        $this->mock(AffiliateService::class, function ($mock) {
            $mock->shouldReceive('getAffiliatesWithinDistance')
                ->andThrow(new \Exception('Test error'));
        });

        $response = $this->getJson('/api/affiliates');

        $response->assertStatus(404);
        $response->assertJson(['data' => []]);
    }

    public function test_api_route_is_accessible(): void
    {
        $this->mock(AffiliateService::class, function ($mock) {
            $mock->shouldReceive('getAffiliatesWithinDistance')
                ->once()
                ->andReturn(collect([]));
        });

        $response = $this->getJson('/api/affiliates');

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/json');
    }

    public function test_api_returns_affiliates_sorted_by_id(): void
    {
        $testData = collect([
            new AffiliateDTO(1, 'First', 53.4, -6.3, 45.2),
            new AffiliateDTO(3, 'Third', 53.5, -6.35, 15.1),
            new AffiliateDTO(5, 'Fifth', 53.35, -6.25, 25.5),
        ]);

        $this->mock(AffiliateService::class, function ($mock) use ($testData) {
            $mock->shouldReceive('getAffiliatesWithinDistance')
                ->once()
                ->andReturn($testData);
        });

        $response = $this->getJson('/api/affiliates');

        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertEquals(1, $data[0]['affiliate_id']);
        $this->assertEquals(3, $data[1]['affiliate_id']);
        $this->assertEquals(5, $data[2]['affiliate_id']);
    }

    public function test_api_response_contains_correct_affiliate_data_structure(): void
    {
        $testData = collect([
            new AffiliateDTO(42, 'Test Affiliate', 53.123456, -6.654321, 75.8),
        ]);

        $this->mock(AffiliateService::class, function ($mock) use ($testData) {
            $mock->shouldReceive('getAffiliatesWithinDistance')
                ->once()
                ->andReturn($testData);
        });

        $response = $this->getJson('/api/affiliates');

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'affiliate_id' => 42,
            'name' => 'Test Affiliate',
            'latitude' => 53.123456,
            'longitude' => -6.654321,
            'distance' => 75.8,
        ]);
    }
}
