<?php

namespace Config;

use Slim\Routing\RouteCollectorProxy;
//Controllers
use App\Controllers\UsersController;
use App\Controllers\MateriasController;
use App\Controllers\TurnosController;
//middles
use App\Middleware\RegistroMiddleware;
use App\Middleware\LoginMiddleware;
use App\Middleware\RegistroMateriaMiddleware;
use App\Middleware\ConsultaMateriaMiddleware;
use App\Middleware\AsignaMateriaMiddleware;



return function ($app) 
{
    $app->post('/usuario',UsersController::class . ':add')->add(RegistroMiddleware::class);
    $app->post('/login',UsersController::class . ':login')->add(LoginMiddleware::class);
    //$app->post('/materias',MateriasController::class . ':add')->add(RegistroMateriaMiddleware::class);

    $app->group('/materias', function (RouteCollectorProxy $materias) 
    {
        $materias->post('',MateriasController::class . ':add')->add(RegistroMateriaMiddleware::class);
        $materias->get('/{id}', MateriasController::class . ':materiasGetAll')->add(ConsultaMateriaMiddleware::class);
        $materias->put('/{id]/{profesor}',MateriasController::class . ':asignaMateria')->add(AsignaMateriaMiddleware::class);
    });
};