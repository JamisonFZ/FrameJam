<?php

namespace FrameJam\Core\Validation;

use FrameJam\Core\Application;

class Validator
{
    private array $data;
    private array $rules;
    private array $errors = [];

    public function __construct(array $data, array $rules)
    {
        $this->data = $data;
        $this->rules = $rules;
    }

    public function validate(): bool
    {
        $this->errors = [];

        foreach ($this->rules as $field => $rules) {
            foreach ($rules as $rule) {
                if (!$this->validateField($field, $rule)) {
                    break;
                }
            }
        }

        return empty($this->errors);
    }

    private function validateField(string $field, string $rule): bool
    {
        $value = $this->data[$field] ?? null;

        switch ($rule) {
            case 'required':
                if (empty($value)) {
                    $this->addError($field, 'O campo é obrigatório.');
                    return false;
                }
                break;

            case 'email':
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, 'O campo deve ser um e-mail válido.');
                    return false;
                }
                break;

            case 'min:':
                $min = (int) substr($rule, 4);
                if (strlen($value) < $min) {
                    $this->addError($field, "O campo deve ter no mínimo {$min} caracteres.");
                    return false;
                }
                break;

            case 'max:':
                $max = (int) substr($rule, 4);
                if (strlen($value) > $max) {
                    $this->addError($field, "O campo deve ter no máximo {$max} caracteres.");
                    return false;
                }
                break;

            case 'numeric':
                if (!is_numeric($value)) {
                    $this->addError($field, 'O campo deve ser numérico.');
                    return false;
                }
                break;

            case 'date':
                if (!strtotime($value)) {
                    $this->addError($field, 'O campo deve ser uma data válida.');
                    return false;
                }
                break;

            case 'url':
                if (!filter_var($value, FILTER_VALIDATE_URL)) {
                    $this->addError($field, 'O campo deve ser uma URL válida.');
                    return false;
                }
                break;

            case 'unique:':
                [$table, $column] = explode(',', substr($rule, 7));
                if ($this->isUnique($table, $column, $value)) {
                    $this->addError($field, 'Este valor já está em uso.');
                    return false;
                }
                break;

            case 'confirmed':
                if ($value !== ($this->data[$field . '_confirmation'] ?? null)) {
                    $this->addError($field, 'A confirmação não corresponde.');
                    return false;
                }
                break;
        }

        return true;
    }

    private function isUnique(string $table, string $column, $value): bool
    {
        $db = Application::getInstance()->getContainer()->make('db');
        $stmt = $db->prepare("SELECT COUNT(*) FROM {$table} WHERE {$column} = ?");
        $stmt->execute([$value]);
        return $stmt->fetchColumn() > 0;
    }

    private function addError(string $field, string $message): void
    {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = [];
        }
        $this->errors[$field][] = $message;
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function fails(): bool
    {
        return !empty($this->errors);
    }

    public function validated(): array
    {
        return array_intersect_key($this->data, array_flip(array_keys($this->rules)));
    }
} 