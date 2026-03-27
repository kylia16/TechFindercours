<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CompetenceController;


Route::apiResource('competences', CompetenceController::class);
