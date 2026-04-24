<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CompetenceController;
use App\Http\Controllers\UtilisateurController;
use App\Http\Controllers\InterventionController;
use App\Http\Controllers\UserCompetencesController;
use App\Http\Controllers\AuthController;

// Routes publiques
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// Routes protégées
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/profil',  [AuthController::class, 'profil']);
    Route::put('/profil',  [AuthController::class, 'updateProfil']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::apiResource('competences',   CompetenceController::class);
    Route::apiResource('utilisateurs',  UtilisateurController::class);
    Route::apiResource('interventions', InterventionController::class);

    // user-competences : DELETE avec body JSON nécessite une route explicite
    Route::get('user-competences',                          [UserCompetencesController::class, 'index']);
    Route::post('user-competences',                         [UserCompetencesController::class, 'store']);
    Route::get('user-competences/user/{code_user}',         [UserCompetencesController::class, 'showByUser']);
    Route::delete('user-competences',                       [UserCompetencesController::class, 'destroy']);
});
