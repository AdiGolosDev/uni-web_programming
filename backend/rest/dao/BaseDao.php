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
        $results = $this->query($query, $params);
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


    // CRUD

    public function get_all() {
        return $this->query("SELECT * FROM {$this->table_name} ORDER BY {$this->pk()}");
    }

    public function get_by_id($id) {
        return $this->query_unique("SELECT * FROM {$this->table_name} WHERE {$this->pk()} = :id", ['id' => $id]);
    }

    public function count_all() {
        return (int) $this->query_unique("SELECT COUNT(*) AS total FROM {$this->table_name}")['total'];
    }

    public function add($entity) {
        $entity = $this->only_columns($entity);
        if (count($entity) === 0) {
            throw new InvalidArgumentException("Nothing to insert into {$this->table_name}: no writable columns");
        }
        $columns = array_keys($entity);

        $query = "INSERT INTO {$this->table_name} (" . implode(', ', $columns) . ")" . " VALUES (:" . implode(', :', $columns) . ")";
        $this->execute($query, $entity);

        if ($this->primary_key !== null) {
            $entity[$this->primary_key] = (int) self::connection()->lastInsertId();
        }
        return $entity;
    }

    public function update($entity, $id) {
        $pk = $this->pk();
        $entity = $this->only_columns($entity);
        if (count($entity) === 0) {
            return 0;
        }

        $set = [];
        foreach (array_keys($entity) as $column) {
            $set[] = "$column = :$column";
        }
        $query = "UPDATE {$this->table_name} SET " . implode(', ', $set) . " WHERE $pk = :id";

        $entity['id'] = $id;
        return $this->execute($query, $entity);
    }

    public function delete($id) {
        return $this->execute("DELETE FROM {$this->table_name} WHERE {$this->pk()} = :id", ['id' => $id]);
    }

    // transactions
    public function begin_transaction() { self::connection()->beginTransaction(); }
    public function commit() { self::connection()->commit(); }
    public function rollback() { if (self::connection()->inTransaction()) self::connection()->rollBack(); }

    protected function only_columns($entity) {
        $entity = array_intersect_key($entity, array_flip($this->columns));
        foreach ($entity as $column => $value) {
            if (is_bool($value)) $entity[$column] = (int) $value;
        }
        return $entity;
    }

}
