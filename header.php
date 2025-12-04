<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Colección de álbumes</title>
<link rel="stylesheet" href="assets/css/styles.css">
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
                <a class="btn-link" href="<?= esc('?' . http_build_query(array_merge($_GET, ['tema' => ($tema === 'oscuro' ? 'claro' : 'oscuro')]))); ?>">
                    Cambiar a <?= $tema === 'oscuro' ? 'tema claro' : 'tema oscuro'; ?>
                </a>
            </nav>
        </div>
    </header>
