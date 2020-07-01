<?php
namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
use App\Models\User;

class LoginMiddleware
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
        $email =  $headers['email'];
        $clave = $headers['clave'];
        $rta = new Response();
        if ((isset($email) && $email!= "") && (isset($clave) && $clave!=""))
        {
            $usuarioEncontrado = json_decode(User::whereRaw('email = ? AND clave = ?',array($email,$clave))->get());
            
            if($usuarioEncontrado != [])
            {
                $response = $handler->handle($request);
                $existingContent = (string) $response->getBody();
                $array = array(
                    "status" =>"200",
                    "token" => $existingContent
                );
                $rta->getBody()->write(json_encode($array));
            }else
            {
                $array = array(
                    "status" =>"404",
                    "message" => "No hay coincidencia entre email y contraseña"
                );
                //$rta->withStatus(404); ->esto no hace nada
                $rta->getBody()->write(json_encode($array));
            }
        }else
        {
            $array = array(
                "status" =>"403",
                "message" => "Faltan datos"
            );
            $rta->getBody()->write(json_encode($array));
        }

        return $rta->withHeader('Content-type', 'application/json');
    }
}
