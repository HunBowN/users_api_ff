<?php
class Database {

  private static $connection = false;

  public  $conn;
  

  function __construct()
  {
    $conf = require($_SERVER['DOCUMENT_ROOT'] . '/app/modules/database/config.php');
   
    try {
      $this->conn = new PDO(
        "mysql:host={$conf['host']};port={$conf['port']};dbname={$conf['database']};user={$conf['user']};password={$conf['password']};charset={$conf['charset']}",
      );
    } catch (PDOException $ex) {
      (new Logger('storage/logs/db.log'))->log('Connection error: ' . $ex->getMessage());
      echo 'DB connection error: ' . $ex->getMessage();
    }
    
  }

  public static function getInstance()
  {
    if (!self::$connection)
    {
      self::$connection = new Database(); 
    }
    return self::$connection;
  }
}
