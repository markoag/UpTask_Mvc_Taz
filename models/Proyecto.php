<?php

namespace Model;

use Model\ActiveRecord;

class Proyecto extends ActiveRecord
{
    protected static $tabla = 'proyectos';
    protected static $columnasDB = [
        'id',
        'nombre',
        'url',
        'usuarioId',
        'createdAt',
        'updatedAt'
    ];

    public $id;
    public $nombre;
    public $url;
    public $usuarioId;
    public $createdAt;
    public $updatedAt;

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->url = $args['url'] ?? '';
        $this->usuarioId = $args['usuarioId'] ?? '';
        $this->createdAt = $args['createdAt'] ?? null;
        $this->updatedAt = $args['updatedAt'] ?? null;
    }

    public function validarProyecto()
    {
        if (!$this->nombre) {
            self::$alertas['error'][] = 'El nombre del proyecto es obligatorio';
        }
        
        return self::$alertas;
    }

}