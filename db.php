<?php
session_start();
class db{
    /**
     *
     * @var \PDO 
     */
     
    private static $connection;
    
    function __construct(){
        if(self::$connection==null){
            $servername = "remotemysql.com";
            $username = "8OhRjtA3Sn";
            $password = "parA6a2rzF";
            $database_name ="8OhRjtA3Sn";
            $port = 3306;
            try {
                self::$connection = new \PDO("mysql:host={$servername};dbname={$database_name}", $username, $password);
                self::$connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION); 
//                self::$connection->setAttribute(\PDO::ATTR_TIMEOUT, 500);
            } catch (\PDOException $e) {
                die($e->getMessage());
            }
        }
    }
    function __destruct() {
        return;
    }
    /**
     * 
     * @return \PDO
     */
     
    function connection(){
        return self::$connection;
    }
}
?>
