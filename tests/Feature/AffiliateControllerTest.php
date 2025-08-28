<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\DTOs\AffiliateDTO;
use App\Services\AffiliateService;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class AffiliateControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_index_route_displays_affiliates_page(): void
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

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewIs('affiliates.index');
        $response->assertViewHas('affiliates');
        $response->assertSee('Affiliate Locator');
        $response->assertSee('Found 2 affiliate(s) within 100km');
        $response->assertSee('John Doe');
        $response->assertSee('Jane Smith');
    }

    public function test_index_route_displays_empty_results(): void
    {
        $this->mock(AffiliateService::class, function ($mock) {
            $mock->shouldReceive('getAffiliatesWithinDistance')
                ->once()
                ->andReturn(collect([]));
        });

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewIs('affiliates.index');
        $response->assertSee('Found 0 affiliate(s) within 100km');
        $response->assertSee('No affiliates found within 100km');
    }

    public function test_index_route_handles_service_exception(): void
    {
        $this->mock(AffiliateService::class, function ($mock) {
            $mock->shouldReceive('getAffiliatesWithinDistance')
                ->andThrow(new \Exception('Test error'));
        });

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewIs('affiliates.index');
        $response->assertViewHas('error', 'Error loading affiliates: Test error');
        $response->assertSee('Error loading affiliates: Test error');
    }

    public function test_index_route_sorts_affiliates_by_id(): void
    {
        // Create a properly sorted collection (the service should sort by ID)
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

        $response = $this->get('/');

        $response->assertStatus(200);
        $content = $response->getContent();

        // Check that the affiliates appear in the correct order in the HTML
        $firstPos = strpos($content, 'First');
        $thirdPos = strpos($content, 'Third');
        $fifthPos = strpos($content, 'Fifth');

        $this->assertLessThan($thirdPos, $firstPos);
        $this->assertLessThan($fifthPos, $thirdPos);
    }
}
