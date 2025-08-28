<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\AffiliateService;
use Illuminate\View\View;

class AffiliateController extends Controller
{
    // Made this invokable for simplicity as there's only one main action for now. So no need for index() and other methods.
    public function __invoke(AffiliateService $affiliateService): View
    {
        try {
            $affiliates = $affiliateService->getAffiliatesWithinDistance();

            return view('affiliates.index', compact('affiliates'));
        } catch (\Exception $e) {
            return view('affiliates.index', [
                'affiliates' => collect([]),
                'error' => 'Error loading affiliates: '.$e->getMessage(),
            ]);
        }
    }
}
