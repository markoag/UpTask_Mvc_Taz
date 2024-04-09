<?php

namespace Model;

class Usuario extends ActiveRecord
{
    protected static $tabla = 'usuarios';
    protected static $columnasDB = [
        'id',
        'nombre',
        'email',
        'password',
        'confirmado',
        'token',
        'createdAt',
        'updatedAt'
    ];

    public $id;
    public $nombre;
    public $email;
    public $password;
    public $password2;
    public $confirmado;
    public $token;
    public $createdAt;
    public $updatedAt;

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->password = $args['password'] ?? '';
        $this->password2 = $args['password2'] ?? '';
        $this->confirmado = $args['confirmado'] ?? 0;
        $this->token = $args['token'] ?? '';
        $this->createdAt = $args['createdAt'] ?? null;
        $this->updatedAt = $args['updatedAt'] ?? null;
    }

    // Validación
    public function validarNuevaCuenta()
    {
        if (!$this->nombre) {
            self::$alertas['error'][] = "El nombre es obligatorio";
        }

        if (!$this->email) {
            self::$alertas['error'][] = "El email es obligatorio";
        }

        if (!$this->password) {
            self::$alertas['error'][] = "La contraseña es obligatoria";
        }

        if ($this->password && strlen($this->password) < 6) {
            self::$alertas['error'][] = "La contraseña debe tener al menos 6 caracteres";
        }
        if ($this->password !== $this->password2) {
            self::$alertas['error'][] = "Las contraseñas no coinciden";
        }

        return self::$alertas;
    }

    // Validar Login
    public function validarLogin()
    {
        if (!$this->email) {
            self::$alertas['error'][] = "El email es obligatorio";
        }

        if (!$this->password) {
            self::$alertas['error'][] = "La contraseña es obligatoria";
        }

        return self::$alertas;
    }

    // Validar Email
    public function validarEmail()
    {
        if (!$this->email) {
            self::$alertas['error'][] = "El email es obligatorio";
        }

        return self::$alertas;
    }

    public function validarPassword()
    {
        if (!$this->password) {
            self::$alertas['error'][] = "El password es obligatorio";
        }

        if (strlen($this->password) < 6) {
            self::$alertas['error'][] = "El password debe tener al menos 6 caracteres";
        }

        if ($this->password !== $this->password2) {
            self::$alertas['error'][] = "Las contraseñas no coinciden";
        }

        return self::$alertas;
    }

    // Verificar si el usuario existe
    public function existeUsuario()
    {
        $query = "SELECT * FROM " . self::$tabla . " WHERE email = '" . $this->email . "' LIMIT 1";
        $resultado = self::$db->query($query);

        if ($resultado->num_rows) {
            self::$alertas['error'][] = "El usuario ya existe";
            return false;
        }

        return $resultado;
    }

    public function hashPassword()
    {
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
    }
    // Generar un token
    public function generarToken()
    {
        $this->token = bin2hex(random_bytes(20));
    }
    // Comprobar password
    public function comprobarPasswordAndVerificado($password)
    {
        $resultado = password_verify($password, $this->password);

        if (!$resultado || !$this->confirmado) {
            self::$alertas['error'][] = "Contraseña Incorrecta o tu cuenta no ha sido confirmada";
            return false;
        } else {
            return true;
        }
    }
}
