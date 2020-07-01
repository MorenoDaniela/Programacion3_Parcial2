<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Materia;
use \Firebase\JWT\JWT;
use App\Models\User;
use App\Models\Inscripto;

class MateriasController 
{

    /*   public function getAll(Request $request, Response $response, $args)
    {
        $rta = json_encode(Materia::all());

        // $response->getBody()->write("Controller");
        $response->getBody()->write($rta);

        return $response;
    }*/

    public function add(Request $request, Response $response, $args)
    {
        $header = getallheaders();
        $token = $header['token'];

        $decoded = JWT::decode($token, 'usuario', array('HS256'));

        $Materia = new Materia;
        $respuesta = $request->getParsedBody();
        $response->withHeader('Content-type', 'application/json');
        $Materia->vacantes = $respuesta['vacantes'];
        $Materia->cuatrimestre = $respuesta['cuatrimestre'];
        $Materia->profesor_id = $respuesta['profesor'];
        $Materia->materia = $respuesta['materia'];
        $rta = json_encode(array("ok" => $Materia->save()));
        $response->getBody()->write($rta);
        
        return $response;
    }

    public function materiasGetAll(Request $request, Response $response, $args)
    {
        $header = getallheaders();
        $token = $header['token'];
        $id = $args['id'];

        $decoded = JWT::decode($token, 'usuario', array('HS256'));
        $usuarioEncontrado = json_decode(User::whereRaw('email = ? AND clave = ?',array($decoded->email,$decoded->clave))->get());
        $materiaEncontrada = json_decode(Materia::where('id', $id)->get());
        $Inscriptos = json_decode(Inscripto::where('materia_id',$id)->get());
        if ($usuarioEncontrado[0]->tipo_id == 1 && $materiaEncontrada[0] !=[])
        {
            $rta = json_encode(array("ok" => $materiaEncontrada));
            $response->getBody()->write($rta);
        }else if (($usuarioEncontrado[0]->tipo_id == 2 || $usuarioEncontrado[0]->tipo_id == 3) && $materiaEncontrada[0]!=[] && $Inscriptos[0]!=[])
        {
            $rta = json_encode(array("ok" => $materiaEncontrada,
        "Inscriptos:" => $Inscriptos));
            $response->getBody()->write($rta);
        }else
        {
            $rta = json_encode(array("false" => "No se encontraron coincidencias"));
            $response->getBody()->write($rta);
        }
        return $response;
    } 
}