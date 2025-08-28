<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\AffiliateDTO;
use App\DTOs\CoordinateDTO;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class AffiliateService
{
    private const CACHE_KEY = 'affiliates_data';

    /**
     * Get all affiliates within the specified distance from Dublin office
     *
     * @param  float|null  $maxDistance  Maximum distance in kilometers (defaults to config value)
     * @return Collection<int, AffiliateDTO>
     */
    public function getAffiliatesWithinDistance(?float $maxDistance = null): Collection
    {
        // if null, use config value which defaults to 100km
        $maxDistance ??= config('services.affiliate.distance_limit_km');

        $dublinOffice = new CoordinateDTO(
            latitude: config('services.affiliate.dublin_office.latitude'),
            longitude: config('services.affiliate.dublin_office.longitude')
        );

        $affiliates = $this->getAllAffiliates();

        return filterByDistance(
            $affiliates,
            $dublinOffice,
            $maxDistance,
            'affiliate_id'
        );
    }

    /**
     * Get all affiliates from the data file
     *
     * @return Collection<int, AffiliateDTO>
     */
    public function getAllAffiliates(): Collection
    {
        // Caching the affiliates data to avoid repeated file reads as the data is static
        $cacheKey = self::CACHE_KEY;
        $cacheTtl = config('services.affiliate.cache_ttl');

        return Cache::remember($cacheKey, $cacheTtl, function (): Collection {
            try {
                return $this->loadAffiliatesFromFile();
            } catch (\Exception $e) {
                // Log the error and rethrow as runtime exception, we should be using Sentry or similar in real app so sentry will email developers of any issues
                Log::error('Error loading affiliates data: '.$e->getMessage());
                throw new RuntimeException('Unable to load affiliates data', 0, $e);
            }
        });
    }

    /**
     * Clear the affiliates cache
     */
    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Load affiliates data from the text file
     *
     * @return Collection<int, AffiliateDTO>
     */
    private function loadAffiliatesFromFile(): Collection
    {
        $filename = config('services.affiliate.data_file');

        if (! Storage::disk('local')->exists($filename)) {
            throw new RuntimeException("Affiliates data file not found: {$filename}");
        }

        $content = Storage::disk('local')->get($filename);

        $lines = array_filter(explode("\n", $content));
        $affiliates = collect();

        foreach ($lines as $lineNumber => $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }

            $data = json_decode($line, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::warning('Invalid JSON on line '.($lineNumber + 1).': '.json_last_error_msg());

                continue;
            }

            $affiliates->push(AffiliateDTO::fromArray($data));
        }

        return $affiliates;
    }
}
