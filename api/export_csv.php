
<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/config.php';

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=solicitudes.csv');

$output = fopen('php://output', 'w');
fputcsv($output, [
  'Fecha','Nº Troquel','Paso','Descripción','Detalle','Cantidad','Razón','Prioridad',
  'Matricero','Parte','Pieza','Imagen','Estatus','Tipo de acero','Dimensiones','ID seguimiento','Últ. act.'
]);

$sql = "
SELECT 
  s.fecha, s.numero_troquel, s.paso, s.descripcion, s.detalle, s.cantidad,
  s.razon, s.prioridad, s.matricero, s.parte, s.pieza, s.imagen,
  seg.estatus, seg.tipo_acero, seg.dimensiones, seg.id as seguimiento_id, seg.ultima_actualizacion
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
ORDER BY s.fecha DESC
";
$result = $mysqli->query($sql);
while ($row = $result->fetch_assoc()) {
    fputcsv($output, array_values($row));
}
fclose($output);
