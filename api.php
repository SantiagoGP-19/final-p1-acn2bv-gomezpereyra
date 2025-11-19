<?php
header('Content-Type: application/json');

include_once("conexion.php");

$db = new conexion();

$busqueda = isset($_GET['q']) ? $_GET['q'] : '';

$categoria = isset($_GET['categoria']) ? $_GET['categoria'] : '';

$sql = "SELECT * FROM albuns WHERE 1=1";

if ($busqueda != '') {
    $sql .= " AND titulo LIKE '%$busqueda%'";
}
if ($categoria != '' && $categoria != 'todas') {
    $sql .= " AND categoria = '$categoria'";
}

try {
    $resultados = $db->consultar($sql);
    echo json_encode($resultados);
} catch (Exception $e) {
    echo json_encode(['error' => 'Error al consultar la base de datos']);
}
