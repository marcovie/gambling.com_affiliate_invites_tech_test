<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AffiliateResource;
use App\Services\AffiliateService;
use Illuminate\Http\JsonResponse;

class AffiliateApiController extends Controller
{
    // Made this invokable for simplicity as there's only one main action for now. So no need for index() and other methods.
    // This is also really simple api as there's no authentication or other complex logic or any versioning.
    public function __invoke(AffiliateService $affiliateService): AffiliateResource|JsonResponse
    {
        try {
            $affiliates = $affiliateService->getAffiliatesWithinDistance();

            return AffiliateResource::make($affiliates);
        } catch (\Exception $e) {
            return AffiliateResource::make(collect([]))
                ->response()
                ->setStatusCode(JsonResponse::HTTP_NOT_FOUND);
        }
    }
}
