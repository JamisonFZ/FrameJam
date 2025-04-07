<?php

namespace FrameJam\Core;

use Illuminate\Database\Eloquent\Model as EloquentModel;

abstract class Model extends EloquentModel
{
    protected $guarded = ['id'];
    
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        
        // Configuração automática da conexão com o banco de dados
        if (!isset($this->connection)) {
            $this->connection = config('database.default');
        }
    }
} 