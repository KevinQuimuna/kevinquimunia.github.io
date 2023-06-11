<?php
// Verificar si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Obtener los datos del formulario y limpiarlos
  $nombre = limpiar($_POST['Nombre']);
  $edad = limpiar($_POST['Edad']);
  $email = limpiar($_POST['Email']);
  $comentario = limpiar($_POST['Comentario']);

  // Validar los datos
  if (campoVacio($nombre) || !preg_match('/^[A-Za-z\s]+$/', $nombre) ||
      campoVacio($edad) || !is_numeric($edad) ||
      campoVacio($email) || !filter_var($email, FILTER_VALIDATE_EMAIL) ||
      campoVacio($comentario)) {
    echo "Por favor, completa todos los campos correctamente.";
    exit();
  }

  // Configurar los detalles del correo electrónico
  $destinatario = "kevin.wwe.1224@gmail.com";
  $asunto = "Nuevo mensaje desde el formulario de contacto";
  $contenido = "Nombre: $nombre\nCorreo: $email\nMensaje: $comentario";

  // Conexión a la base de datos
  $conexion = new mysqli('localhost', 'root', '', 'pagina personal');

  // Verificar si la conexión fue exitosa
  if ($conexion->connect_errno) {
    echo "Error al conectar a la base de datos: " . $conexion->connect_error;
    exit();
  }

  // Preparar la consulta SQL para insertar los datos del formulario en la tabla
  $consulta = $conexion->prepare("INSERT INTO `pagina personal` (nombre, edad, email, comentario) VALUES (?, ?, ?, ?)");

  // Verificar si la consulta se preparó correctamente
  if (!$consulta) {
    echo "Error al preparar la consulta: " . $conexion->error;
    exit();
  }

  // Asociar los valores a los parámetros de la consulta
  $consulta->bind_param("ssss", $nombre, $edad, $email, $comentario);

  // Ejecutar la consulta SQL
  if ($consulta->execute()) {
    // Mostrar mensaje de agradecimiento
    echo "Gracias por visitar mi página web. Tu comentario se ha enviado correctamente.";
  } else {
    echo "Error al enviar el formulario: " . $consulta->error;
  }

  // Cerrar la consulta y la conexión a la base de datos
  $consulta->close();
  $conexion->close();
} else {
  // Si el formulario no se ha enviado correctamente, redirige al formulario nuevamente
  header('Location: formulario.html');
  exit();
}

// Función para limpiar los datos ingresados por el usuario
function limpiar($dato) {
  $dato = trim($dato);
  $dato = htmlspecialchars($dato);
  return $dato;
}

// Función para verificar si un campo está vacío
function campoVacio($valor) {
  return empty($valor);
}
?>
