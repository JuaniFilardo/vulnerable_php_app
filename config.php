<?php

/********

CONFIG VERSION 2.5.3

2.5.0 -> Agrega compatibilidad con PHPUnit.
2.5.1 -> Actualiza conexión a Bd. Agrega una conexión default al Pool de conexiones.
2.5.2 -> Agrega configuración para Permisos.
2.5.3 -> Agrega $PROJECT_REL_PATH y modifica internamenta como calcula el $WEB_PATH

********/

/*** HEADER UTF-8 ***/
header('Content-Type: text/html; charset=utf-8');

    // Todas las rutas de directorio terminan en /
    // Rutas relativas
global $PROJECT_REL_PATH;
global $CLASSES_REL_PATH;
global $TEMPLATES_REL_PATH;
global $CTRL_REL_PATH;
global $AJAX_REL_PATH;

$PROJECT_REL_PATH = 'vulnerable_php_app/';
$CLASSES_REL_PATH = 'src/clases/';
$TEMPLATES_REL_PATH = 'tmpl/';
$CTRL_REL_PATH = 'src/ctrl/';
$AJAX_REL_PATH = $CTRL_REL_PATH . 'ajax/';

    // Rutas absolutas
global $PROTOCOL;
global $SERVER_PATH;
global $WEB_PATH;
global $LOGIN_PATH;
global $PUBLIC_PATH;
global $PERFIL_PATH;
$PROTOCOL = 'http://';
$SERVER_PATH = dirname(__FILE__) . '/';
$HTTP_HOST = (isset($_SERVER['HTTP_HOST'])) ? $_SERVER['HTTP_HOST'] . '/' : '/';
$WEB_PATH = $PROTOCOL . $HTTP_HOST . $PROJECT_REL_PATH;
$LOGIN_PATH = $WEB_PATH;
$PUBLIC_PATH = $SERVER_PATH . "public/";
$PERFIL_PATH = $WEB_PATH . $CTRL_REL_PATH . 'perfil.ctrl.php';

/*** Función de error ***/
function ERROR($message = "",  $absolute_path = null, $status_code = 500){
    global $WEB_PATH;
    if(!isset($absolute_path)){
        $absolute_path = $WEB_PATH . "error.php";
    }
    http_response_code($status_code); // Ver que el código no se envía porque dsp hago una redirección. Hay que arreglar esto.
    header("Location: $absolute_path?message=$message"); // Si no se encuentra, Apache te lanzará uno por defecto (404).
}

/*** No se puede acceder directamente al config.php ***/
if (basename($_SERVER['PHP_SELF']) === 'config.php') {
    ERROR();
}

/*** DEV_MODE flag ***/
define ("DEV_MODE", true);

if(DEV_MODE){
    ini_set("display_errors", 1);
} else {
    ini_set("display_errors", 0);
}
error_reporting(E_ALL);

/*** TIMEZONE ***/
date_default_timezone_set('America/Argentina/Cordoba');

/*** Headers de caché ***/
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

/*** Autoload de clases ***/

global $PACKAGES;
$PACKAGES = array('bd');

/**
 * Función de autocarga de las clases.
 * Soporta que las clases estén distribuidas en carpetas siempre y cuando
 * éstas tengan nombres diferentes. Dos clases con nombres iguales y en carpetas
 * diferentes puede provocar ambigüedad. Para solucionar esto se debe usar
 * namespace.
 *
 */
spl_autoload_register(function ($nombre_clase) {
    global $PACKAGES, $SERVER_PATH, $CLASSES_REL_PATH;
    $ruta_clases = $SERVER_PATH . $CLASSES_REL_PATH;
    $resultado = '';

    // Tengo en cuenta los namespaces
    $partes = explode('\\', $nombre_clase);
    $nombre_clase = $partes[count($partes) - 1];

    // Continúa ignorando el namespace
    $caracteres = str_split($nombre_clase);
    for ($i = 0; $i < count($caracteres); $i++) {
        if (ctype_upper($caracteres[$i]) && $i !== 0) {
            $resultado .= '_';
        }
        $resultado .= strtolower($caracteres[$i]);
    }
    for ($i = 0; $i < count($PACKAGES); $i++) {
        $posible_archivo = "{$ruta_clases}{$PACKAGES[$i]}/{$resultado}.class.php";
        if (file_exists($posible_archivo)) {
            require_once $posible_archivo;
            break;
        }
    }
});

/*** Base de datos ***/
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'vulnerable_php_app';

PoolConnectionDb::get_instance()->add_connection_db(new MySqlConnectionDb($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME));
