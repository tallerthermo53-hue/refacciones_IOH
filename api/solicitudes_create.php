
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

$numero_troquel = s($_POST['numero_troquel'] ?? null);
$paso           = s($_POST['paso'] ?? null);
$descripcion    = s($_POST['descripcion'] ?? null);
$detalle        = s($_POST['detalle'] ?? '');
$cantidad       = isset($_POST['cantidad']) ? (int)$_POST['cantidad'] : 0;
$razon          = s($_POST['razon'] ?? null);
$razon_otro     = s($_POST['razon_otro'] ?? '');
$prioridad      = s($_POST['prioridad'] ?? null);
$matricero      = s($_POST['matricero'] ?? null);
$parte          = s($_POST['parte'] ?? null);
$pieza          = s($_POST['pieza'] ?? null);

if ($razon === 'Otro' && $razon_otro !== '') $razon = $razon_otro;

if (!$numero_troquel || !$paso || !$descripcion || !$cantidad || !$razon || !$prioridad || !$matricero || !$parte || !$pieza) {
    out(["ok" => false, "error" => "Campos requeridos incompletos."], 400);
}

$imagen_ruta = null;
if (!empty($_FILES['imagen']['name'])) {
    $dir = __DIR__ . '/../uploads/';
    if (!is_dir($dir)) {
        if (!@mkdir($dir, 0777, true) && !is_dir($dir)) {
            out(["ok" => false, "error" => "No se pudo crear la carpeta uploads."], 500);
        }
    }
    $ext = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
    if (!preg_match('/^[a-z0-9]+$/', $ext)) $ext = 'jpg';
    $random = function_exists('random_bytes') ? bin2hex(random_bytes(3)) : uniqid();
    $nombre = 'img_' . date('Ymd_His') . '_' . $random . '.' . $ext;
    $destino = $dir . $nombre;
    if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $destino)) {
        out(["ok" => false, "error" => "No se pudo guardar la imagen."], 500);
    }
    $imagen_ruta = 'uploads/' . $nombre;
}

$stmt = $mysqli->prepare("
    INSERT INTO solicitudes 
    (numero_troquel, paso, descripcion, detalle, cantidad, razon, prioridad, matricero, parte, pieza, imagen)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");
if (!$stmt) out(["ok" => false, "error" => "Prepare: ".$mysqli->error], 500);

$stmt->bind_param("ssssissssss",
  $numero_troquel, $paso, $descripcion, $detalle, $cantidad, $razon, $prioridad, $matricero, $parte, $pieza, $imagen_ruta
);
if (!$stmt->execute()) out(["ok" => false, "error" => "Execute: ".$stmt->error], 500);

out(["ok" => true, "id" => $stmt->insert_id]);
