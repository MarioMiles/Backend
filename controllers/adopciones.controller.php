<?php
use \Firebase\JWT\JWT;
class AdopcionesController {

    private $db = null;

  function __construct($conexion) {
    $this->db = $conexion;
  }
  public function comenzarAdopcion($id){
    header('Location: /confirmar');
    $mascota = json_decode(file_get_contents("php://input"));
    
    $peticion = $this->db->prepare("INSERT INTO adopciones (idMas,idUsu) VALUES (?,?)");
  $resultado = $peticion->execute([$id, IDUSER]);
  http_response_code(201);
  exit(json_encode("Adopcion creada correctamente"));
    
    
    exit(json_encode($resultado));
    http_response_code(201);

  }
}