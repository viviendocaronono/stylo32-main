<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reserva</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header style="background-color: #333; color: #fff; padding: 10px 0; text-align: center;">
    <nav>
      <ul style="list-style: none; padding: 0; margin: 0; display: flex; justify-content: center;">
        <li style="margin: 0 15px;">
          <a href="#home" style="color: #fff; text-decoration: none; font-size: 18px; transition: color 0.3s;">Inicio</a>
        </li>
        <li style="margin: 0 15px;">
          <a href="#about" style="color: #fff; text-decoration: none; font-size: 18px; transition: color 0.3s;">Acerca de</a>
        </li>
        <li style="margin: 0 15px;">
          <a href="#services" style="color: #fff; text-decoration: none; font-size: 18px; transition: color 0.3s;">Servicios</a>
        </li>
        <li style="margin: 0 15px;">
          <a href="#contact" style="color: #fff; text-decoration: none; font-size: 18px; transition: color 0.3s;">Contacto</a>
        </li>
      </ul>
    </nav>
  </header>
<?php
include('php/db.php');

$fecha = isset($_GET['fecha']) ? $_GET['fecha'] : '';

if ($fecha) {
    $query = "SELECT id FROM fechas WHERE fecha = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $fecha);
    $stmt->execute();
    $stmt->bind_result($fecha_id);
    $stmt->fetch();
    $stmt->close();

    if ($fecha_id) {
        echo "<h1>Reserva para la fecha: $fecha</h1>";

        echo "<form id='form-horario' method='POST' action='php/guardar_reserva.php'>";
        echo "<input type='hidden' name='fecha_id' value='$fecha_id'>";
        echo "<input type='hidden' name='horario_id' id='horario_id'>";
        echo "<label for='hora'>Selecciona un horario:</label>";
        echo "<select name='hora' id='hora'>";
        for ($i = 7; $i <= 17; $i++) {
            $hora = sprintf('%02d:00', $i);
            echo "<option value='$hora'>$hora</option>";
        }
        echo "</select>";
        echo "<div id='disponibilidad'></div>";
        echo "<button type='submit' id='btn-enviar'>Reservar</button>";
        echo "</form>";

        echo "
        <script>
        function verificarDisponibilidad(hora, fecha_id) {
            fetch('php/verificar_horario.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'hora=' + encodeURIComponent(hora) + '&fecha_id=' + fecha_id
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('disponibilidad').innerText = data.mensaje;
                if (data.disponible) {
                    document.getElementById('horario_id').value = data.horario_id;
                } else {
                    document.getElementById('horario_id').value = '';
                }
            });
        }

        const fecha_id = '$fecha_id';

        document.getElementById('hora').addEventListener('change', function() {
            const hora = this.value;
            verificarDisponibilidad(hora, fecha_id);
        });

        window.addEventListener('DOMContentLoaded', function () {
            const horaInicial = document.getElementById('hora').value;
            verificarDisponibilidad(horaInicial, fecha_id);
        });

        document.getElementById('form-horario').addEventListener('submit', function(e) {
            const horarioId = document.getElementById('horario_id').value;
            if (!horarioId) {
                console.log('El horario no está disponible, pero el formulario se enviará.');
            }
        });
        </script>";
    } else {
        echo "<h1>No se encontró la fecha en la base de datos.</h1>";
    }
} else {
    echo "<h1>No se ha seleccionado ninguna fecha.</h1>";
}
?>
</body>
</html>
