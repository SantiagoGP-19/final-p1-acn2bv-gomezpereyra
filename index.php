<?php
session_start();
$albums = [
        ['id' => 1,
        'title' => 'Hybrid Theory',
        'artist' => 'Linkin Park',
        'categoria' => 'Nu-metal',
        'descripcion' => 'Álbum debut con himnos alternativos y riffs potentes.',
        'imagen' => 'https://picsum.photos/seed/hybrid/400/250',
        'anio' => 2000,],

    ['id' => 2,
    'title' => 'Meteora',
    'artist' => 'Linkin Park',
    'categoria' => 'Nu-metal',
    'descripcion' => 'Continuación del sonido característico con producción pulida.',
    'imagen' => 'https://picsum.photos/seed/meteora/400/250',
    'anio' => 2003,],

    ['id' => 3,
    'title' => 'In Rainbows',
    'artist' => 'Radiohead',
    'categoria' => 'Alternativo',
    'descripcion' => 'Una exploración íntima y electrónica del rock alternativo.',
    'imagen' => 'https://picsum.photos/seed/inrainbows/400/250',
    'anio' => 2007,],

    ['id' => 4,
    'title' => 'OK Computer',
    'artist' => 'Radiohead',
    'categoria' => 'Alternativo',
    'descripcion' => 'Clásico moderno que mezcla electrónica y rock de forma única.',
    'imagen' => 'https://picsum.photos/seed/okcomputer/400/250',
    'anio' => 1997,],

    ['id' => 5,
    'title' => 'Back in Black',
    'artist' => 'AC/DC',
    'categoria' => 'Rock clásico',
    'descripcion' => 'Álbum icónico con riffs inolvidables y energía pura.',
    'imagen' => 'https://picsum.photos/seed/backinblack/400/250',
    'anio' => 1980,],

    ['id' => 6,
    'title' => 'Demon Days',
    'artist' => 'Gorillaz',
    'categoria' => 'Electrónico',
    'descripcion' => 'Mezcla de electrónica, hip-hop y melodías oscuras.',
    'imagen' => 'https://picsum.photos/seed/demondays/400/250',
    'anio' => 2005,],

    ['id' => 7,
    'title' => 'Is This It',
    'artist' => 'The Strokes',
    'categoria' => 'Indie',
    'descripcion' => 'Un referente del indie rock con canciones cortas y pegadizas.',
    'imagen' => 'https://picsum.photos/seed/isthisit/400/250',
    'anio' => 2001,],

    ['id' => 8,
    'title' => "Whatever People Say I Am, That's What I'm Not",
    'artist' => 'Arctic Monkeys',
    'categoria' => 'Indie',
    'descripcion' => 'Debut feroz y observador que lanzó a la banda al estrellato.',
        'imagen' => 'https://picsum.photos/seed/arctic/400/250',
        'anio' => 2006,]
];

if (!isset($_SESSION['sugerencias'])) {
    $_SESSION['sugerencias'] = [];
}

$items = array_merge($albums, $_SESSION['sugerencias']);

$errors = [];
$success = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
// Tomamos campos y limpiamos
    $title = trim($_POST['title'] ?? '');
    $artist = trim($_POST['artist'] ?? '');
    $categoria = trim($_POST['categoria'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $imagen = trim($_POST['imagen'] ?? '');

    if ($title === '') {
        $errors['title'] = 'El título es obligatorio.';
    }
    if ($categoria === '') {
        $errors['categoria'] = 'La categoría es obligatoria.';
    }
    if ($descripcion === '') {
        $errors['descripcion'] = 'La descripción es obligatoria.';
    }

if (empty($errors)) {
    $new = [
        'id' => time(),
        'title' => $title,
        'artist' => $artist ?: 'Anónimo',
        'categoria' => $categoria,
        'descripcion' => $descripcion,
        'imagen' => $imagen,
        'anio' => date('Y'),
    ];
    $_SESSION['sugerencias'][] = $new;
    $success = $new;
    $items[] = $new;
    }
}
$q = trim($_GET['q'] ?? '');
$categoriaFilter = trim($_GET['categoria'] ?? '');
$tema = trim($_GET['tema'] ?? 'claro');

$categories = array_values(array_unique(array_map(function ($it) {
    return $it['categoria'];
}, $items)));
sort($categories);

$filtered = array_filter($items, function ($it) use ($q, $categoriaFilter) {
if ($q !== '' && stripos($it['title'], $q) === false) return false;
if ($categoriaFilter !== '' && strtolower($categoriaFilter) !== 'todas') {
if (strcasecmp($it['categoria'], $categoriaFilter) !== 0) return false;
}
return true;
});


$totalItems = count($items);
$resultsCount = count($filtered);


function esc($str) { return htmlspecialchars($str, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
?>

<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Colección de álbumes</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="<?= $tema === 'oscuro' ? 'tema-oscuro' : ''; ?>">

<header>
    <div>
        <h1>Colección de álbumes</h1>
        <p class="lead">Busca por título, filtra por categoría o sugiere un nuevo álbum.</p>
    </div>
    <div>
        <p>Total items: <?= $totalItems; ?> — Resultados: <?= $resultsCount; ?></p>
        <nav>
            <a class="btn-link" href="<?= esc('?' . http_build_query(array_merge($_GET, ['tema' => ($tema === 'oscuro' ? 'claro' : 'oscuro')] ) )); ?>">
                Cambiar a <?= $tema === 'oscuro' ? 'tema claro' : 'tema oscuro'; ?>
            </a>
        </nav>
    </div>
</header>

<main>
    <section aria-label="Buscador y filtros">
        <form method="get">
            <input type="text" name="q" placeholder="Buscar por título..." value="<?= esc($q); ?>">
            <select name="categoria">
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

    <section aria-label="Listado de álbumes" class="grid">
        <?php if ($resultsCount === 0): ?>
            <p class="no-results">No se encontraron resultados para la búsqueda indicada.</p>
        <?php else: ?>
            <?php foreach ($filtered as $it): ?>
                <article class="card">
                    <img src="<?= esc($it['imagen']); ?>" alt="Portada: <?= esc($it['title']); ?>">
                    <div class="card-body">
                        <div class="meta">
                            <span class="badge">
                                <a href="?<?= esc(http_build_query(array_merge($_GET, ['categoria' => $it['categoria'], 'q' => '']))); ?>">
                                    <?= esc($it['categoria']); ?>
                                </a>
                            </span>
                            <time class="anio"><?= esc($it['anio']); ?></time>
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

        <form method="post" action="?tema=<?= esc($tema); ?>">
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
                <label for="categoria">Categoría *</label>
                <input id="categoria" name="categoria" type="text" value="<?= esc($_POST['categoria'] ?? ''); ?>">
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
                <button type="submit">Enviar sugerencia</button>
                <small>(*) campos obligatorios</small>
            </div>
        </form>
    </section>
</main>

<footer>
    <p>© 2025 Santiago Gomez Pereyra. Todos los derechos reservados.</p>
</footer>

</body>
</html>