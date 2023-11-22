<?php
/**
 * Configuración inicial del sistema
 * 
 * Definición de constantes a utilizar con valores por defecto en caso de no
 * existir el archivo config.php.
 * 
 * Copie este archivo en el directorio raíz como config.php, y defina sus valores.
 * 
 * Incorporado el: 2017-09-21 00:00
 * @author Edwin Fajardo <contacto@edwinfajardo.com>
 */

################################ ACCESO A BASE DE DATOS
/**
 * Tipo de base de datos a utilizar.
 * Valores aceptados: mysql
 */
define('DB_TYPE', 'mysql');

/**
 * Nombre del host o la IP del servidor de base de datos
 */
define('DB_HOST', 'localhost');

/**
 * Número de puerto del servicio de base de datos
 */
define('DB_PORT', '3306');

/**
 * Nombre de la base de datos
 */
define('DB_NAME', 'blackphp');

/**
 * Usuario de la base de datos
 */
define('DB_USER', 'bphpuser');

/**
 * Contraseña para conectarse a la base de datos
 */
define('DB_PASS', 'x/p-29B%&ELYr.6A');

/**
 * Prefijo de la base de datos
 * 
 * Prefijo que se agregará a cada tabla y vista de la base de datos
 */
define('DB_PREFIX', '');

################################ OTRAS CONFIGURACIONES
/**
 * Estado del sistema
 * Modo en que se está sirviendo el sistema.
 * Valores aceptados: PRODUCTION, MAINTENANCE.
 * 
 * El estado normal es PRODUCTION; en el modo MAINTENANCE, sólo se despliega un
 * aviso de Sistema en Mantenimiento.
 */
define('SYSTEM_STATUS', 'PRODUCTION');
?>
