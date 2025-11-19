<?php

include_once("albums.php");
include_once("funciones.php");



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
    if ($q !== '' && stripos($it['title'], $q) === false)
        return false;
    if ($categoriaFilter !== '' && strtolower($categoriaFilter) !== 'todas') {
        if (strcasecmp($it['categoria'], $categoriaFilter) !== 0)
            return false;
    }
    return true;
});


$totalItems = count($items);
$resultsCount = count($filtered);

include_once("header.php");
include_once("main.php");
include_once("footer.php");
?>