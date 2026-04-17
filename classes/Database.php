<?php

class Database {
    private $host = "127.0.0.1";
    private $username = "root";
    private $password = "";
    private $dbname = "collegescheduling_db";

    protected $conn;

    public function connect() {

        $this->conn = new PDO("mysql:host=$this->host;dbname=$this->dbname", $this->username, $this->password);

        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->conn->exec("SET time_zone = '+08:00'");

        return $this->conn;
    }
}
