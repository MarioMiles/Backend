<?php

class AdminController {

  private $db = null;

  function __construct($conexion) {
    $this->db = $conexion;
  }

  function obtenerRoles() {
    //Comprueba que el ROL de usuario sea de administrador
    if(ROLUSER == IDADMIN) {
      $eval = "SELECT * FROM roles";
      $peticion = $this->db->prepare($eval);
      $peticion->execute();
      $resultado = $peticion->fetchAll(PDO::FETCH_OBJ);
      exit(json_encode($resultado));
    } else {
      //En caso de que no tenga permiso devolverá un error 403.
      http_response_code(403);
      exit(json_encode(["error" => "Este recurso no está disponible"]));    
    }
  }

  function obtenerUsers() {
    if(ROLUSER == IDADMIN) {
      //Funcion que a diferencia de listar usuario, también recibe los roles de los usuarios.
      $eval = "SELECT u.id,u.nombre,u.apellidos,u.email,u.idRol,r.nombreRol FROM users u,roles r WHERE r.id=u.idRol";
      $peticion = $this->db->prepare($eval);
      $peticion->execute();
      $resultado = $peticion->fetchAll(PDO::FETCH_OBJ);
      exit(json_encode($resultado));

    } else {
      http_response_code(403);
      exit(json_encode(["error" => "Este recurso no está disponible"]));    
    }
  }

  function cambiarRol() {
    if(ROLUSER == IDADMIN) {
      $user = json_decode(file_get_contents("php://input"));

      //Comprobamos que se hayan recibido todos los parametros.
      if(!isset($user->id) || !isset($user->rolNuevo) || !strlen($user->rolNuevo)) {
        http_response_code(400);
        exit(json_encode(["error" => "No se han enviado todos los parametros"]));
      }

      $eval = "UPDATE users SET idRol=? WHERE id=?";
      $peticion = $this->db->prepare($eval);
      $peticion->execute([$user->rolNuevo,$user->id]);
      http_response_code(201);
      //Comprobamos si se ha editado correctamente informarnos en la respuesta.
      if($peticion->rowCount()) exit(json_encode("Se ha actualizado el rol del usuario"));
      else exit(json_encode("El rol del usuario no se ha cambiado"));

    } else {
      http_response_code(403);
      exit(json_encode(["error" => "Este recurso no está disponible"]));    
    }
  }

}