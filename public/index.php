<?php 

require_once __DIR__ . '/../includes/app.php';

use Controllers\DashboardController;
use Controllers\LoginController;
use Controllers\TareaController;
use MVC\Router;
$router = new Router();

// Login
$router->get('/', [LoginController::class, 'login']);
$router->post('/', [LoginController::class, 'login']);
$router->get('/logout', [LoginController::class, 'logout']);

// Crear Cuenta
$router->get('/crear-cuenta', [LoginController::class, 'crear']);
$router->post('/crear-cuenta', [LoginController::class, 'crear']);

// Olvide mi contraseña
$router->get('/olvide-contrasena', [LoginController::class, 'olvide']);
$router->post('/olvide-contrasena', [LoginController::class, 'olvide']);

// Nuevo Password
$router->get('/nueva-contrasena', [LoginController::class, 'nueva']);
$router->post('/nueva-contrasena', [LoginController::class, 'nueva']);

// Confirmar Cuenta
$router->get('/mensaje', [LoginController::class, 'mensaje']);
$router->get('/confirmar', [LoginController::class, 'confirmar']);

// Zona de Proyectos
$router->get('/dashboard', [DashboardController::class, 'index']);
$router->get('/nuevo-proyecto', [DashboardController::class, 'nuevo']);
$router->post('/nuevo-proyecto', [DashboardController::class, 'nuevo']);
$router->get('/proyecto', [DashboardController::class, 'proyecto']);
$router->get('/perfil', [DashboardController::class, 'perfil']);
$router->post('/perfil', [DashboardController::class, 'perfil']);
$router->get('/cambiar-contrasena', [DashboardController::class, 'cambiar']);
$router->post('/cambiar-contrasena', [DashboardController::class, 'cambiar']);

// API para tareas
$router->get('/api/tareas', [TareaController::class, 'index']);
$router->post('/api/tarea', [TareaController::class, 'crear']);
$router->post('/api/tarea/actualizar', [TareaController::class, 'actualizar']);
$router->post('/api/tarea/eliminar', [TareaController::class, 'eliminar']);

// Comprueba y valida las rutas, que existan y les asigna las funciones del Controlador
$router->comprobarRutas();