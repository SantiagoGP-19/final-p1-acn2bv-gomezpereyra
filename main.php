
<main>
    <section aria-label="Buscador y filtros">
        <h2>Buscar y filtrar</h2>
        <form method="get" id="form-busqueda">
            <label for="q">Título</label>
            <input id="q" type="text" name="q" placeholder="Buscar por título..." value="<?= esc($q); ?>">

            <label for="categoriaFiltro">Categoría</label>
            <select id="categoriaFiltro" name="categoria">
                <option value="">Todas</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= esc($cat); ?>" <?= ($categoriaFilter === $cat) ? 'selected' : ''; ?>>
                        <?= esc($cat); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <input type="hidden" name="tema" value="<?= esc($tema); ?>">
            <button type="submit">Buscar</button>
            <a class="btn-link" href="?tema=<?= esc($tema); ?>">Mostrar todos</a>
        </form>
    </section>
<?php
include_once("main2.php");
?>
