<?php

declare(strict_types=1);

namespace Tests\Unit\DTOs;

use App\DTOs\CoordinateDTO;
use PHPUnit\Framework\TestCase;

class CoordinateDTOTest extends TestCase
{
    public function test_can_create_coordinate_dto(): void
    {
        $dto = new CoordinateDTO(
            latitude: 53.3340285,
            longitude: -6.2535495
        );

        $this->assertSame(53.3340285, $dto->latitude);
        $this->assertSame(-6.2535495, $dto->longitude);
    }

    public function test_coordinate_dto_is_readonly(): void
    {
        $dto = new CoordinateDTO(
            latitude: 53.3340285,
            longitude: -6.2535495
        );

        // This test verifies that the DTO is readonly by checking reflection
        $reflection = new \ReflectionClass($dto);
        $this->assertTrue($reflection->isReadOnly());
    }
}
