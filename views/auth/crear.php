<div class="contenedor crear">
    <?php 
    include_once __DIR__ . '/../templates/nombre-sitio.php';
    ?>

    <div class="contenedor-sm">
        <p class="descripcion-pagina">Crea tu cuenta en UpTask</p>
        <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

        <form class="formulario" method="POST" action="/crear-cuenta">
            <div class="campo">
                <label for="nombre">Nombre</label>
                <input type="text" name="nombre" id="nombre" placeholder="Tu Nombre"
                    value="<?php echo s($usuario->nombre); ?>">
            </div>
            <div class="campo">
                <label for="email">E-mail</label>
                <input type="email" name="email" id="email" placeholder="Tu E-mail"
                    value="<?php echo s($usuario->email); ?>">
            </div>
            <div class="campo">
                <label for="password">Contraseña</label>
                <input type="password" name="password" id="password" placeholder="Tu Contraseña">
            </div>
            <div class="campo">
                <label for="password2">Repetir Contraseña</label>
                <input type="password" name="password2" id="password2" placeholder="Repite tu Contraseña">
            </div>
            <input type="submit" value="Crear Cuenta" class="boton">
        </form>
        <div class="acciones">
            <a href="/">Iniciar Sesión</a>
            <a href="/olvide-contrasena">¿Olvidaste tu contraseña?</a>
        </div>
    </div> <!--.contenedor-sm-->
</div>