<?php

declare(strict_types=1);

namespace App\DTOs;

readonly class CoordinateDTO
{
    public function __construct(
        public float $latitude,
        public float $longitude
    ) {}
}
