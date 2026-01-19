
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

$res = $mysqli->query("SELECT razon, COUNT(*) AS conteo FROM solicitudes GROUP BY razon ORDER BY conteo DESC");
if (!$res) out(["ok" => false, "error" => $mysqli->error], 500);
$rows = $res->fetch_all(MYSQLI_ASSOC);

out(["ok" => true, "data" => $rows]);
