<?php

//use Config\Database;
namespace Config;
use Slim\App;
use App\Middleware\BeforeMiddleware;
use App\Middleware\AfterMiddleware;
use App\Middleware\RegistroMiddleware;


return function (App $app) {
    $app->addBodyParsingMiddleware();

    //$app->add(new RegistroMiddleware());
    $app->add(new AfterMiddleware());
    //$app->add(BeforeMiddleware::class);
    
};