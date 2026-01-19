
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

$prioridad = trim($_GET['prioridad'] ?? 'Todas');
$troquel   = trim($_GET['troquel'] ?? '');

$sql = "
SELECT s.id, s.fecha, s.numero_troquel, s.paso, s.descripcion, s.detalle, s.cantidad,
       s.razon, s.prioridad, s.matricero, s.parte, s.pieza, s.imagen,
       seg.id AS seguimiento_id, seg.tipo_acero, seg.dimensiones, seg.estatus, seg.ultima_actualizacion
FROM solicitudes s
LEFT JOIN (
    SELECT t1.*
    FROM seguimiento t1
    JOIN (
       SELECT solicitud_id, MAX(ultima_actualizacion) AS max_u
       FROM seguimiento
       GROUP BY solicitud_id
    ) t2 ON t1.solicitud_id = t2.solicitud_id AND t1.ultima_actualizacion = t2.max_u
) seg ON seg.solicitud_id = s.id
WHERE 1=1
";

$params = [];
$types  = "";

if ($prioridad !== 'Todas') {
    $sql .= " AND s.prioridad = ? ";
    $types .= "s";
    $params[] = $prioridad;
}
if ($troquel !== '') {
    $sql .= " AND s.numero_troquel LIKE ? ";
    $types .= "s";
    $params[] = "%$troquel%";
}

$sql .= " ORDER BY s.fecha DESC";

$stmt = $mysqli->prepare($sql);
if (!$stmt) out(["ok" => false, "error" => "Prepare: ".$mysqli->error], 500);
if ($types !== "") $stmt->bind_param($types, ...$params);
$stmt->execute();
$res = $stmt->get_result();
$data = $res->fetch_all(MYSQLI_ASSOC);

out(["ok" => true, "data" => $data]);
