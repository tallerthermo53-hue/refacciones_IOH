
<?php
include 'conexion.php';
$numero_troquel = $_POST['numero_troquel'];
$paso = $_POST['paso'];
$descripcion = $_POST['descripcion'];
$detalle = $_POST['detalle'];
$cantidad = $_POST['cantidad'];
$razon = $_POST['razon'];
$prioridad = $_POST['prioridad'];
$parte = $_POST['parte'];
$pieza = $_POST['pieza'];
$imagen = $_POST['imagen']; // Aquí podrías manejar subida de imagen

$sql = "INSERT INTO solicitudes (numero_troquel, paso, descripcion, detalle, cantidad, razon, prioridad, parte, pieza, imagen)
        VALUES ('$numero_troquel','$paso','$descripcion','$detalle','$cantidad','$razon','$prioridad','$parte','$pieza','$imagen')";
if ($conn->query($sql) === TRUE) {
    echo "Solicitud guardada correctamente";
} else {
    echo "Error: " . $conn->error;
}
?>
