<?php
// db.php - Datenbank-Verbindungsklasse für die Sprachapp

class Database {
    private $pdo;
    
    public function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die("Datenbank-Verbindungsfehler: " . $e->getMessage());
        }
    }
    
    // SELECT-Abfrage mit mehreren Ergebnissen
    public function select($sql, $params = []) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    // SELECT-Abfrage mit einem Ergebnis
    public function selectOne($sql, $params = []) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }
    
    // INSERT-Abfrage
    public function insert($table, $data) {
        $fields = array_keys($data);
        $placeholders = array_map(function($field) {
            return ':' . $field;
        }, $fields);
        
        $sql = "INSERT INTO {$table} (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $placeholders) . ")";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);
        
        return $this->pdo->lastInsertId();
    }
    
    // UPDATE-Abfrage
    public function update($table, $data, $where, $whereParams = []) {
        $setStatements = array_map(function($field) {
            return $field . ' = :' . $field;
        }, array_keys($data));
        
        $sql = "UPDATE {$table} SET " . implode(', ', $setStatements) . " WHERE " . $where;
        
        $stmt = $this->pdo->prepare($sql);
        $params = array_merge($data, $whereParams);
        $stmt->execute($params);
        
        return $stmt->rowCount();
    }
    
    // DELETE-Abfrage
    public function delete($table, $where, $params = []) {
        $sql = "DELETE FROM {$table} WHERE " . $where;
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->rowCount();
    }
    
    // Transaktion starten
    public function beginTransaction() {
        return $this->pdo->beginTransaction();
    }
    
    // Transaktion bestätigen
    public function commit() {
        return $this->pdo->commit();
    }
    
    // Transaktion zurückrollen
    public function rollback() {
        return $this->pdo->rollBack();
    }
}

// Datenbank-Instanz erstellen
$db = new Database();