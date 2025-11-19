<?php
include_once("conexion.php");
?>

<section aria-label="Listado de álbumes" class="grid" id="contenedor-albums">
        <?php if ($resultsCount === 0): ?>
            <p class="no-results">No se encontraron resultados para la búsqueda indicada.</p>
        <?php else: ?>
            <?php foreach ($filtered as $it): ?>
                <article class="card">
                    <img src="<?= esc($it['imagen']); ?>" alt="Portada del álbum <?= esc($it['title']); ?>">
                    <div class="card-body">
                        <div class="meta">
                            <span class="badge">
                                <a href="?<?= esc(http_build_query(array_merge($_GET, ['categoria' => $it['categoria'], 'q' => '']))); ?>">
                                    <?= esc($it['categoria']); ?>
                                </a>
                            </span>
                            <time class="anio" datetime="<?= esc($it['anio']); ?>"><?= esc($it['anio']); ?></time>
                        </div>
                        <h3><?= esc($it['title']); ?></h3>
                        <p class="desc"><?= esc($it['descripcion']); ?></p>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>

    <section aria-label="Formulario de sugerencia" class="sugerir">
        <h2>Sugerir un álbum</h2>

        <?php if ($success): ?>
            <p class="ok">Gracias — recibimos tu sugerencia: <strong><?= esc($success['title']); ?></strong></p>
        <?php endif; ?>

        <form id="form-sugerir" method="post" action="?tema=<?= esc($tema); ?>">
            <div class="form-grid">
                <div>
                    <label for="title">Título *</label>
                    <input id="title" name="title" type="text" value="<?= esc($_POST['title'] ?? ''); ?>">
                    <?php if (!empty($errors['title'])): ?><span class="error"><?= esc($errors['title']); ?></span><?php endif; ?>
                </div>
                <div>
                    <label for="artist">Artista</label>
                    <input id="artist" name="artist" type="text" value="<?= esc($_POST['artist'] ?? ''); ?>">
                </div>
            </div>

            <div class="form-field">
                <label for="categoriaNueva">Categoría *</label>
                <input id="categoriaNueva" name="categoria" type="text" value="<?= esc($_POST['categoria'] ?? ''); ?>">
                <?php if (!empty($errors['categoria'])): ?><span class="error"><?= esc($errors['categoria']); ?></span><?php endif; ?>
            </div>

            <div class="form-field">
                <label for="descripcion">Descripción *</label>
                <textarea id="descripcion" name="descripcion" rows="3"><?= esc($_POST['descripcion'] ?? ''); ?></textarea>
                <?php if (!empty($errors['descripcion'])): ?><span class="error"><?= esc($errors['descripcion']); ?></span><?php endif; ?>
            </div>

            <div class="form-field">
                <label for="imagen">URL de imagen (opcional)</label>
                <input id="imagen" name="imagen" type="text" value="<?= esc($_POST['imagen'] ?? ''); ?>">
            </div>

            <div class="form-actions">
                <?php
                $objConexion = new conexion();
                $sql = "INSERT INTO `albuns` (`Id`, `titulo`, `artista`, `categoria`, `descripcion`, `url`) VALUES (NULL, 'dsadasasdasd', 'dadasdasdxzxczcxz', 'dasdasddasdsa', 'czxczxcxzczxdsa', 'dsadasddsdadsadsadsa');";
                $objConexion->ejecutar($sql);
                ?>
                <button type="submit">Enviar sugerencia</button>
                <small>(*) campos obligatorios</small>
            </div>
        </form>
    </section>
</main>