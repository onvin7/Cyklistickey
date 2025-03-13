<?php
class Database
{
    /*
    private $host = 'db.mp.spse-net.cz';
    private $db_name = 'vincenon21_1';
    private $username = 'vincenon21';
    private $password = 'larahobujulu';
    */

    private $host = 'localhost';
    private $db_name = 'cyklistickey';
    private $username = 'root';
    private $password = '';
    private $connection;

    public function connect()
    {
        $this->connection = null;

        try {
            $this->connection = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Connection error: " . $e->getMessage();
        }

        return $this->connection;
    }
}
