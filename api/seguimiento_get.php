
<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/config.php';

function out($arr, $code = 200) {
    http_response_code($code);
    echo json_encode($arr, JSON_UNESCAPED_UNICODE);
    exit;
}

$solicitud_id = (int)($_GET['solicitud_id'] ?? 0);
if (!$solicitud_id) out(["ok" => false, "error" => "Falta solicitud_id"], 400);

$stmt = $mysqli->prepare("SELECT * FROM seguimiento WHERE solicitud_id=? ORDER BY ultima_actualizacion DESC LIMIT 1");
if (!$stmt) out(["ok" => false, "error" => "Prepare: ".$mysqli->error], 500);
$stmt->bind_param("i", $solicitud_id);
$stmt->execute();
$res = $stmt->get_result();
$data = $res->fetch_assoc();

out(["ok" => true, "data" => $data]);

