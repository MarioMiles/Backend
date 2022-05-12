<?php
use \Firebase\JWT\JWT;
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
  public function listarMascotas() {
    //Comprueba si el usuario esta registrado.
    if(IDUSER) {
      $eval = "SELECT nombre, tipoAni, peso, foto FROM mascotas";
      $peticion = $this->db->prepare($eval);
      $peticion->execute();
      $resultado = $peticion->fetchAll(PDO::FETCH_OBJ);
      exit(json_encode($resultado));
    } else {
      http_response_code(401);
      exit(json_encode(["error" => "Fallo de autorizacion"]));       
    }
  }
  public function insertarMascotas() {
    $mascota = json_decode(file_get_contents("php://input"));

    if(!isset($mascota->nombre) || !isset($mascota->tipoAni)) {
      http_response_code(400);
      exit(json_encode(["error" => "Njkhkhjo se han enviado todos los parametros"]));
    }
   
      
   
    $ext =strpos("gif", "jpeg") ? ".jpg":".png";
    $nombreFoto = "m-".time().$ext;
    $ruta = ROOT."images/".$nombreFoto;



    $imgFind = ROOT."images/p-".IDUSER."-*";
    $imgFile = glob($imgFind);
    foreach($imgFile as $fichero) unlink($fichero);
   
    
    
  
    
    $foto = "http://localhost/backendphp"."/images/".$nombreFoto;
    
   
   $peticion = $this->db->prepare("INSERT INTO mascotas (nombre,tipoAni,peso,foto) VALUES (?,?,?,?)");
    $resultado = $peticion->execute([$mascota->nombre,$mascota->tipoAni, $mascota->peso, $foto]);
    http_response_code(201);
    exit(json_encode("Mascota creada correctamente"));
    }
  

  public function editarMascota($id) {
    if(IDUSER) {
      //Cogemos los valores de la peticion.
      $mascota = json_decode(file_get_contents("php://input"));
      json_encode($mascota);
      
      
      
      //Obtenemos los datos guardados en el servidor relacionados con el usuario
      $peticion = $this->db->prepare("SELECT nombre,tipoAni,peso FROM mascotas WHERE id=?");
      //$peticion->execute([$nId]);
      $resultado = $peticion->fetchObject();

      //Combinamos los datos de la petición y de los que había en la base de datos.
      $nId = isset($mascota->id) ? $mascota->id : $resultado->id;
      $nNombre = isset($mascota->nombre) ? $mascota->nombre : $resultado->nombre;
      $ntipoAni = isset($mascota->tipoAni) ? $mascota->tipoAni : $resultado->tipoAni;
      $nPeso = isset($mascota->peso) ? $mascota->peso : $resultado->peso;
       
        $eval = "UPDATE mascotas SET nombre=?,tipoAni=?,peso=? WHERE id=?";
        $peticion = $this->db->prepare($eval);
        $peticion->execute([$nNombre,$ntipoAni,$nPeso,$nId]);        
      
      http_response_code(201);
      exit(json_encode("Usuario actualizado correctamente"));
    
    } else {
      http_response_code(401);
      exit(json_encode(["error" => "Fallo de autorizacion"]));         
    }
  }
  /*public function editarMascota($id) {
    if(empty($id)) {
      http_response_code(400);
      exit(json_encode(["error" => "Peticion mal formada"]));    
    }
    $mascota = json_decode(file_get_contents("php://input"));
   
    if(IDUSER) {
     
     $peticion = $this->db->prepare("SELECT nombre,tipoAni,peso FROM mascotas WHERE id= :id");
     $peticion->execute([$id]);
     $resultado = $peticion->fetchObject();

     //Combinamos los datos de la petición y de los que había en la base de datos.
     $nNombre = isset($mascota->nombre) ? $mascota->nombre : $resultado->nombre;
     $ntipoAni = isset($mascota->tipoAni) ? $mascota->tipoAni : $resultado->tipoAni;
     $nPeso = isset($mascota->peso) ? $mascota->peso : $resultado->peso;
     
     
      $eval = "UPDATE mascotas SET nombre=?, tipoAni=?, peso=? WHERE id= :id" ;
      $peticion = $this->db->prepare($eval);
      $peticion->execute([$nNombre,$ntipoAni,$nPeso,$id]);
     
      http_response_code(201);
      //Comprobamos si se ha eliminado la nota e informarnos en la respuesta.
      if($peticion->rowCount()) exit(json_encode("Se ha actualizado la mascota"));
      else exit(json_encode("La mascota no se ha actualizado"));
    } else {
      http_response_code(401);
      exit(json_encode(["error" => "Fallo de autorizacion"]));        
    }
  }*/
    
 
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
    public function subirAvatar($id) {
      
      if(is_null(IDUSER)){
        http_response_code(401);
        exit(json_encode(["error" => "Fallo de autorizacion"]));
      }
      if(isset($_FILES['imagen'])) {
        $imagen = $_FILES['imagen'];
        $mime = $imagen['type'];
        $size = $imagen['size'];
        $rutaTemp = $imagen['tmp_name'];
    
        //Comprobamos que la imagen sea JPEG o PNG y que el tamaño sea menor que 400KB.
        if( !(strpos($mime, "jpeg") || strpos($mime, "png")) || ($size > 400000) ) {
          http_response_code(400);
          exit(json_encode(["error" => "La imagen tiene que ser JPG o PNG y no puede ocupar mas de 400KB"]));
        } else {
    
          //Comprueba cual es la extensión del archivo.
          $ext = strpos($mime, "jpeg") ? ".jpg":".png";
          $nombreFoto = "p-".time().$ext;
          $ruta = ROOT."images/".$nombreFoto;
    
          //Comprobamos que el usuario no tenga mas fotos de perfil subidas al servidor.
          //En caso de que exista una imagen anterior la elimina.
          $imgFind = ROOT."images/p-".IDUSER."-*";
          $imgFile = glob($imgFind);
          foreach($imgFile as $fichero) unlink($fichero);
          
          //Si se guarda la imagen correctamente actualiza la ruta en la tabla usuarios
          if(move_uploaded_file($rutaTemp,$ruta)) {
    
            //Prepara el contenido del campo imgSrc
            $foto = "http://localhost/backendphp/"."/images/".$nombreFoto;
    
            $eval = "UPDATE mascotas SET foto=? WHERE id=?";
            $peticion = $this->db->prepare($eval);
            $peticion->execute([$foto, $id]);
            
            
    
            http_response_code(201);
            exit(json_encode("Imagen actualizada correctamente"));
          } else {
            http_response_code(500);
            exit(json_encode(["error" => "Ha habido un error con la subida"]));      
          }
        }
      }  else {
        http_response_code(400);
        exit(json_encode(["error" => "No se han enviado todos los parametros"]));
      }
    }
}
