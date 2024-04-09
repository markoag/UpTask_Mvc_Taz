<?php

namespace Controllers;

use Classes\Email;
use Model\Usuario;
use MVC\Router;

class LoginController
{
    public static function login(Router $render)
    {
        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth = new Usuario($_POST);

            $alertas = $auth->validarLogin();

            if (empty($alertas)) {
                $usuario = Usuario::where('email', $auth->email);

                if ($usuario) {
                    // Revisar si el password es correcto
                    if ($usuario->comprobarPasswordAndVerificado($auth->password)) {
                        // Autenticar el usuario
                        session_start();
                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;

                        // Redireccionar

                        header('Location: /dashboard');

                    }
                } else {
                    Usuario::setAlerta('error', 'Usuario no encontrado');
                }
            }
        }

        $alertas = Usuario::getAlertas();
        // Renderizar la vista
        $render->render('auth/login', [
            'titulo' => 'Iniciar Sesión',
            'alertas' => $alertas
        ]);
    }
    public static function logout()
    {
        session_start();
        session_destroy();
        header('Location: /');
    }

    public static function crear(Router $render)
    {
        $usuario = new Usuario;
        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarNuevaCuenta();

            if (empty($alertas)) {
                $existeUsuario = Usuario::where('email', $usuario->email);

                if ($existeUsuario) {
                    Usuario::setAlerta('error', 'Este e-mail ya está registrado');
                    $alertas = Usuario::getAlertas();
                } else {
                    // Hashear el password
                    $usuario->hashPassword();
                    //Eliminar el password2
                    unset($usuario->password2);
                    // Generar un token
                    $usuario->generarToken();
                    // Enviar un correo
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarEmail();

                    // Guardar el usuario en la BD
                    $usuario->createdAt = date('Y-m-d H:i:s');

                    $resultado = $usuario->guardar();

                    if ($resultado) {
                        header('Location: /mensaje');
                    }

                }
            }
        }

        $alertas = Usuario::getAlertas();
        // Renderizar la vista
        $render->render('auth/crear', [
            'titulo' => 'Crear Cuenta',
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }

    public static function olvide(Router $render)
    {
        $alertas = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth = new Usuario($_POST);
            $alertas = $auth->validarEmail();

            if (empty($alertas)) {
                $usuario = Usuario::where('email', $auth->email);

                if ($usuario && $usuario->confirmado) {
                    // Generar un token
                    $usuario->generarToken();

                    // Enviar un correo
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarInstrucciones();

                    // Guardar el usuario en la BD
                    $usuario->updatedAt = date('Y-m-d H:i:s');
                    $usuario->guardar();

                    Usuario::setAlerta('exito', 'Revisa tu email para cambiar tu contraseña');

                } else {
                    Usuario::setAlerta('error', 'Usuario no encontrado o no confirmado');
                }
            }
        }

        $alertas = Usuario::getAlertas();
        // Renderizar la vista
        $render->render('auth/olvide', [
            'titulo' => 'Recuperar Contraseña',
            'alertas' => $alertas
        ]);
    }

    public static function nueva(Router $render)
    {
        $alertas = [];
        $error = false;
        $token = s($_GET['token']);

        // Buscar el usuario con el token
        $usuario = Usuario::where('token', $token);

        if (empty($usuario)) {
            Usuario::setAlerta('error', 'Token no válido');
            $error = true;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Leer el nuevo password y guardarlo
            $auth = new Usuario($_POST);

            $alertas = $auth->validarPassword();

            if (empty($alertas)) {

                // Actualizar el password
                $usuario->password = $auth->password;
                $usuario->hashPassword();
                unset($usuario->password2);
                $usuario->token = null;
                $usuario->updatedAt = date('Y-m-d H:i:s');
                $resultado = $usuario->guardar();

                if ($resultado) {
                    Usuario::setAlerta('exito', 'Contraseña actualizada');
                }

            }
        }

        $alertas = Usuario::getAlertas();
        // Renderizar la vista
        $render->render('auth/nueva', [
            'titulo' => 'Nueva Contraseña',
            'alertas' => $alertas,
            'error' => $error,
            'resultado' => $resultado
        ]);
    }

    public static function mensaje(Router $render)
    {
        // Renderizar la vista
        $render->render('auth/mensaje', [
            'titulo' => 'Mensaje Enviado'
        ]);

    }
    public static function confirmar(Router $render)
    {
        $alertas = [];
        $token = s($_GET['token']);
        $usuario = Usuario::where('token', $token);

        if (empty($usuario)) {
            // Mensaje de error
            Usuario::setAlerta('error', 'Token no válido');
        } else {
            // Modificar a usuario confirmado
            $usuario->confirmado = '1';
            $usuario->token = null;
            $usuario->updatedAt = date('Y-m-d H:i:s');
            $usuario->guardar();
            Usuario::setAlerta('exito', 'Cuenta confirmada');
        }
        $alertas = Usuario::getAlertas();
        // Renderizar la vista
        $render->render('auth/confirmar', [
            'titulo' => 'Confirmar Cuenta',
            'alertas' => $alertas
        ]);

    }
}