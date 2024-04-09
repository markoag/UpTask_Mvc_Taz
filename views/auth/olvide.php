<div class="contenedor olvide">
    <?php include_once __DIR__ . '/../templates/nombre-sitio.php'; ?>

    <div class="contenedor-sm">
        <p class="descripcion-pagina">Recuperar Contraseña</p>
        <?php include_once __DIR__ . '/../templates/alertas.php'; ?>
        
        <form class="formulario" method="POST" action="/olvide-contrasena">
            <div class="campo">
                <label for="email">E-mail</label>
                <input type="email" id="email" placeholder="Ingresa tu e-mail" name="email">
            </div>
            <input type="submit" class="boton" value="Enviar Instrucciones">
        </form>
        <div class="acciones">
            <a href="/">Iniciar Sesión</a>
            <a href="/crear-cuenta">Crear Cuenta</a>
        </div>
    </div> <!--.contenedor-sm-->
</div>