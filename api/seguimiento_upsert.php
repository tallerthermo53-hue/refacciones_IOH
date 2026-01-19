
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
function s($v){ return trim((string)($v ?? '')); }

$seguimiento_id = isset($_POST['seguimiento_id']) && $_POST['seguimiento_id'] !== '' ? (int)$_POST['seguimiento_id'] : null;
$solicitud_id   = (int)($_POST['solicitud_id'] ?? 0);
$tipo_acero     = s($_POST['tipo_acero'] ?? '');
$dimensiones    = s($_POST['dimensiones'] ?? '');
$estatus        = s($_POST['estatus'] ?? '');
$comentario     = s($_POST['comentario'] ?? '');

if (!$solicitud_id) out(["ok" => false, "error" => "Falta solicitud_id"], 400);

if ($seguimiento_id) {
    $stmt = $mysqli->prepare("UPDATE seguimiento SET tipo_acero=?, dimensiones=?, estatus=?, comentario=? WHERE id=? AND solicitud_id=?");
    if (!$stmt) out(["ok" => false, "error" => "Prepare: ".$mysqli->error], 500);
    $stmt->bind_param("ssssii", $tipo_acero, $dimensiones, $estatus, $comentario, $seguimiento_id, $solicitud_id);
    if (!$stmt->execute()) out(["ok" => false, "error" => "Execute: ".$stmt->error], 500);
    out(["ok" => true, "action" => "update", "id" => $seguimiento_id]);
} else {
    $stmt = $mysqli->prepare("INSERT INTO seguimiento (solicitud_id, tipo_acero, dimensiones, estatus, comentario) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) out(["ok" => false, "error" => "Prepare: ".$mysqli->error], 500);
    $stmt->bind_param("issss", $solicitud_id, $tipo_acero, $dimensiones, $estatus, $comentario);
    if (!$stmt->execute()) out(["ok" => false, "error" => "Execute: ".$stmt->error], 500);
    out(["ok" => true, "action" => "insert", "id" => $stmt->insert_id]);
}
