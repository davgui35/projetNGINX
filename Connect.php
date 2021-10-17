<?php

class Connect{

    private $login;
    private $pass;
    private $connect;

    /**
     * Construct connexion
     * @param string $db dbname
     * @param string $login
     * @param string $password
     */
    public function __construct($db, $login ="newuser", $pass="password")
    {
        $this->login = $login;
        $this->pass = $pass;
        $this->db = $db;
        $this->connexion();
    }

    /**
     * Database connection
     */
    public function connexion(){
        try {
            $pdo = new PDO(
                'mysql:host=localhost;dbname='.$this->db.';charset=utf8mb4', 
                $this->login, 
                $this->pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::FETCH_ASSOC);
            $this->connect = $pdo;
        } catch (PDOException $e) {
            $msg = 'ERREUR PDO dans '. $e->getFile() . ' L.'. $e->getLine(). ' : ' . $e->getMessage();
            die($msg);
        }
    } 

    /**
     * find user by name
     */
    public function find($sql, Array $condition = null) {
        $statement = $this->connect->prepare($sql);

        if($condition) {
            foreach ($condition as $value) {
                $statement->bindParam($value[0], $value[1], $value[2]);
            }
        }
        $statement->execute();

        return $statement->fetchAll();
        $statement->closeCursor();
        $statement=NULL;
    }
}