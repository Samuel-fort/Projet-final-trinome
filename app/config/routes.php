<?php

use app\controllers\DashboardController;
use app\controllers\VilleController;
use app\controllers\BesoinController;
use app\controllers\DonateurController;
use app\controllers\DonController;
use app\controllers\DistributionController;
use app\controllers\TypeBesoinController;
use app\controllers\TodolistController;
use app\middlewares\SecurityHeadersMiddleware;
use flight\Engine;
use flight\net\Router;

/** 
 * @var Router $router 
 * @var Engine $app
 */

$router->group('', function(Router $router) {

    // ── DASHBOARD ─────────────────────────────────────────────────────────────
    $router->get('/', [DashboardController::class, 'index']);

    // ── TODOLIST ──────────────────────────────────────────────────────────────
    $router->get('/todolist', [TodolistController::class, 'index']);

    // ── VILLES ────────────────────────────────────────────────────────────────
    $router->get('/villes',                        [VilleController::class, 'index']);
    $router->get('/villes/create',                 [VilleController::class, 'create']);
    $router->post('/villes/store',                 [VilleController::class, 'store']);
    $router->get('/villes/@id:[0-9]+/edit',        [VilleController::class, 'edit']);
    $router->post('/villes/@id:[0-9]+/update',     [VilleController::class, 'update']);
    $router->post('/villes/@id:[0-9]+/delete',     [VilleController::class, 'delete']);

    // ── BESOINS ───────────────────────────────────────────────────────────────
    $router->get('/besoins',                       [BesoinController::class, 'index']);
    $router->get('/besoins/create',                [BesoinController::class, 'create']);
    $router->post('/besoins/store',                [BesoinController::class, 'store']);
    $router->get('/besoins/@id:[0-9]+/edit',       [BesoinController::class, 'edit']);
    $router->post('/besoins/@id:[0-9]+/update',    [BesoinController::class, 'update']);
    $router->post('/besoins/@id:[0-9]+/delete',    [BesoinController::class, 'delete']);

    // ── TYPES DE BESOINS ──────────────────────────────────────────────────────
    $router->get('/types-besoins',                      [TypeBesoinController::class, 'index']);
    $router->get('/types-besoins/create',               [TypeBesoinController::class, 'create']);
    $router->post('/types-besoins/store',               [TypeBesoinController::class, 'store']);
    $router->get('/types-besoins/@id:[0-9]+/edit',      [TypeBesoinController::class, 'edit']);
    $router->post('/types-besoins/@id:[0-9]+/update',   [TypeBesoinController::class, 'update']);
    $router->post('/types-besoins/@id:[0-9]+/delete',   [TypeBesoinController::class, 'delete']);

    // ── DONATEURS ─────────────────────────────────────────────────────────────
    $router->get('/donateurs',                     [DonateurController::class, 'index']);
    $router->get('/donateurs/create',              [DonateurController::class, 'create']);
    $router->post('/donateurs/store',              [DonateurController::class, 'store']);
    $router->get('/donateurs/@id:[0-9]+/edit',     [DonateurController::class, 'edit']);
    $router->post('/donateurs/@id:[0-9]+/update',  [DonateurController::class, 'update']);
    $router->post('/donateurs/@id:[0-9]+/delete',  [DonateurController::class, 'delete']);

    // ── DONS ──────────────────────────────────────────────────────────────────
    $router->get('/dons',                          [DonController::class, 'index']);
    $router->get('/dons/create',                   [DonController::class, 'create']);
    $router->post('/dons/store',                   [DonController::class, 'store']);
    $router->post('/dons/@id:[0-9]+/delete',       [DonController::class, 'delete']);

    // ── DISTRIBUTIONS ─────────────────────────────────────────────────────────
    $router->get('/distributions',                          [DistributionController::class, 'index']);
    $router->get('/distributions/besoins',                  [DistributionController::class, 'getBesoins']);
    $router->post('/distributions/store',                   [DistributionController::class, 'store']);
    $router->post('/distributions/@id:[0-9]+/delete',       [DistributionController::class, 'delete']);

}, [SecurityHeadersMiddleware::class]);