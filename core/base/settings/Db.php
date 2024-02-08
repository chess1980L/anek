<?php

namespace core\base\Settings;

use core\base\controller\Singleton;
use PDO;

class Db
{
    private $pdo;
    use Singleton;

    private function __construct()
    {
        $this->host = 'localhost';
        $this->dbname = 'anekdotes';
        $this->username = 'root';
        $this->password = '';

        $dsn = "mysql:host={$this->host};dbname={$this->dbname}";
        $this->pdo = new PDO($dsn, $this->username, $this->password);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function getPdo()
    {
        return $this->pdo;
    }
}