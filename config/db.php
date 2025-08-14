<?php
class Database
{
    
    private $host = 'md413.wedos.net';
    private $db_name = 'd340619_blog';
    private $username = 'w340619_blog';
    private $password = 'kaYak714?';


    
    /*
    private $host = 'localhost';
    private $db_name = 'cyklistickey';
    private $username = 'root';
    private $password = '';
    */
    private $connection;

    public function connect()
    {
        $this->connection = null;

        try {
            $this->connection = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password,
                [
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8mb4'"
                ]
            );
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Connection error: " . $e->getMessage();
        }

        return $this->connection;
    }
}
