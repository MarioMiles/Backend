<?php

use \Firebase\JWT\JWT;

class SugerenciasController {

  private $db = null;

  function __construct($conexion) {
    $this->db = $conexion;
  }

  public function listarSugerencias() {
    //Comprueba si el usuario esta registrado.
    if(IDUSER) {
      $eval = "SELECT * FROM sugerencias";
      $peticion = $this->db->prepare($eval);
      $peticion->execute();
      $resultado = $peticion->fetchAll(PDO::FETCH_OBJ);
      exit(json_encode($resultado));
    } else {
      http_response_code(401);
      exit(json_encode(["error" => "Fallo de autorizacion"]));       
    }
  }
  public function insertarSugerencia(){
    if(IDUSER){
      //Comprobamos que se hayan proporcionados todos los datos para enviar un mensaje.
      $sugerencia = json_decode(file_get_contents("php://input"));
      if(!isset($sugerencia->asunto) || !isset($sugerencia->mensaje)) {
        http_response_code(400);
        exit(json_encode(["error" => "No se han enviado todos los parametros"]));
      }
      //Inserta en la tabla el mensaje con los parámetros de entrada
      $eval = 'INSERT INTO sugerencias (asunto,mensaje,idUsu) VALUES (?,?,?)';
      $peticion = $this->db->prepare($eval);
      $peticion->execute([$sugerencia->asunto,$sugerencia->mensaje, IDUSER]);
      http_response_code(201);
      exit(json_encode("Mensaje enviado correctamente"));
    }
    else{
      http_response_code(401);
      exit(json_encode(["error" => "Fallo de autorizacion"]));
    }
  }
  public function eliminarSugerencia($id) {
    //Comprueba si se ha proporcionado un email válido.
    
    if(empty($id)) {
      http_response_code(400);
      exit(json_encode(["error" => "Peticion mal formada"]));    
    }
    if(IDUSER) {
      //Función que solo elimina los mensajes que ha recibido.
      $eval = "DELETE FROM sugerencias WHERE id=?";
      $peticion = $this->db->prepare($eval);
      $peticion->execute([$id]);
      http_response_code(200);
      if($peticion->rowCount()) exit(json_encode("Mensaje eliminado correctamente"));
      else exit(json_encode("No se ha eliminado el mensaje"));
    } else {
      http_response_code(401);
      exit(json_encode(["error" => "Fallo de autorizacion"]));            
    }
  }
}