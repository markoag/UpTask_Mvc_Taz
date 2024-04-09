<?php

namespace Controllers;

use Model\Proyecto;
use Model\Tarea;

class TareaController
{
    public static function index()
    {
        $proyectoId = $_GET['id'];

        if(!$proyectoId) header('Location: /dashboard');

        $proyecto = Proyecto::where('url', $proyectoId);
        session_start();

        if(!$proyecto || $proyecto->usuarioId !== $_SESSION['id']) header('Location: /404');

        $tareas = Tarea::perteneceA('proyectoId', $proyecto->id);

        echo json_encode(['tareas' => $tareas]);
    }
    public static function crear()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            session_start();

            $proyecto = Proyecto::where('url', $_POST['proyectoId']);

            if (!$proyecto || $proyecto->usuarioId !== $_SESSION['id']) {
                $respuesta = [
                    'tipo' => 'error',
                    'mensaje' => 'Hubo un error al agg la tarea'
                ];
                echo json_encode($respuesta);
                return;
            }
            // Instanciar y crear la Tarea
            $tarea = new Tarea($_POST);
            $tarea->proyectoId = $proyecto->id;
            $tarea->createdAt = date('Y-m-d H:i:s');
            $resultado = $tarea->guardar();

            $respuesta = [
                'tipo' => 'exito',
                'mensaje' => 'Tarea creada correctamente',
                'id' => $resultado['id'],
                'proyectoId' => $proyecto->id,
                'createdAt' => $tarea->createdAt
            ];
            echo json_encode($respuesta);
        }
    }
    public static function actualizar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            session_start();

            // Validar que el proyecto exista
            $proyecto = Proyecto::where('url', $_POST['proyectoId']);
            if (!$proyecto || $proyecto->usuarioId !== $_SESSION['id']) {
                $respuesta = [
                    'tipo' => 'error',
                    'mensaje' => 'Hubo un error al actualizar la tarea'
                ];
                echo json_encode($respuesta);
                return;
            }

            // Instanciar y actualizar la Tarea
            $tarea = new Tarea($_POST);
            $tarea->proyectoId = $proyecto->id;
            $tarea->updatedAt = date('Y-m-d H:i:s');
            $resultado = $tarea->guardar();

            if($resultado){
                $respuesta = [
                    'tipo' => 'exito',
                    'id' => $tarea->id,
                    'proyectoId' => $proyecto->id,
                    'mensaje' => 'Tarea actualizada correctamente'
                ];
                echo json_encode(['respuesta' => $respuesta]);
            }
        }
    }
    public static function eliminar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            session_start();

            // Validar que el proyecto exista
            $proyecto = Proyecto::where('url', $_POST['proyectoId']);
            if (!$proyecto || $proyecto->usuarioId !== $_SESSION['id']) {
                $respuesta = [
                    'tipo' => 'error',
                    'mensaje' => 'Hubo un error al eliminar la tarea'
                ];
                echo json_encode($respuesta);
                return;
            }

            // Instanciar y eliminar la Tarea
            $tarea = new Tarea($_POST);
            $resultado = $tarea->eliminar();

            $resultado = [
                'resultado' => $resultado,
                'mensaje' => 'Tarea eliminada correctamente',
                'tipo' => 'exito'
            ];
            
            echo json_encode($resultado);
        }
    }
}