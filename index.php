<?php

include_once("conexion.php");
include_once("funciones.php");

$errors = [];
$success = null;

$db = new Conexion();

$errors = [];
$success = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $artist = trim($_POST['artist'] ?? '');
    $categoria = trim($_POST['categoria'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $imagen = trim($_POST['imagen'] ?? '');
    $anio = !empty($_POST['anio']) ? $_POST['anio'] : date('Y');

    if ($title === '') {
        $errors['title'] = 'El título es obligatorio.';
    }
    if ($categoria === '') {
        $errors['categoria'] = 'La categoría es obligatoria.';
    }
    if ($descripcion === '') {
        $errors['descripcion'] = 'La descripción es obligatoria.';
    }
    if (empty($imagen)) {
        $imagen = 'https://picsum.photos/seed/default/400/250';
    }

if (empty($errors)) {
        $anio = date('Y');
        
        $t = addslashes($title);
        $a = addslashes($artist);
        $c = addslashes($categoria);
        $d = addslashes($descripcion);
        $u = addslashes($imagen);
        $y = (int)$anio;

$sqlInsert = "INSERT INTO albuns (titulo, artista, categoria, descripcion, url, anio) VALUES ('$t', '$a', '$c', '$d', '$u', '$y')";
        try {
            $db->ejecutar($sqlInsert);
            
            $success = [
                'title' => $title,
                'categoria' => $categoria
            ];
        } catch (Exception $e) {
            $errors['db'] = 'Error al guardar en la base de datos.';
        }
    }
}

$sqlGet = "SELECT Id as id, titulo as title, artista as artist, categoria, descripcion, url as imagen, anio FROM albuns";
$items = $db->consultar($sqlGet);

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