<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\User;
use \Firebase\JWT\JWT;


class UsersController 
{

    /*   public function getAll(Request $request, Response $response, $args)
    {
        $rta = json_encode(Usuario::all());

        // $response->getBody()->write("Controller");
        $response->getBody()->write($rta);

        return $response;
    }*/

    public function add(Request $request, Response $response, $args)
    {
        /*
        $arrayFotos = ($request->getUploadedFiles())['foto'];
        for ($i=0; $i < count($arrayFotos); $i++) { 
            $uploadFileName = $arrayFotos[$i]->getClientFilename();
            if ( $i == 0)
            {
                $arrayFotos[$i]->moveTo("../perfiles/$uploadFileName");//no existe mirar
            }
            else if ( $i == 1)
            {
                $arrayFotos[$i]->moveTo("../portadas/$uploadFileName");//no existe mirar
            }
        }*/

        $usuario = new User;
        $respuesta = $request->getParsedBody();
        $response->withHeader('Content-type', 'application/json');
        $usuario->email = $respuesta['email'];
        $usuario->clave = $respuesta['clave'];//mirar si en tabla esta como pass o clave $usuario->clave
        $usuario->tipo_id = $respuesta['tipo'];
        $usuario->nombre = $respuesta['nombre'];
        $usuario->legajo = $respuesta['legajo'];
        $rta = json_encode(array("ok" => $usuario->save()));
        $response->getBody()->write($rta);
        
        return $response;
    }

    public function login(Request $request, Response $response, $args)
    {
        
        $body = $request->getParsedBody();
        $email = $body['email'];
        $clave = $body['clave'];

        $usuarioEncontrado = json_decode(User::whereRaw('email = ? AND clave = ?',array($email,$clave))->get());
        $key = 'usuario';
        $payload = array(
            "email" => $usuarioEncontrado[0]->email,
            "clave" => $usuarioEncontrado[0]->clave,
            "tipo" => $usuarioEncontrado[0]->tipo_id,
            "id" =>$usuarioEncontrado[0]->id,
            "nombre"=> $usuarioEncontrado[0]->nombre,
            "legajo" => $usuarioEncontrado[0]->legajo);

        //$response->withStatus(200);
        
        $response->getBody()->write(JWT::encode($payload,$key));
        //$existingContent = (string) $response->getBody();
        //$response->getBody()->write($existingContent);

        return $response->withHeader('Content-type', 'application/json');;
    }


    
}