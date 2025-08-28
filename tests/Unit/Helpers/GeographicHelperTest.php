<?php

declare(strict_types=1);

namespace Tests\Unit\Helpers;

use App\DTOs\AffiliateDTO;
use App\DTOs\CoordinateDTO;
use Tests\TestCase;

class GeographicHelperTest extends TestCase
{
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
        $cork = new CoordinateDTO(51.8969, -8.4863);

        $distance = calculateDistance($dublin, $cork);

        $this->assertGreaterThan(210, $distance);
        $this->assertLessThan(230, $distance);
    }

    public function test_filter_by_distance_filters_correctly(): void
    {
        $dublin = new CoordinateDTO(53.3340285, -6.2535495);

        $affiliates = collect([
            new AffiliateDTO(1, 'Close Affiliate', 53.334, -6.254),  // Very close to Dublin
            new AffiliateDTO(2, 'Far Affiliate', 51.8969, -8.4863), // Cork - ~219km away
            new AffiliateDTO(3, 'Medium Affiliate', 53.4, -6.3),    // Medium distance
        ]);

        $result = filterByDistance($affiliates, $dublin, 100.0, 'affiliate_id');

        $this->assertCount(2, $result); // Should exclude Cork affiliate
        $this->assertTrue($result->contains('affiliate_id', 1));
        $this->assertTrue($result->contains('affiliate_id', 3));
        $this->assertFalse($result->contains('affiliate_id', 2));
    }

    public function test_filter_by_distance_adds_distance_property(): void
    {
        $dublin = new CoordinateDTO(53.3340285, -6.2535495);

        $affiliates = collect([
            new AffiliateDTO(1, 'Test Affiliate', 53.334, -6.254),
        ]);

        $result = filterByDistance($affiliates, $dublin, 100.0);
        $affiliate = $result->first();

        $this->assertNotNull($affiliate->distance);
        $this->assertIsFloat($affiliate->distance);
        $this->assertLessThan(1.0, $affiliate->distance); // Very close
    }

    public function test_filter_by_distance_sorts_by_specified_field(): void
    {
        $dublin = new CoordinateDTO(53.3340285, -6.2535495);

        $affiliates = collect([
            new AffiliateDTO(3, 'Third', 53.334, -6.254),
            new AffiliateDTO(1, 'First', 53.335, -6.255),
            new AffiliateDTO(2, 'Second', 53.336, -6.256),
        ]);

        $result = filterByDistance($affiliates, $dublin, 100.0, 'affiliate_id');

        $this->assertSame(1, $result->first()->affiliate_id);
        $this->assertSame(2, $result->skip(1)->first()->affiliate_id);
        $this->assertSame(3, $result->last()->affiliate_id);
    }

    public function test_works_with_generic_objects(): void
    {
        $dublin = new CoordinateDTO(53.3340285, -6.2535495);

        $objects = collect([
            (object) ['id' => 1, 'latitude' => 53.334, 'longitude' => -6.254],
            (object) ['id' => 2, 'latitude' => 51.8969, 'longitude' => -8.4863], // Cork
        ]);

        $result = filterByDistance($objects, $dublin, 100.0, 'id');

        $this->assertCount(1, $result); // Only close one should remain
        $this->assertSame(1, $result->first()->id);
        $this->assertObjectHasProperty('distance', $result->first());
    }
}
