<?php
class db{
  private $host = "localhost";
  private $database = "id18038849_back";
  private $user = "id18038849_mario";
  private $password = "Olvidado01??";
  private $dbPDO;
  private $JWTkey = "ClaveSuperSecreta";

  public function getConnection() {
    $this->dbPDO = null;
    try{
      $this->dbPDO = new PDO('mysql:host='.$this->host.';dbname='.$this->database,$this->user,$this->password);
    } catch(PDOException $exception) {
      echo "Conexión Fallida: " . $exception->getMessage();
    }
    return $this->dbPDO;
  }

  public function getClave() {
    return $this->JWTkey;
  }
}
