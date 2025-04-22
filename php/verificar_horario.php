<?php
include('db.php');

$hora = $_POST['hora'] ?? '';
$fecha_id = $_POST['fecha_id'] ?? '';

$response = ['disponible' => false, 'mensaje' => 'Datos incompletos'];

if ($hora && $fecha_id) {
    // Verificamos si el horario ya existe
    $stmt = $conn->prepare("SELECT id FROM horarios WHERE hora = ? AND fecha_id = ?");
    $stmt->bind_param("si", $hora, $fecha_id);
    $stmt->execute();
    $stmt->bind_result($horario_id);
    
    if (!$stmt->fetch()) {
        // Si no existe, lo insertamos
        $stmt->close();
        $insert = $conn->prepare("INSERT INTO horarios (hora, fecha_id) VALUES (?, ?)");
        $insert->bind_param("si", $hora, $fecha_id);
        $insert->execute();
        $horario_id = $insert->insert_id; // Obtenemos el ID del horario insertado
        $insert->close();
    } else {
        // Si ya existe, cerramos la consulta
        $stmt->close();
    }

    // Ahora verificamos si el horario está ocupado
    $check = $conn->prepare("SELECT COUNT(*) FROM usuario_horarios WHERE horario_id = ?");
    $check->bind_param("i", $horario_id);
    $check->execute();
    $check->bind_result($ocupado);
    $check->fetch();
    $check->close();

    if ($ocupado > 0) {
        $response['mensaje'] = "Este horario ya está ocupado.";
    } else {
        $response['disponible'] = true;
        $response['mensaje'] = "Horario disponible.";
    }

    $response['horario_id'] = $horario_id; // Siempre devolvemos el horario_id, ya sea nuevo o existente
}

echo json_encode($response);
?>
