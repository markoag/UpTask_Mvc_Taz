<?php

namespace Controllers;

use Model\Proyecto;
use MVC\Router;

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

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $proyecto = new Proyecto($_POST);
            
            // Validar
            $alertas = $proyecto->validarProyecto();

            if(empty($alertas)) {
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
        if(!$token) header('Location: /dashboard');
        // Revisar la autenticación del usuario
        $proyecto = Proyecto::where('url', $token);
        if($proyecto->usuarioId !== $_SESSION['id']) header('Location: /dashboard');
        


        
        $render->render('dashboard/proyecto', [
            'titulo' => $proyecto->nombre
        ]);
    }

    public static function perfil(Router $render)
    {
        session_start();

        isAuth();
        
        $render->render('dashboard/perfil', [
            'titulo' => 'Perfil'
        ]);
    }
}