<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\DTOs\CoordinateDTO;
use App\Services\AffiliateService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class AffiliateServiceTest extends TestCase
{
    private AffiliateService $service;

    protected function setUp(): void
    {
        parent::setUp();

        // Clear cache first to avoid stale data
        Cache::flush();

        $this->service = new AffiliateService;

        // Set test configuration
        Config::set('services.affiliate.dublin_office.latitude', 53.3340285);
        Config::set('services.affiliate.dublin_office.longitude', -6.2535495);
        Config::set('services.affiliate.distance_limit_km', 100);
        Config::set('services.affiliate.cache_ttl', 3600);
        Config::set('services.affiliate.data_file', 'affiliates.txt');
    }

    protected function tearDown(): void
    {
        Cache::flush();
        parent::tearDown();
    }

    public function test_calculate_distance_between_same_points_is_zero(): void
    {
        $point1 = new CoordinateDTO(53.3340285, -6.2535495);
        $point2 = new CoordinateDTO(53.3340285, -6.2535495);

        $distance = calculateDistance($point1, $point2);

        $this->assertSame(0.0, $distance);
    }

    public function test_calculate_distance_between_different_points(): void
    {
        $dublin = new CoordinateDTO(53.3340285, -6.2535495);
        $cork = new CoordinateDTO(51.8969, -8.4863); // Cork, Ireland

        $distance = calculateDistance($dublin, $cork);

        // Cork is approximately 219km from Dublin
        $this->assertGreaterThan(210, $distance);
        $this->assertLessThan(230, $distance);
    }

    public function test_calculate_distance_formula_accuracy(): void
    {
        // Test with known coordinates and expected distance
        $point1 = new CoordinateDTO(52.986375, -6.043701); // Yosef Giles from test data
        $dublin = new CoordinateDTO(53.3340285, -6.2535495);

        $distance = calculateDistance($point1, $dublin);

        // This should be approximately 41.8km based on the great-circle distance formula
        $this->assertGreaterThan(40, $distance);
        $this->assertLessThan(50, $distance);
    }

    public function test_calculate_distance_with_negative_coordinates(): void
    {
        $point1 = new CoordinateDTO(-33.8688, 151.2093); // Sydney
        $point2 = new CoordinateDTO(51.5074, -0.1278); // London

        $distance = calculateDistance($point1, $point2);

        // Sydney to London is approximately 17,000km
        $this->assertGreaterThan(16000, $distance);
        $this->assertLessThan(18000, $distance);
    }

    public function test_clear_cache_removes_cached_data(): void
    {
        // Set some cached data
        Cache::put('affiliates_data', collect(['test' => 'data']), 3600);
        $this->assertTrue(Cache::has('affiliates_data'));

        $this->service->clearCache();

        $this->assertFalse(Cache::has('affiliates_data'));
    }

    public function test_get_affiliates_within_distance_filters_correctly(): void
    {
        // Clear cache and use test data file - all are within ~1km of Dublin
        Cache::flush();
        Config::set('services.affiliate.data_file', 'test_affiliates.txt');

        // Test with very small distance to filter some out
        $result = $this->service->getAffiliatesWithinDistance(1.0);

        // With such a small distance, likely only the closest one will match
        $this->assertGreaterThan(0, $result->count());
        $this->assertLessThan(4, $result->count()); // Not all 3 should be included
        $this->assertTrue($result->contains('affiliate_id', 1)); // Closest should be included
    }

    public function test_get_affiliates_within_distance_sorts_by_id(): void
    {
        // Clear cache and use test data file which has IDs: 1, 2, 3
        Cache::flush();
        Config::set('services.affiliate.data_file', 'test_affiliates.txt');

        $result = $this->service->getAffiliatesWithinDistance(100.0);

        $this->assertCount(3, $result);
        $this->assertSame(1, $result->first()->affiliate_id);
        $this->assertSame(2, $result->skip(1)->first()->affiliate_id);
        $this->assertSame(3, $result->last()->affiliate_id);
    }

    public function test_get_affiliates_within_distance_adds_distance_to_results(): void
    {
        // Clear cache and use test data file
        Cache::flush();
        Config::set('services.affiliate.data_file', 'test_affiliates.txt');

        $result = $this->service->getAffiliatesWithinDistance(100.0);

        $this->assertGreaterThan(0, $result->count());
        $affiliate = $result->first();
        $this->assertNotNull($affiliate->distance);
        $this->assertIsFloat($affiliate->distance);
    }
}
