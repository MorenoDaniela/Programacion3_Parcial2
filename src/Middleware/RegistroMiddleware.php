<?php
namespace App\Middleware;

//use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as Response;
use App\Models\User;

class RegistroMiddleware
{
    /**
     * Example middleware invokable class
     *
     * @param  ServerRequest  $request PSR-7 request
     * @param  RequestHandler $handler PSR-15 request handler
     *
     * @return Response
     */
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        var_dump("1");
        $headers = $request->getParsedBody();
        //var_dump($uploadedFiles = $request->getUploadedFiles());
        //$arrayFotos = ($request->getUploadedFiles())['foto'];

        if ((isset($headers['email']) && $headers['email']!="") 
        && (isset($headers['clave']) && $headers['clave']!="") 
        && (isset($headers['tipo'])&& $headers['tipo']!="")
        && (isset($headers['legajo'])&& $headers['legajo']!="")
        && (isset($headers['nombre'])&& $headers['nombre']!="")
        && ($headers['legajo']>=1000 || $headers['legajo']<=2000))
        {
            var_dump("2");
            //siempre hace json_decode cuando hago consulta where
            $usuario = json_decode(User::where('email', $headers['email'])->get());
            var_dump("antes de if");
            if ($usuario == [] )
            {
                var_dump("3");
                $response = $handler->handle($request);
                $existingContent = (string) $response->getBody();
                $resp = new Response();
                $resp->getBody()->write('Los datos se encuentran bien' . $existingContent);
                return $resp->withHeader('Content-type', 'application/json');
            }else if ($usuario[0]->email == $headers['email'] || $usuario[0]->legajo== $headers['legajo'])
            {
                $response = new Response();
                $response->getBody()->write("El email ya se encuentra registrado o ese legajo esta en uso");
                //throw new \Slim\Exception\HttpForbiddenException($request);
                $response->withStatus(403);
                return $response->withHeader('Content-type', 'application/json');
            } 
        } else 
        {
            $response = new Response();
            $response->getBody()->write("No se pudo completar el registro, faltan datos");
            //throw new \Slim\Exception\HttpForbiddenException($request);
            $response->withStatus(403);
            return $response->withHeader('Content-type', 'application/json');
        }

    }
}