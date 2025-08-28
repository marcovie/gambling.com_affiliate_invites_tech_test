<?php

declare(strict_types=1);

use App\Http\Controllers\AffiliateController;
use Illuminate\Support\Facades\Route;

// Web route for displaying affiliates within a certain distance from a given location with no middleware or auth for simplicity
Route::get('/', AffiliateController::class)->name('affiliates.index');
