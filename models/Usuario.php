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
    public $passwordActual;
    public $passwordNuevo;
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
        $this->passwordActual = $args['passwordActual'] ?? '';
        $this->passwordNuevo = $args['passwordNuevo'] ?? '';
        $this->confirmado = $args['confirmado'] ?? 0;
        $this->token = $args['token'] ?? '';
        $this->createdAt = $args['createdAt'] ?? null;
        $this->updatedAt = $args['updatedAt'] ?? null;
    }

    // Validación
    public function validarNuevaCuenta() : array
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
    public function validarLogin() : array
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
    public function validarEmail() : array
    {
        if (!$this->email) {
            self::$alertas['error'][] = "El email es obligatorio";
        }

        return self::$alertas;
    }

    public function validarPassword() : array
    {
        if (!$this->password) {
            self::$alertas['error'][] = "La contraseña es obligatoria";
        }

        if (strlen($this->password) < 6) {
            self::$alertas['error'][] = "La contraseña debe tener al menos 6 caracteres";
        }

        if ($this->password !== $this->password2) {
            self::$alertas['error'][] = "Las contraseñas no coinciden";
        }

        return self::$alertas;
    }
    public function nuevoPassword() : array
    {
        if (!$this->passwordActual) {
            self::$alertas['error'][] = "La contraseña actual es obligatorio";
        }

        if (!$this->passwordNuevo || strlen($this->passwordNuevo) < 6) {
            self::$alertas['error'][] = "La contraseña nueva es obligatorio o debe tener al menos 6 caracteres";
        }

        if ($this->passwordNuevo !== $this->password2) {
            self::$alertas['error'][] = "Las contraseñas no coinciden";
        }

        if ($this->passwordNuevo && $this->passwordActual && $this->passwordNuevo === $this->passwordActual) {
            self::$alertas['error'][] = "La contraseña nueva debe ser diferente a la actual";
        }

        return self::$alertas;
    }

    // Comprobar el password
    public function comprobarPassword() : bool
    {
        return password_verify($this->passwordActual, $this->password);
    }
    public function hashPassword() : void
    {
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
    }

    public function validar_perfil() : array
    {
        if (!$this->nombre) {
            self::$alertas['error'][] = "El nombre es obligatorio";
        }

        if (!$this->email) {
            self::$alertas['error'][] = "El email es obligatorio";
        }

        if($this->nombre === $_SESSION['nombre'] && $this->email === $_SESSION['email']) {
            self::$alertas['error'][] = "No hay cambios que guardar";
        }
        return self::$alertas;
    }

    // Verificar si el usuario existe
    public function existeUsuario() : bool
    {
        $query = "SELECT * FROM " . self::$tabla . " WHERE email = '" . $this->email . "' LIMIT 1";
        $resultado = self::$db->query($query);

        if ($resultado->num_rows) {
            self::$alertas['error'][] = "El usuario ya existe";
            return false;
        }

        return $resultado;
    }
    
    // Generar un token
    public function generarToken() : void
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
