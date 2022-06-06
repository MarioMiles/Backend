<?php
//Importamos las librerias necesarias.
require_once 'config/db.php';
require_once 'config/cors.php';
require "vendor/autoload.php";
use \Firebase\JWT\JWT;

//Guardamos la url para buscar el controlador y ponemos mensaje de bienvenida.
if(!isset($_GET['url'])) {
  exit(json_encode(["Bienvenido al Backend con routes"]));
} else $url = $_GET['url'];

//Preparamos la conexion con la base de datos
$bd = new db();
$conexion = $bd->getConnection();

//Comprueba si hay algún token valido en la cabecera y obtiene el ID del USER
$idUser = null;
//A su vez se obtiene el rol del usuario.
$rolUser = null;

if(!empty($_SERVER['HTTP_AUTHORIZATION'])) {
  $jwt = $_SERVER['HTTP_AUTHORIZATION'];
  try {
    //A diferencia del anterior backend, en este hemos modificado el contenido del token,
    //añadiendole al final ? idRol. Con la función explode se divide facilmente.
    $jwt = explode('?',$jwt)[0];

    $JWTraw = JWT::decode($jwt, $bd->getClave(), array('HS256'));
    $idUser = $JWTraw->id;

    //Aun pasando el proceso de verificación JWT se comprueba si en la base de datos existe el usuario.
    $peticion = $conexion->prepare("SELECT id,idRol FROM users WHERE id = ?");
    $peticion->execute([$idUser]);
    if($peticion->rowCount() == 0) {
      $idUser = null;
    } else {
      $resultado = $peticion->fetchObject();
      $rolUser = $resultado->idRol;
    }

  } catch (Exception $e) { }
}
//Guardamos las variables globales. IDUSER, ROLUSER, Metodo, CJWT, DIRECTORIO ROOT.
define('IDUSER', $idUser);
define('ROLUSER', $rolUser);
define('METODO', $_SERVER["REQUEST_METHOD"]);
define('ROOT', dirname(__FILE__) . DIRECTORY_SEPARATOR);
define('CJWT', $bd->getClave());

//También definimos los diferentes IDROL que tendrá nuestra aplicación y que corresponden con la tabla SQL.
define('IDADMIN', 14);
define('IDUSER1', 1);
define('IDUSER2', 2);

//Procesamos la ruta y los metodos.
$control = explode('/',$url);

switch($control[0]) {
  
  case "user":
    require_once("controllers/user.controller.php");
    $user = new UserController($conexion);
    switch(METODO) {
      case "GET":
        switch($control[1]) {
          case "list":
            $user->listarUser();
            break;
          case "rol":
            $user->comprobarRol();
            break;
          case "":
            $user->leerPerfil();
            break;
          case "id":
            $user->obtenerUsuarioPorId($control[2]);
            break;
        }
        break;
      case "POST":
        switch($control[1]) {
          case "login":
            $user->hacerLogin();
            break;
          case "image":
            $user->subirAvatar();
            break;
          case "":
            $user->registrarUser();
          
        }
        break;
        
      case "PUT":
        switch($control[1]) {
          case "":
        $user->editarUser();
        break;
        case "admin":
          $user->darAdmin($control[2]);
          break;
          case "user":
            $user->quitarAdmin($control[2]);
            break;
      }
      case "DELETE":
     
        switch($control[1]) {
        case "" :
        $user->eliminarUsuario();
        break;
        case "id":
          
          $user->eliminarUser($control[2]);
          break;
        }
      default: exit(json_encode(["Bienvenido al Backend con routes"]));  
    }  
    break;

  /*case "admin":
    require_once("controllers/admin.controller.php");
    $admin = new AdminController($conexion);
    switch(METODO) {
      case "GET":
        if(isset($control[1]) && $control[1] == "roles")
          $admin->obtenerRoles();
        else
          $admin->obtenerUsers();
        break;
      case "PUT":
        $admin->cambiarRol();
        break;
      default: exit(json_encode(["Bienvenido al Backend con routes"]));
    }
    break;*/

  case "adopciones":
    
    require_once("controllers/adopciones.controller.php");
    $adopciones = new AdopcionesController($conexion);
    exit(json_encode([METODO]));
    switch(METODO) {
      
      case "GET":
        exit(json_encode(["Bienvenidpapapappas"]));
        $adopciones->comenzarAdopcion($control[1]);
        break;
      case "POST":
        exit(json_encode(["Bienvenidpapapappas"]));
        $adopciones->comenzarAdopcion($control[1]);
        break;
      case "PUT":
        $adopciones->comenzarAdopcion($control[1]);
        break;
      case "DELETE":
        $notas->eliminarAdopcion($control[1]);
        break;
      default: exit(json_encode(["Bienvenido al Backend con routes"]));
    }
    break;

    case "mascotas":
      require_once("controllers/mascotas.controller.php");
      $mascotas = new MascotasController($conexion);
      switch(METODO) {
        case "GET":
          switch($control[1]) {
          case "":
            $mascotas->obtenerMascotas();
            break;
          case "misMascotas":
            $mascotas->obtenerMisMascotas($control[2]);
            break;
          case "todas":
           $mascotas->obtenerTodas();
            break;  
          case "adoptadas":
            
            $mascotas->obtenerAdoptados();
            break;
          }
        case "POST":
          
          switch($control[1]) {
          case "":
            $mascotas->insertarMascotas();
          break;
          case "image":
            $mascotas->subirAvatar($control[2]);
          break;
          case "confirmar":
            $mascotas->comenzarAdopcion($control[2]);
          break;
          }
        case "PUT":
          switch($control[1]) {
          case "":
         $mascotas->editarMascota($control[1]);
          break;
          case "cancelar":
          $mascotas->cancelarAdopcion($control[1]);
          break;
          }
        case "DELETE":
        
          $mascotas->eliminarMascota($control[1]);
          break;
        default: exit(json_encode(["Bienvenido al Backend con routes"]));
      }
      break;
      
    case "sugerencias":
      require_once("controllers/sugerencias.controller.php");
      $sugerencias = new SugerenciasController($conexion);
      switch(METODO){
        case "GET":
          $sugerencias->listarSugerencias();
          break;
        case "POST":
          $sugerencias->insertarSugerencia();
          break;
        case "PUT":
          $mensajes->editarMensaje();
          break;
        case "DELETE":
          $sugerencias->eliminarSugerencia($control[1]);
          break;
        default: exit(json_encode(["Bienvenido al Backend con routes"]));
      }
      break;
      
    default:
    exit(json_encode(["Bienvenido al Backend con routes"]));
}

