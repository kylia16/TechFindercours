<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\web\CompetenceController;
use App\Http\Controllers\web\UtilisateurController;
use App\Http\Controllers\web\AuthController;

Route::get('/login', function () {
    return redirect('/web/connexion');
})->name('login');

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('web')->group(function () {

    // Authentification
    Route::get('/connexion',   [AuthController::class, 'showLoginForm']);
    Route::post('/connexion',  [AuthController::class, 'login']);
    Route::get('/deconnexion', [AuthController::class, 'logout']);

    // Compétences
    Route::get('/competences',                [CompetenceController::class, 'index']);
    Route::post('/competences/store',         [CompetenceController::class, 'store']);
    Route::post('/competences/{id}/update',   [CompetenceController::class, 'update']);
    Route::delete('/competences/{id}/delete', [CompetenceController::class, 'destroy']);

    // Users
    Route::get('/users',                      [UtilisateurController::class, 'index']);
    Route::post('/users/store',               [UtilisateurController::class, 'store']);
    Route::post('/users/{id}/update',         [UtilisateurController::class, 'update']);
    Route::delete('/users/{id}/delete',       [UtilisateurController::class, 'destroy']);

    // Pages à venir
    Route::get('/interventions',              function () { return 'Bientôt disponible'; });
    Route::get('/user-competences',           function () { return 'Bientôt disponible'; });

});
