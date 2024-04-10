<?php include_once __DIR__ . '/header.php'; ?>

<div class="contenedor-sm">
    <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

    <a href="/perfil" class="enlace">Volver Perfil</a>

    <form class="formulario" method="POST" action="/cambiar-contrasena">
        <div class="campo">
            <label for="passwordActual">Contraseña Actual</label>
            <input type="password" name="passwordActual" placeholder="Tu Contraseña Actual">
        </div>
        <div class="campo">
            <label for="passwordNuevo">Contraseña Nueva</label>
            <input type="password" name="passwordNuevo" placeholder="Tu Contraseña Nueva">
        </div>
        <div class="campo">
            <label for="password2">Repetir Contraseña Nueva</label>
            <input type="password" name="password2" placeholder="Repite tu Contraseña Nueva">
        </div>

        <input type="submit" value="Guardar Cambios">
    </form>

</div>

<?php include_once __DIR__ . '/footer.php'; ?>