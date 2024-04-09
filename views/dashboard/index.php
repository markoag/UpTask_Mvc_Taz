<?php include_once __DIR__ . '/header.php'; ?>

<?php if (count($proyectos) === 0) { ?>
    <p class="no-proyectos">No tienes creado nigún Proyecto <a href="/nuevo-proyecto">Crea uno ahora!</a></p>
<?php } else { ?>
    <ul class="listado-proyectos">
        <?php foreach ($proyectos as $proyecto) { ?>
            <li class="proyecto">
                <a href="/proyecto?id=<?php echo $proyecto->url; ?>">
                    <?php echo $proyecto->nombre; ?>
                </a>
            </li>
        <?php } ?>
    </ul>
<?php } ?>

<?php include_once __DIR__ . '/footer.php'; ?>