<?php

/**
 * ===== ===== ===== ===== ===== ===== ===== ===== =====
 * AUTHOR : Mr. Dylan Kong A Siou 
 * CHECK THE README FOR ADDITIONNAL INFORMATION
 * ===== ===== ===== ===== ===== ===== ===== ===== =====
 */

namespace tete0148;

class Database {
    
    /**
    * @var $pdo \PDO
    */
    private $pdo = null;
    
    private $host = 'iutbg-lamp.univ-lyon1.fr';
    private $user = 'p1601344';
    private $pass = '11601344';
    private $db   = 'p1601344';
    
    public function __construct()
    {
        try {
            $this->pdo = new \PDO('mysql:host=' . $this->host . ';dbname=' . $this->db, $this->user, $this->pass);            
            $this->pdo->exec('SET NAMES utf8');
            $this->pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            die('Unable to connect to the database');
        }
    }
    
    public function select($table, $columns = '*', $whereConditions = '')
    {
        $sql = 'SELECT ' . $columns . ' FROM ' . $table . 
                             ((strlen($whereConditions) > 0) ? ' WHERE ' . $whereConditions : '');
        $query = $this->pdo->query($sql);
        $results = $query->fetchAll();
        
        $query->closeCursor();
        
        return $results;
    }
    
    /**
    * @param $table string
    * @parem $data array - An array where indexes are columns and values are values to insert to the database
    */
    public function insert($table, $data = [])
    {
        if(empty($data))
            throw new \Exception("Data array can't be empty");
        
        $rows = '';
        $values = '';
        $params = [];
        foreach($data as $row => $value) {
            $rows .= ($row . ',');
            $values .= (':' . $row . ',');
            $params[':' . $row] = $value;
        }
        // remove leading commas
        $rows = substr($rows, 0, strlen($rows) - 1);  
        $values = substr($values, 0, strlen($values) - 1);

        
        $statement = $this->pdo->prepare('INSERT INTO ' . $table . '(' . $rows . ') VALUES(' . $values . ')');
        $statement->execute($params);
        $statement->closeCursor();
    }
    
    /**
    * @param $table string
    * @param $data array - An array where indexes are columns and values are values to insert to the database
    * @param $whereConditions string - Conditions after the WHERE keybord in the request
    */
    public function update($table, $data = [], $whereConditions = '', $secure = true)
    {
        if(empty($data))
            throw new \Exception("Data array can't be empty");
        
        if($secure) {
            if(empty($whereConditions))
                throw new \Exception("Warning: you are updating a table without constraints. Set 'secure' to false if you really want to do that");
        }
        
        $updates = '';
        $params = [];
        foreach($data as $row => $value) {
            $updates .= ($row . ' = :' . $row . ',');
            $params[':' . $row] = $value;
        }
        // remove leading commas
        $updates = substr($updates, 0, strlen($updates) - 1);
        
        $statement = $this->pdo->prepare('UPDATE ' . $table . ' SET ' . $updates . ((strlen($whereConditions) > 0) ? ' WHERE ' . $whereConditions : ''));
        $statement->execute($params);
        $statement->closeCursor();
    }
}