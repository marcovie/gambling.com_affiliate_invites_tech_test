<?php

use App\Http\Controllers\Api\AffiliateApiController;
use Illuminate\Support\Facades\Route;

// API route for fetching affiliates within a certain distance this just example to show returning json response
// We should be using versioning for real world api's /api/v1/affiliates and middleware for auth, rate limiting etc This just a simple example
Route::get('/affiliates', AffiliateApiController::class)->name('api.affiliates.index');
