

<?php
// Activar errores para depuración
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Forzar salida JSON
header('Content-Type: application/json; charset=utf-8');

// Respuesta simple
echo json_encode(["ok" => true, "ping" => "pong"], JSON_UNESCAPED_UNICODE);
