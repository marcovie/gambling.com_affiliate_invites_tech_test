<?php

declare(strict_types=1);

namespace Tests\Unit\DTOs;

use App\DTOs\AffiliateDTO;
use PHPUnit\Framework\TestCase;

class AffiliateDTOTest extends TestCase
{
    public function test_can_create_affiliate_dto_from_array(): void
    {
        $data = [
            'affiliate_id' => 1,
            'name' => 'John Doe',
            'latitude' => '53.3340285',
            'longitude' => '-6.2535495',
        ];

        $dto = AffiliateDTO::fromArray($data);

        $this->assertSame(1, $dto->affiliate_id);
        $this->assertSame('John Doe', $dto->name);
        $this->assertSame(53.3340285, $dto->latitude);
        $this->assertSame(-6.2535495, $dto->longitude);
        $this->assertNull($dto->distance);
    }

    public function test_can_add_distance_to_affiliate_dto(): void
    {
        $dto = new AffiliateDTO(
            affiliate_id: 1,
            name: 'John Doe',
            latitude: 53.3340285,
            longitude: -6.2535495
        );

        $dtoWithDistance = $dto->withDistance(50.5);

        $this->assertSame(1, $dtoWithDistance->affiliate_id);
        $this->assertSame('John Doe', $dtoWithDistance->name);
        $this->assertSame(53.3340285, $dtoWithDistance->latitude);
        $this->assertSame(-6.2535495, $dtoWithDistance->longitude);
        $this->assertSame(50.5, $dtoWithDistance->distance);

        // Original DTO should remain unchanged
        $this->assertNull($dto->distance);
    }

    public function test_handles_string_numeric_values_in_from_array(): void
    {
        $data = [
            'affiliate_id' => '42',
            'name' => 'Jane Smith',
            'latitude' => '52.123456',
            'longitude' => '-7.654321',
        ];

        $dto = AffiliateDTO::fromArray($data);

        $this->assertSame(42, $dto->affiliate_id);
        $this->assertSame(52.123456, $dto->latitude);
        $this->assertSame(-7.654321, $dto->longitude);
    }
}
