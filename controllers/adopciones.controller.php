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
  public function eliminarAdopcion($id) {
    if(empty($id)) {
      http_response_code(400);
      exit(json_encode(["error" => "Peticion mal formada"]));    
    }
   
      $eval = "DELETE FROM adopcion WHERE id=?";
      $peticion = $this->db->prepare($eval);
      $peticion->execute([$id]);
      http_response_code(200);
      

      //Comprobamos si se ha eliminado la mascota e informarnos en la respuesta.
      if($peticion->rowCount()) exit(json_encode("Adopcion eliminada correctamente"));
      else exit(json_encode("La adopcion no se ha podido eliminar"));

    }

}