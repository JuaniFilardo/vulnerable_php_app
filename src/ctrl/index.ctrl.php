<?php

require_once '../../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Obtener el input del usuario por POST:
    $user = $_POST['username'];
    $pass = $_POST['password'];

    // Consulta SQL vulnerable a inyección:
    $consulta = "SELECT id, nombre FROM usuarios WHERE nombre='" . $user . "' AND password='" . $pass . "'";

    // Ejecutar la consulta SQL vulnerable
    $conexion = PoolConnectionDb::get_instance()->get_connection_db();
    $resultados = $conexion->query($consulta);

    if (count($resultados) > 0) {
        require_once $SERVER_PATH . "tmpl/welcome.tmpl.php";
    } else {
        header('Location: ' . $WEB_PATH . 'src/ctrl/index.ctrl.php');
    }

}

else {
    require_once $SERVER_PATH . "tmpl/index.tmpl.php";
}
