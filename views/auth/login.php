<div class="contenedor login">
    <?php include_once __DIR__ . '/../templates/nombre-sitio.php'; ?>

    <div class="contenedor-sm">
        <p class="descripcion-pagina">Iniciar Sesión</p>
        <?php include_once __DIR__ . '/../templates/alertas.php'; ?>
        
        <form class="formulario" method="POST" action="/">
            <div class="campo">
                <label for="email">E-mail</label>
                <input type="email" id="email" placeholder="Ingresa tu e-mail" name="email">
            </div>
            <div class="campo">
                <label for="password">Contraseña</label>
                <input type="password" id="password" placeholder="Ingresa tu password" name="password">
            </div>
            <input type="submit" class="boton" value="Iniciar Sesión">
        </form>
        <div class="acciones">
            <a href="/crear-cuenta">Crear Cuenta</a>
            <a href="/olvide-contrasena">¿Olvidaste tu contraseña?</a>
        </div>
    </div> <!--.contenedor-sm-->
</div>