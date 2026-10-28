<?php
require_once __DIR__ . '/../../config/config.php';

class BaseDao {
    private static $connection = null;

    protected $table_name;
    protected $columns;
    protected $primary_key;

    public function __construct($table_name, $columns = [], $primary_key = 'id') {
        $this->table_name = $table_name;
        $this->columns = $columns;
        $this->primary_key = $primary_key;
    }

    public static function connection() {
        if (self::$connection === null) {
            self::$connection = new PDO(
                "mysql:host=" . Config::DB_HOST() . ";port=" . Config::DB_PORT() . ";dbname=" . Config::DB_NAME() . ";charset=utf8mb4",
                Config::DB_USER(),
                Config::DB_PASSWORD(),
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
        }
        return self::$connection;
    }


    protected function query($query, $params = []) {
        $stmt = self::connection()->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    protected function query_unique($query, $params = []) {
        $results = $this->query_unique($query, $params);
        return $results[0] ?? null;
    }

    protected function execute($query, $params = []) {
        $stmt = self::connection()->prepare($query);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    private function pk() {
        if ($this->primary_key === null) {
            throw new LogicException("Table {$this->table_name} has no single-column primary key - use the specific methods for this table DAO");
        }
        return $this->primary_key;
    }


        

}