<?php
class ConexaoBanco extends PDO 
{
    private static $instance = null;

    public function __construct($databaseName, $user, $password)
    {
        parent::__construct($databaseName, $user, $password);
    }    

    public static function getInstance()
    {
        try {
            if(!isset(self::$instance)) {
                self::$instance = new ConexaoBanco("mysql:dbname=mecanica;host=localhost","root","");
            }
            return self::$instance;
        } catch(PDOException $error) {
            echo "Erro ao conectar! ".$error;
        }
    }
}