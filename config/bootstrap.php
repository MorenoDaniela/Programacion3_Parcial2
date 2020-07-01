<?php


require_once __DIR__ . '/../vendor/autoload.php';
use Slim\Factory\AppFactory;
use Config\Database;
use Psr\Http\Message\ServerRequestInterface;

// Instanciar Illuminate
new Database();

$app = AppFactory::create();
$app->setBasePath("/Programacion3_Parcial2/public");
$app->addRoutingMiddleware();

$customErrorHandler = function (
    ServerRequestInterface $request,
    Throwable $exception,
    bool $displayErrorDetails,
    bool $logErrors,
    bool $logErrorDetails
) use ($app) {
    // $logger->error($exception->getMessage());

    $payload = ['Error' => $exception->getMessage()];

    $response = $app->getResponseFactory()->createResponse();
    $response->getBody()->write(
        json_encode($payload, JSON_UNESCAPED_UNICODE)
    );

    return $response;
};

// Add Error Middleware
$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$errorMiddleware->setDefaultErrorHandler($customErrorHandler);


// REGISTRAR RUTAS
(require_once __DIR__ . '/routes.php')($app);

// REGISTRAR MIDDLEWARE
(require_once __DIR__ . '/middleware.php')($app);

return $app;