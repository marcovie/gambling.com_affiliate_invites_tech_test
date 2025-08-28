<?php

declare(strict_types=1);

namespace App\DTOs;

readonly class AffiliateDTO
{
    public function __construct(
        public int $affiliate_id,
        public string $name,
        public float $latitude,
        public float $longitude,
        public ?float $distance = null
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            affiliate_id: (int) $data['affiliate_id'],
            name: (string) $data['name'],
            latitude: (float) $data['latitude'],
            longitude: (float) $data['longitude']
        );
    }

    public function withDistance(float $distance): self
    {
        return new self(
            affiliate_id: $this->affiliate_id,
            name: $this->name,
            latitude: $this->latitude,
            longitude: $this->longitude,
            distance: $distance
        );
    }
}
