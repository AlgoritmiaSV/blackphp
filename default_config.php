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
const DB_TYPE = 'mysql';

/**
 * Nombre del host o la IP del servidor de base de datos
 */
const DB_HOST = 'localhost';

/**
 * Número de puerto del servicio de base de datos
 */
const DB_PORT = '3306';

/**
 * Nombre de la base de datos
 */
const DB_NAME = 'blackphp';

/**
 * Usuario de la base de datos
 */
const DB_USER = 'bphpuser';

/**
 * Contraseña para conectarse a la base de datos
 */
const DB_PASS = 'x/p-29B%&ELYr.6A';

/**
 * Prefijo de la base de datos
 * 
 * Prefijo que se agregará a cada tabla y vista de la base de datos
 */
const DB_PREFIX = '';

################################ LICENCIA DE USO DEL SOFTWARE
/**
 * Servidor de administración de licencias
 * 
 * URL del servidor administrador de licencias.
 */
const LICENSE_SERVER = null;

/**
 * Usuario al quien es acreditada la licencia
 */
const LICENSE_USER = null;

/**
 * Contraseña para comprobación de licencia
 */
const LICENSE_PWD = null;

################################ OTRAS CONFIGURACIONES
/**
 * Estado del sistema
 * Modo en que se está sirviendo el sistema.
 * Valores aceptados: PRODUCTION, MAINTENANCE.
 * 
 * El estado normal es PRODUCTION; en el modo MAINTENANCE, sólo se despliega un
 * aviso de Sistema en Mantenimiento.
 */
const SYSTEM_STATUS = 'PRODUCTION';

/**
 * Creación de entidades
 * Permite configurar el sistema para permitir, o no, nuevas entidades.
 * Valores aceptados: OPEN, CLOSE
 */
const CREATE_ENTITY = 'OPEN';

/**
 * Carpeta privada
 * Ubicación de archivos sensibles de las entidades. Se recomienda una ubicación
 * no accesible al público.
 */
const PRIVATE_FOLDER = 'entities/';
