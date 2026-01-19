
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

if (empty($_FILES['file']['tmp_name'])) out(["ok" => false, "error" => "Sube un archivo CSV"], 400);

$handle = fopen($_FILES['file']['tmp_name'], 'r');
if (!$handle) out(["ok" => false, "error" => "No se pudo leer el archivo"], 500);

$header = fgetcsv($handle);
$insertados = 0;

$stmt = $mysqli->prepare("
  INSERT INTO solicitudes (fecha, numero_troquel, paso, descripcion, detalle, cantidad, razon, prioridad, matricero, parte, pieza, imagen)
  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");

while (($data = fgetcsv($handle)) !== false) {
    [$fecha,$troquel,$paso,$descripcion,$detalle,$cantidad,$razon,$prioridad,$matricero,$parte,$pieza,$imagen] = array_slice($data, 0, 12);
    $stmt->bind_param("ssssisssssss", $fecha,$troquel,$paso,$descripcion,$detalle,$cantidad,$razon,$prioridad,$matricero,$parte,$pieza,$imagen);
    if ($stmt->execute()) $insertados++;
}
fclose($handle);
out(["ok" => true, "insertados" => $insertados]);

