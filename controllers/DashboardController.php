<?php

namespace Controllers;

use MVC\Router;
use Model\Usuario;
use Model\Proyecto;

class DashboardController
{
    public static function index(Router $render)
    {
        session_start();

        isAuth();

        $id = $_SESSION['id'];
        $proyectos = Proyecto::perteneceA('usuarioId', $id);

        $render->render('dashboard/index', [
            'titulo' => 'Proyectos',
            'proyectos' => $proyectos
        ]);
    }

    public static function nuevo(Router $render)
    {
        session_start();
        isAuth();
        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $proyecto = new Proyecto($_POST);

            // Validar
            $alertas = $proyecto->validarProyecto();

            if (empty($alertas)) {
                // Generar URL
                $proyecto->url = md5(uniqid(rand(), true));

                // Almacenar el creador del proyecto
                $proyecto->usuarioId = $_SESSION['id'];

                // Guardar en la BD
                $proyecto->createdAt = date('Y-m-d H:i:s');
                $proyecto->guardar();

                // Redireccionar
                header('Location: /proyecto?id=' . $proyecto->url);
            }
        }

        $render->render('dashboard/nuevo', [
            'alertas' => $alertas,
            'titulo' => 'Nuevo Proyecto'
        ]);
    }

    public static function proyecto(Router $render)
    {
        session_start();

        isAuth();

        // Revisar token
        $token = $_GET['id'] ?? null;
        if (!$token)
            header('Location: /dashboard');
        // Revisar la autenticación del usuario
        $proyecto = Proyecto::where('url', $token);
        if ($proyecto->usuarioId !== $_SESSION['id'])
            header('Location: /dashboard');




        $render->render('dashboard/proyecto', [
            'titulo' => $proyecto->nombre
        ]);
    }

    public static function perfil(Router $render)
    {
        session_start();
        isAuth();
        $alertas = [];

        $usuario = Usuario::find($_SESSION['id']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario->sincronizar($_POST);

            $alertas = $usuario->validar_perfil();

            if (empty($alertas)) {
                // Revisar si el email ya existe
                $existeUsuario = Usuario::where('email', $usuario->email);

                if ($existeUsuario && $existeUsuario->id !== $usuario->id) {
                    Usuario::setAlerta('error', 'El email ya está registrado');
                    $alertas = Usuario::getAlertas();
                } else {
                    // Guardar cambios
                    $usuario->updatedAt = date('Y-m-d H:i:s');
                    $usuario->guardar();

                    Usuario::setAlerta('exito', 'Cambios guardados correctamente');
                    $alertas = Usuario::getAlertas();

                    // Asignar los nuevos valores a la sesión
                    $_SESSION['nombre'] = $usuario->nombre;
                }
            }
        }

        $render->render('dashboard/perfil', [
            'titulo' => 'Perfil',
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }

    public static function cambiar(Router $render)
    {
        session_start();
        isAuth();
        $alertas = [];

        $usuario = Usuario::find($_SESSION['id']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $usuario->sincronizar($_POST);

            $alertas = $usuario->nuevoPassword();

            if (empty($alertas)) {
                // Revisar si el password actual es correcto
                $resultado = $usuario->comprobarPassword();

                if($resultado) {
                    $usuario->password = $usuario->passwordNuevo;
                    // Eliminar propiedades que no existen en la tabla
                    unset($usuario->passwordActual);
                    unset($usuario->passwordNuevo);
                    unset($usuario->password2);
                    // Hashear el password
                    $usuario->hashPassword();
                    // Guardar cambios
                    $usuario->updatedAt = date('Y-m-d H:i:s');
                    $resultado = $usuario->guardar();

                    if($resultado) {
                        Usuario::setAlerta('exito', 'Contraseña actualizada correctamente');
                        $alertas = Usuario::getAlertas();
                    } else {
                        Usuario::setAlerta('error', 'Hubo un error al actualizar la contraseña');
                        $alertas = Usuario::getAlertas();
                    }

                } else {
                    Usuario::setAlerta('error', 'El password actual es incorrecto');
                    $alertas = Usuario::getAlertas();
                }
                
                // // Hashear el password
                // $usuario->hashPassword();
                // // Guardar cambios
                // $usuario->updatedAt = date('Y-m-d H:i:s');
                // $usuario->guardar();

                // Usuario::setAlerta('exito', 'Contraseña actualizada correctamente');
                // $alertas = Usuario::getAlertas();
            }
        }

        $render->render('dashboard/cambiar-contrasena', [
            'titulo' => 'Cambiar Contraseña',
            'alertas' => $alertas
        ]);
    }
}