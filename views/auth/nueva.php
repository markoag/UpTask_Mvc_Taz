<div class="contenedor nueva">
    <?php include_once __DIR__ . '/../templates/nombre-sitio.php'; ?>

    <div class="contenedor-sm">
        <p class="descripcion-pagina">Nueva Contraseña</p>
        <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

        <?php if ($error)
            return ?>

        <?php if (!$resultado): ?>
            <form class="formulario" method="POST">
                <div class="campo">
                    <label for="password">Nueva Contraseña</label>
                    <input type="password" id="password" name="password" placeholder="Tu nueva contraseña">
                </div>
                <div class="campo">
                    <label for="password2">Confirmar Contraseña</label>
                    <input type="password" id="password2" name="password2" placeholder="Confirma tu nueva contraseña">
                </div>

                <input type="submit" class="boton" value="Recuperar Contraseña">

            </form>
            <div class="acciones">
                <a href="/">¿Ya tienes cuenta? Iniciar Sesión</a>
                <a href="/crear-cuenta">¿Aún no tienes cuenta? Crea una</a>
            </div>
        <?php else: ?>
            <div class="acciones">
                <a href="/">Iniciar Sesión</a>
            </div>
        <?php endif; ?>

    </div> <!--.contenedor-sm-->
</div>