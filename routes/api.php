<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\EventController;
use App\Http\Controllers\API\TicketController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\HomeController;
use App\Http\Controllers\API\FavoriteController;
use App\Http\Controllers\API\BlogController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Routes publiques
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

// Routes événements publiques
Route::get('/events', [EventController::class, 'index']);
Route::get('/events/search', [EventController::class, 'search']);
Route::get('/events/category/{category}', [EventController::class, 'byCategory']);
// Route spécifique pour les tickets (doit être avant la route générique {event})
Route::get('/events/{event}/tickets', [EventController::class, 'tickets'])->name('api.events.tickets');
Route::get('/events/{event}', [EventController::class, 'show']);
Route::get('/categories', [CategoryController::class, 'index']);

// Route pour la page d'accueil
Route::get('/home', [HomeController::class, 'index']);

// Routes des favoris
Route::get('/favorites', [FavoriteController::class, 'index']);
Route::post('/events/{event}/favorite', [FavoriteController::class, 'toggle']);

// Blog routes
Route::get('/blog-posts', [BlogController::class, 'index']);
Route::get('/blog-posts/{id}', [BlogController::class, 'show']);

// Routes protégées
Route::middleware('auth:sanctum')->group(function () {
    // Profil utilisateur
    Route::get('/profile', [UserController::class, 'profile']);
    Route::post('/profile', [UserController::class, 'updateProfile']);
    Route::post('/change-password', [UserController::class, 'changePassword']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Événements
    Route::get('/events/my-events', [EventController::class, 'myEvents']);
    Route::post('/events', [EventController::class, 'store']);
    Route::put('/events/{event}', [EventController::class, 'update']);
    Route::delete('/events/{event}', [EventController::class, 'destroy']);

    // Billets
    Route::get('/tickets', [TicketController::class, 'index']);
    Route::get('/tickets/{ticket}', [TicketController::class, 'show']);
    Route::post('/tickets/purchase', [TicketController::class, 'purchase']);
    Route::get('/tickets/validate/{ticket}', [TicketController::class, 'validateTicket']);
    Route::get('/tickets/my-tickets', [TicketController::class, 'myTickets']);

    // Dashboard stats
    Route::get('/dashboard/stats', [UserController::class, 'dashboardStats']);
});
