<?php
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');
include_once("conexion.php");

$db = new Conexion();

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $db->pdo->prepare("SELECT Id, titulo, artista, categoria, descripcion, url, anio FROM albuns WHERE Id = ?");
    $stmt->execute([$id]);
    $album = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode($album ? [$album] : []);
    exit;
}

$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 6; 
$offset = ($page - 1) * $limit;

$q = trim($_GET['q'] ?? '');
$categoria = $_GET['categoria'] ?? '';

$where = "WHERE 1=1";
$params = [];

if ($q !== '') {
    $where .= " AND titulo LIKE ?";
    $params[] = "%$q%";
}
if ($categoria !== '' && strtolower($categoria) !== 'todas') {
    $where .= " AND categoria = ?";
    $params[] = $categoria;
}

$countStmt = $db->pdo->prepare("SELECT COUNT(*) FROM albuns $where");
$countStmt->execute($params);
$total = (int)$countStmt->fetchColumn();

$sql = "SELECT Id, titulo, artista, categoria, descripcion, url, anio 
        FROM albuns $where 
        ORDER BY Id DESC 
        LIMIT $limit OFFSET $offset";

$stmt = $db->pdo->prepare($sql);
$stmt->execute($params);
$albums = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'data' => $albums,
    'pagination' => [
        'current' => $page,
        'total'   => $total,
        'pages'   => ceil($total / $limit),
        'limit'   => $limit
    ]
]);