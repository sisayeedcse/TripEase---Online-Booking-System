<?php
/**
 * Database Connection Class
 * Handles all database operations using PDO
 */

require_once __DIR__ . '/config.php';

class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET
            ];
            
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    // Prevent cloning of the instance
    private function __clone() {}
    
    // Prevent unserializing of the instance
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}

/**
 * Query Builder Class
 * Provides convenient methods for database operations
 */
class QueryBuilder {
    private $db;
    private $table;
    private $query;
    private $bindings = [];
    private $select = '*';
    private $where = [];
    private $orderBy = '';
    private $limit = '';
    private $join = '';
    
    public function __construct($table) {
        $this->db = Database::getInstance()->getConnection();
        $this->table = $table;
    }
    
    public function select($columns = '*') {
        $this->select = is_array($columns) ? implode(', ', $columns) : $columns;
        return $this;
    }
    
    public function where($column, $operator, $value = null) {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }
        
        $placeholder = ':where_' . count($this->where);
        $this->where[] = "$column $operator $placeholder";
        $this->bindings[$placeholder] = $value;
        return $this;
    }
    
    public function orWhere($column, $operator, $value = null) {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }
        
        $placeholder = ':where_' . count($this->where);
        $connector = empty($this->where) ? '' : 'OR ';
        $this->where[] = $connector . "$column $operator $placeholder";
        $this->bindings[$placeholder] = $value;
        return $this;
    }
    
    public function whereIn($column, $values) {
        if (empty($values)) {
            return $this;
        }
        
        $placeholders = [];
        foreach ($values as $i => $value) {
            $placeholder = ':wherein_' . $i;
            $placeholders[] = $placeholder;
            $this->bindings[$placeholder] = $value;
        }
        
        $this->where[] = "$column IN (" . implode(', ', $placeholders) . ")";
        return $this;
    }
    
    public function whereLike($column, $value) {
        $placeholder = ':like_' . count($this->where);
        $this->where[] = "$column LIKE $placeholder";
        $this->bindings[$placeholder] = "%$value%";
        return $this;
    }
    
    public function join($table, $first, $operator, $second) {
        $this->join .= " INNER JOIN $table ON $first $operator $second";
        return $this;
    }
    
    public function leftJoin($table, $first, $operator, $second) {
        $this->join .= " LEFT JOIN $table ON $first $operator $second";
        return $this;
    }
    
    public function orderBy($column, $direction = 'ASC') {
        $this->orderBy = " ORDER BY $column $direction";
        return $this;
    }
    
    public function limit($limit, $offset = 0) {
        $this->limit = " LIMIT $limit";
        if ($offset > 0) {
            $this->limit .= " OFFSET $offset";
        }
        return $this;
    }
    
    public function get() {
        $this->buildSelectQuery();
        $stmt = $this->db->prepare($this->query);
        $stmt->execute($this->bindings);
        return $stmt->fetchAll();
    }
    
    public function first() {
        $this->limit(1);
        $result = $this->get();
        return $result[0] ?? null;
    }
    
    public function count() {
        $this->select = 'COUNT(*) as count';
        $result = $this->first();
        return $result['count'] ?? 0;
    }
    
    public function insert($data) {
        $columns = array_keys($data);
        $placeholders = array_map(function($col) { return ":$col"; }, $columns);
        
        $this->query = "INSERT INTO {$this->table} (" . implode(', ', $columns) . ") 
                       VALUES (" . implode(', ', $placeholders) . ")";
        
        $bindings = [];
        foreach ($data as $key => $value) {
            $bindings[":$key"] = $value;
        }
        
        $stmt = $this->db->prepare($this->query);
        $result = $stmt->execute($bindings);
        
        return $result ? $this->db->lastInsertId() : false;
    }
    
    public function update($data) {
        $sets = [];
        $bindings = [];
        
        foreach ($data as $key => $value) {
            $placeholder = ":set_$key";
            $sets[] = "$key = $placeholder";
            $bindings[$placeholder] = $value;
        }
        
        $this->query = "UPDATE {$this->table} SET " . implode(', ', $sets);
        
        if (!empty($this->where)) {
            $this->query .= " WHERE " . implode(' AND ', $this->where);
            $bindings = array_merge($bindings, $this->bindings);
        }
        
        $stmt = $this->db->prepare($this->query);
        return $stmt->execute($bindings);
    }
    
    public function delete() {
        $this->query = "DELETE FROM {$this->table}";
        
        if (!empty($this->where)) {
            $this->query .= " WHERE " . implode(' AND ', $this->where);
        }
        
        $stmt = $this->db->prepare($this->query);
        return $stmt->execute($this->bindings);
    }
    
    public function exists() {
        return $this->count() > 0;
    }
    
    private function buildSelectQuery() {
        $this->query = "SELECT {$this->select} FROM {$this->table}";
        
        if (!empty($this->join)) {
            $this->query .= $this->join;
        }
        
        if (!empty($this->where)) {
            $this->query .= " WHERE " . implode(' AND ', $this->where);
        }
        
        $this->query .= $this->orderBy . $this->limit;
    }
    
    public function raw($query, $bindings = []) {
        $stmt = $this->db->prepare($query);
        $stmt->execute($bindings);
        return $stmt->fetchAll();
    }
    
    public function beginTransaction() {
        return $this->db->beginTransaction();
    }
    
    public function commit() {
        return $this->db->commit();
    }
    
    public function rollback() {
        return $this->db->rollBack();
    }
}

// Helper function to create a new query builder instance
function db($table) {
    return new QueryBuilder($table);
}
