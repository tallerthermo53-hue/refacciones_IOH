
<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$mysqli = new mysqli("localhost", "root", "", "smcr_db"); // Ajusta si tienes contraseña
if ($mysqli->connect_errno) {
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(500);
    echo json_encode(["ok" => false, "error" => "Error de conexión: " . $mysqli->connect_error], JSON_UNESCAPED_UNICODE);
    exit;
}
$mysqli->set_charset("utf8mb4");

