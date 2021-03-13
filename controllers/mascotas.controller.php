<?php

class MascotasController {

  private $db = null;

  function __construct($conexion) {
    $this->db = $conexion;
  }

  public function obtenerMascotas() {
    $busqueda = null;
    if(!empty($_GET["busqueda"])) $busqueda = $_GET["busqueda"];
    $eval = "SELECT * FROM mascotas";
    $peticion = $this->db->prepare($eval);
    $peticion->execute();
    $resultado = $peticion->fetchAll(PDO::FETCH_OBJ);
    exit(json_encode($resultado));
  }
  
  public function insertarMascotas() {
    $mascota = json_decode(file_get_contents("php://input"));

    if(!isset($mascota->nombre) || !isset($mascota->tipoAni)) {
      http_response_code(400);
      exit(json_encode(["error" => "No se han enviado todos los parametros"]));
    }

    $peticion = $this->db->prepare("INSERT INTO mascotas (nombre,tipoAni,peso) VALUES (?,?,?)");
    $resultado = $peticion->execute([$mascota->nombre,$mascota->tipoAni, $mascota->peso]);
    http_response_code(201);
    exit(json_encode("Mascota creada correctamente"));
  }

  public function editarMascota() {
    $nota = json_decode(file_get_contents("php://input"));
    if(IDUSER) {
     // if(!isset($mascota->nombre) || !isset($mascota->tipoAni) || !isset($mascota->peso)) {
      //  http_response_code(400);
       // exit(json_encode(["error" => "No se han enviado todos los parametros"]));
     // }
      $eval = "UPDATE mascotas SET nombre=?, tipoAni=?, peso=? WHERE id=?";
      $peticion = $this->db->prepare($eval);
      $peticion->execute([$mascota->nombre,$mascota->tipoAni,$mascota->peso]);
      http_response_code(201);
      //Comprobamos si se ha eliminado la nota e informarnos en la respuesta.
      if($peticion->rowCount()) exit(json_encode("Se ha actualizado la nota"));
      else exit(json_encode("La nota no se ha actualizado"));
    } else {
      http_response_code(401);
      exit(json_encode(["error" => "Fallo de autorizacion"]));        
    }
  }

  public function eliminarMascota($id) {
    if(empty($id)) {
      http_response_code(400);
      exit(json_encode(["error" => "Peticion mal formada"]));    
    }
   
      $eval = "DELETE FROM mascotas WHERE id=?";
      $peticion = $this->db->prepare($eval);
      $peticion->execute([$id]);
      http_response_code(200);
      //Comprobamos si se ha eliminado la mascota e informarnos en la respuesta.
      if($peticion->rowCount()) exit(json_encode("Mascota eliminada correctamente"));
      else exit(json_encode("La mascota no se ha podido eliminar"));

    }
  }
