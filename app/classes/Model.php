<?php
class Model
{
    public static $qb;
    public $tableName;
    protected $conn;

    public function __construct()
    {
        self::$qb = Database::getInstance();
        $this->conn = self::$qb->conn;
        $this->tableName = $this->getTable();
    }

    protected function query($query)
    {
        $result = $this->conn->query($query);
        if (!$result) {
            throw new Exception('Request execution error');
        }
        return $result->fetch(PDO::FETCH_ASSOC);
    }

    
    protected function multipleQuery($query)
    {
        $result = $this->conn->query($query);
        if (!$result) {
            throw new PDOException('Request execution error');
        }
        foreach ($result->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $data[] = $row;
        }
        return $data;
    }

    public static function factory($name, $id = false)
    {
        $class = 'Model_' . ucfirst($name);
        return new $class($id);
    }

    protected function getTable()
    {
        return strtolower(
            str_replace('Model_', '', get_called_class()) . 's'
        );
    }

}