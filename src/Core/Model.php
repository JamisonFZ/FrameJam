<?php

namespace FrameJam\Core;

use FrameJam\Core\Database\Database;
use PDO;

abstract class Model
{
    protected string $table;
    protected string $primaryKey = 'id';
    protected array $fillable = [];
    protected array $guarded = ['id'];
    protected array $attributes = [];
    protected array $original = [];
    protected bool $exists = false;

    public function __construct(array $attributes = [])
    {
        $this->fill($attributes);
        $this->original = $this->attributes;
    }

    /**
     * Preenche o modelo com os atributos fornecidos
     *
     * @param array $attributes
     * @return $this
     */
    public function fill(array $attributes): self
    {
        foreach ($attributes as $key => $value) {
            $this->setAttribute($key, $value);
        }

        return $this;
    }

    /**
     * Define um atributo no modelo
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function setAttribute(string $key, $value): void
    {
        if ($this->isFillable($key)) {
            $this->attributes[$key] = $value;
        }
    }

    /**
     * Obtém um atributo do modelo
     *
     * @param string $key
     * @return mixed
     */
    public function getAttribute(string $key)
    {
        return $this->attributes[$key] ?? null;
    }

    /**
     * Verifica se um atributo é preenchível
     *
     * @param string $key
     * @return bool
     */
    protected function isFillable(string $key): bool
    {
        if (in_array($key, $this->guarded)) {
            return false;
        }

        return empty($this->fillable) || in_array($key, $this->fillable);
    }

    /**
     * Salva o modelo no banco de dados
     *
     * @return bool
     */
    public function save(): bool
    {
        $db = Database::getInstance();
        
        if ($this->exists) {
            return $this->update();
        }
        
        return $this->insert();
    }

    /**
     * Insere o modelo no banco de dados
     *
     * @return bool
     */
    protected function insert(): bool
    {
        $db = Database::getInstance();
        
        $columns = array_keys($this->attributes);
        $values = array_values($this->attributes);
        $placeholders = array_fill(0, count($columns), '?');
        
        $sql = sprintf(
            "INSERT INTO %s (%s) VALUES (%s)",
            $this->getTable(),
            implode(', ', $columns),
            implode(', ', $placeholders)
        );
        
        $result = $db->query($sql, $values);
        
        if ($result) {
            $this->exists = true;
            $this->attributes[$this->primaryKey] = $db->lastInsertId();
            $this->original = $this->attributes;
        }
        
        return $result !== false;
    }

    /**
     * Atualiza o modelo no banco de dados
     *
     * @return bool
     */
    protected function update(): bool
    {
        $db = Database::getInstance();
        
        $sets = [];
        $values = [];
        
        foreach ($this->attributes as $key => $value) {
            if ($key !== $this->primaryKey) {
                $sets[] = "$key = ?";
                $values[] = $value;
            }
        }
        
        $values[] = $this->attributes[$this->primaryKey];
        
        $sql = sprintf(
            "UPDATE %s SET %s WHERE %s = ?",
            $this->getTable(),
            implode(', ', $sets),
            $this->primaryKey
        );
        
        $result = $db->query($sql, $values);
        
        if ($result) {
            $this->original = $this->attributes;
        }
        
        return $result !== false;
    }

    /**
     * Exclui o modelo do banco de dados
     *
     * @return bool
     */
    public function delete(): bool
    {
        if (!$this->exists) {
            return false;
        }
        
        $db = Database::getInstance();
        
        $sql = sprintf(
            "DELETE FROM %s WHERE %s = ?",
            $this->getTable(),
            $this->primaryKey
        );
        
        $result = $db->query($sql, [$this->attributes[$this->primaryKey]]);
        
        if ($result) {
            $this->exists = false;
        }
        
        return $result !== false;
    }

    /**
     * Obtém o nome da tabela
     *
     * @return string
     */
    public function getTable(): string
    {
        if (isset($this->table)) {
            return $this->table;
        }
        
        // Converte o nome da classe para snake_case para obter o nome da tabela
        $className = (new \ReflectionClass($this))->getShortName();
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $className)) . 's';
    }

    /**
     * Encontra um registro pelo ID
     *
     * @param int $id
     * @return static|null
     */
    public static function find(int $id): ?self
    {
        $instance = new static();
        $db = Database::getInstance();
        
        $sql = sprintf(
            "SELECT * FROM %s WHERE %s = ? LIMIT 1",
            $instance->getTable(),
            $instance->primaryKey
        );
        
        $result = $db->query($sql, [$id])->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            $instance->fill($result);
            $instance->exists = true;
            $instance->original = $instance->attributes;
            return $instance;
        }
        
        return null;
    }

    /**
     * Obtém todos os registros
     *
     * @return array
     */
    public static function all(): array
    {
        $instance = new static();
        $db = Database::getInstance();
        
        $sql = sprintf("SELECT * FROM %s", $instance->getTable());
        
        $results = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        
        $models = [];
        
        foreach ($results as $result) {
            $model = new static();
            $model->fill($result);
            $model->exists = true;
            $model->original = $model->attributes;
            $models[] = $model;
        }
        
        return $models;
    }

    /**
     * Obtém um atributo do modelo
     *
     * @param string $name
     * @return mixed
     */
    public function __get(string $name)
    {
        return $this->getAttribute($name);
    }

    /**
     * Define um atributo no modelo
     *
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function __set(string $name, $value): void
    {
        $this->setAttribute($name, $value);
    }
} 