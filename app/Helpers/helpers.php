<?php

declare(strict_types=1);

use App\DTOs\CoordinateDTO;
use Illuminate\Support\Collection;

if (! function_exists('calculateDistance')) {
    /**
     * Calculate distance between two coordinates using great-circle distance formula
     */
    function calculateDistance(CoordinateDTO $point1, CoordinateDTO $point2): float
    {
        $lat1Rad = deg2rad($point1->latitude);
        $lon1Rad = deg2rad($point1->longitude);
        $lat2Rad = deg2rad($point2->latitude);
        $lon2Rad = deg2rad($point2->longitude);

        $deltaLat = $lat2Rad - $lat1Rad;
        $deltaLon = $lon2Rad - $lon1Rad;

        $a = sin($deltaLat / 2) * sin($deltaLat / 2) +
             cos($lat1Rad) * cos($lat2Rad) *
             sin($deltaLon / 2) * sin($deltaLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return 6371.0 * $c;
    }
}

if (! function_exists('filterByDistance')) {
    /**
     * Filter objects by distance from a reference point
     *
     * @template T of object
     *
     * @param  Collection<int, T>  $objects  Collection of objects with latitude and longitude properties
     * @param  CoordinateDTO  $referencePoint  The reference coordinate to calculate distances from
     * @param  float  $maxDistance  Maximum distance in kilometers
     * @param  string  $sortBy  Field to sort by (default: 'id')
     * @param  bool  $isDecending  Whether to sort in descending order (default: false)
     * @return Collection<int, T> Filtered and sorted collection
     */
    function filterByDistance(
        Collection $objects,
        CoordinateDTO $referencePoint,
        float $maxDistance,
        string $sortBy = 'affiliate_id',
        bool $isDecending = false
    ): Collection {
        return $objects
            ->map(function ($object) use ($referencePoint) {
                if (! property_exists($object, 'latitude') || ! property_exists($object, 'longitude')) {
                    throw new \InvalidArgumentException('Object must have latitude and longitude properties');
                }

                $objectCoordinate = new CoordinateDTO($object->latitude, $object->longitude);
                $distance = calculateDistance($referencePoint, $objectCoordinate);

                if (method_exists($object, 'withDistance')) {
                    return $object->withDistance($distance);
                }

                return (object) array_merge((array) $object, ['distance' => $distance]);
            })
            ->filter(function ($object) use ($maxDistance) {
                $distance = property_exists($object, 'distance') ? $object->distance : null;

                return $distance !== null && $distance <= $maxDistance;
            })
            ->sortBy($sortBy, descending: $isDecending)
            ->values();
    }
}
