<?php
namespace App\Middleware;

//use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as Response;
use \Firebase\JWT\JWT;
use App\Models\User;

class RegistroMateriaMiddleware
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
        
        $headers = $request->getParsedBody();
        $header = getallheaders();
        $token = $header['token'];
        $decoded = JWT::decode($token, 'usuario', array('HS256'));
        $usuarioEncontrado = json_decode(User::whereRaw('email = ? AND clave = ?',array($decoded->email,$decoded->clave))->get());
        //$profesorEncontrado = json_decode(User::where)('email',$headers['profesor'] );
        if ($usuarioEncontrado[0]->tipo_id == 3)
        {
            if ((isset($headers['materia']) && $headers['materia']!="") && (isset($headers['cuatrimestre']) && $headers['cuatrimestre']!="") &&
            (isset($headers['vacantes']) && $headers['vacantes']!="") && (isset($headers['profesor']) && $headers['profesor']!=""))
            {
                $profesorEncontrado = json_decode(User::where('id',$headers['profesor'])->get());
                if ($profesorEncontrado != [] && $profesorEncontrado[0]->id == 2)
                {
                    $response = $handler->handle($request);
                    $existingContent = (string) $response->getBody();
                    $resp = new Response();
                    $resp->getBody()->write('Los datos se encuentran bien' . $existingContent);
                    return $resp->withHeader('Content-type', 'application/json');
                }else
                {
                    $response = new Response();
                    $response->getBody()->write("No se pudo completar el registro, debe ingresar un id de profesor valido en -profesor-");
                    return $response->withHeader('Content-type', 'application/json');
                }
            } else 
            {
                $response = new Response();
                $response->getBody()->write("No se pudo completar el registro, faltan datos");
                return $response->withHeader('Content-type', 'application/json');
            }
        }else
        {
            $response = new Response();
            $response->getBody()->write("Registro de materias solo para admins");
            return $response->withHeader('Content-type', 'application/json');
        }
    }
}