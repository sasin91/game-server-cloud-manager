<?php

use App\Http\Controllers\CloudController;
use App\Http\Controllers\DeploymentController;
use App\Http\Controllers\ServerDeploymentController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ServerController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TeamInvitation\AcceptTeamInvitationController;
use App\Http\Controllers\TeamInvitation\DeclineTeamInvitationController;
use App\Http\Controllers\TeamInvitationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::group(['middleware' => 'auth'], function () {
    Route::post('teams', [TeamController::class, 'store'])
        ->name('teams.store');

    Route::patch('teams/{team}', [TeamController::class, 'update'])
        ->name('teams.update');

    Route::delete('teams/{team}', [TeamController::class, 'destroy'])
        ->name('teams.destroy');

    Route::post('teams/{team}/restore', [TeamController::class, 'restore'])
        ->name('teams.restore');

    Route::post('teams/{team}/invitations', [TeamInvitationController::class, 'store'])
        ->name('teams.invitations.store');

    Route::post('accept-team-invitation', AcceptTeamInvitationController::class)
        ->name('team-invitations.accept');

    Route::post('decline-team-invitation', DeclineTeamInvitationController::class)
        ->name('team-invitations.decline');

    Route::get('clouds', [CloudController::class, 'index'])
        ->name('clouds.index');

    Route::post('clouds', [CloudController::class, 'store'])
        ->name('clouds.store');

    Route::get('clouds/{cloud}', [CloudController::class, 'show'])
        ->name('clouds.show');

    Route::match(['PUT', 'PATCH'], 'clouds/{cloud}', [CloudController::class, 'update'])
        ->name('clouds.update');

    Route::delete('clouds/{cloud}', [CloudController::class, 'destroy'])
        ->name('clouds.destroy');

    Route::get('servers', [ServerController::class, 'index'])
        ->name('servers.index');

    Route::post('servers', [ServerController::class, 'store'])
        ->name('servers.store');

    Route::match(['PUT', 'PATCH'], 'servers/{server}', [ServerController::class, 'update'])
        ->name('servers.update');

    Route::delete('servers/{server}', [ServerController::class, 'destroy'])
        ->name('servers.delete');

    Route::apiResource('projects', ProjectController::class);
    Route::post('projects/{project}/restore', [ProjectController::class, 'restore'])
        ->name('projects.restore');

    Route::apiResource('server.deployments', ServerDeploymentController::class);
});
