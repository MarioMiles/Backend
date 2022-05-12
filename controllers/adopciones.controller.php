<?php
use \Firebase\JWT\JWT;
class AdopcionesController {

    private $db = null;

  function __construct($conexion) {
    $this->db = $conexion;
  }
  public function insertarAdopcion() {
    
   
   $peticion = $this->db->prepare("INSERT INTO adopciones (id, idMas, idUsu) VALUES (?,?,?)");
    $resultado = $peticion->execute([$adopcion->nombre,$adopcion->idMas, $adopcion->idUsu]);
    http_response_code(201);
    exit(json_encode("Adopcion creada correctamente"));
    }
}