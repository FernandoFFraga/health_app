<?php

namespace App\Core;

use PDOStatement;
use App\Core\Connect;

abstract class Model
{
    
    /** @var object|null */
    protected $data;

    /** @var PDOException|null */
    protected $fail;

    /** @var string|null */
    protected $message;

    public function __set($name, $value)
    {
        if (empty($this->data)) {
            $this->data = new \stdClass();
        }

        $this->data->$name = $value;
    }

    public function __isset($name)
    {
        return isset($this->data->$name);
    }

    public function __get($name)
    {
        return ($this->data->$name ?? null);
    }

    /**
     * @return null|object
     */ 
    public function data() : ?object
    {
        return $this->data;
    }

    /**
     * @return null|PDOException
     */ 
    public function fail() : ?string
    {
        return $this->fail;
    }

    /**
     * @return null|string
     */ 
    public function message() : ?string
    {
        return $this->message;
    }

    /**
     * @param string $entity
     * @param array $data
     * @return integer|null
     */
    protected function create(string $entity, array $data) : ?int
    {
        try {
            $columns = implode(", ", array_keys($data));
            $values = ":" . implode(", :", array_keys($data));

            $stmt = Connect::getInstance()->prepare(
                "INSERT INTO {$entity} ({$columns}) VALUES ({$values})"
            );

            $stmt->execute($this->filter($data));

            return Connect::getInstance()->lastInsertId();

        } catch (\PDOException $execpetion) {
            $this->fail = $execpetion;
            return null;
        }
    }

    /**
     * Read
     *
     * @param string $select
     * @param string $params
     * @return PDOStatement
     */
    protected function read(string $select, string $params = null) :?PDOStatement
    {
        try {
            $stmt = Connect::getInstance()->prepare($select);
            if ($params) {
                parse_str($params, $paramsArray);
                foreach ($paramsArray as $key => $value) {
                    $type = (is_numeric($value) ? \PDO::PARAM_INT : \PDO::PARAM_STR);
                    $stmt->bindValue(":{$key}", $value, $type);
                }
            }

            $stmt->execute();
            return $stmt;

        } catch (\PDOException $execpetion) {
            $this->fail = $execpetion;

            var_dump($this->fail);
            
            return null;
        }

    }

    /**
     * @param string $entity
     * @param array $data
     * @param string $terms
     * @param string $params
     * @return integer|null
     */
    protected function update(string $entity, array $data, string $terms, string $params): ?int
    {
        try {
            $dataSet = [];
            foreach ($data as $bind => $value) {
                $dataSet[] = "{$bind} = :{$bind}";
            }
            $dataSet = implode(", ", $dataSet);
            parse_str($params, $params);

            $stmt = Connect::getInstance()->prepare("UPDATE {$entity} SET {$dataSet} WHERE {$terms}");
            $stmt->execute($this->filter(array_merge($data, $params)));
            return ($stmt->rowCount() ?? 1);
        } catch (\PDOException $execpetion) {
            $this->fail = $execpetion;
            return null;
        }
    }

    /**
     * @param string $entity
     * @param string $terms
     * @param string $params
     * @return integer|null
     */
    protected function delete(string $entity, string $terms, string $params) : ?int
    {
        try {
            $stmt = Connect::getInstance()->prepare("DELETE FROM {$entity} WHERE {$terms}");
            parse_str($params, $paramsA);
            $stmt->execute($paramsA);
            return ($stmt->rowCount() ?? 1);
        } catch (\PDOException $execpetion) {
            $this->fail = $execpetion;
            return null;
        }
    }

    /**
     * @return array|null
     */
    protected function safe() : ?array
    {

        $safe = (array)$this->data;
        foreach ((array) $this->safe as $unset) {
            unset ($safe[$unset]);
        }

        return $safe;
    }

    /**
     * @param array $data
     * @return array|null
     */
    private function filter(array $data): ?array
    {
        $filter = [];
        foreach ($data as $key => $value) {
            $filter[$key] = (is_null($value) ? null : filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS));
        }

        return $filter;
    }
}
