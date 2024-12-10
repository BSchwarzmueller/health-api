<?php

namespace utils;

class QueryBuilder
{
    protected $table;
    protected $select = '*';
    protected $where = [];
    protected $bindings = [];
    protected $limit;
    protected $offset;

    public function table($table)
    {
        $this->table = $table;
        return $this;
    }

    public function select($columns = '*')
    {
        $this->select = is_array($columns) ? implode(', ', $columns) : $columns;
        return $this;
    }

    public function where($column, $operator, $value)
    {
        $this->where[] = "$column $operator ?";
        $this->bindings[] = $value;
        return $this;
    }

    public function limit($limit)
    {
        $this->limit = $limit;
        return $this;
    }

    public function offset($offset)
    {
        $this->offset = $offset;
        return $this;
    }

    public function get()
    {
        $sql = "SELECT $this->select FROM $this->table";

        if ($this->where) {
            $sql .= ' WHERE ' . implode(' AND ', $this->where);
        }

        if ($this->limit) {
            $sql .= ' LIMIT ' . $this->limit;
        }

        if ($this->offset) {
            $sql .= ' OFFSET ' . $this->offset;
        }

        return ['sql' => $sql, 'bindings' => $this->bindings];
    }

    public function insert($data)
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $sql = "INSERT INTO $this->table ($columns) VALUES ($placeholders)";
        $bindings = array_values($data);

        return ['sql' => $sql, 'bindings' => $bindings];
    }

    public function update($data, $id)
    {
        $set = implode(', ', array_map(fn($key) => "$key = ?", array_keys($data)));

        $sql = "UPDATE $this->table SET $set WHERE id = ?";
        $bindings = [...array_values($data), $id];

        return ['sql' => $sql, 'bindings' => $bindings];
    }

    public function delete($id)
    {
        $sql = "DELETE FROM $this->table WHERE id = ?";
        $bindings = [$id];

        return ['sql' => $sql, 'bindings' => $bindings];
    }
}