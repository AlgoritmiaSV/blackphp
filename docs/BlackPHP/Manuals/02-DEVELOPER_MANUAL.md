# Manual del desarrollador de BlackPHP

---

## Contenido

* [Generalidades](#generalidades)
* [BlackPHP updater](#blackphp-updater)
* [Base de datos](#base-de-datos)
* [Scripts en el directorio raíz](#scripts-en-el-directorio-raíz)
* [Carpetas](#carpetas)
* [Modelos](#modelos)
* [Vistas](#vistas)
* [Controladores](#controladores)
* [Idioma](#idioma)
* [Temas](#temas)
* [Librerías externas PHP](#librerías-externas-php)
* [Librerías externas Javascript](#librerías-externas-javascript)

---

## Generalidades

**BlackPHP** es un Framework escalable desarrollado por miembros de la sociedad **Red Teleinformática** en Centroamérica; la intención preliminar fue acelerar la producción de los sistemas que comparten características similares, tales como la creación de múltiples entidades y múltiples usuarios por entidad.

**BlackPHP** está desarrollado en PHP y sigue la lógina de una estructura MVC estándar. La versión actual es compatible con PHP 8.2

---

## BlackPHP updater

Adicionalmente, se ha creado un conjunto de script para Linux, a fin de actualizar cada una de las partes fundamentales del Framework. Las acciones que realiza son las siguientes:

* Crea un sólo archivo js minificado que incluye todos los archivos anteriores.
* Compila los estilos scss en varios temas .min.css
* Sincroniza las carpetas básicas de BlackPHP en cada proyecto: node_modules, vendor, libs, utilities, entre otros.
* Explora la base de datos en busca de cambios en la estructura de cada proyecto, y actualiza los modelos.
* Revisa todas las palabras y frases sujetas a traducción con gettext, y actualiza los archivos de lenguaje .po; compila los archivos po existentes en .mo

---

## Base de datos

La base de datos de **BlackPHP** comprende básicamente dos partes: Un conjunto de tablas de uso del sistema (todas aquellas cuyos nombres inician con app_), y las tablas para el almacenamiento de datos de las entidades durante el uso.

### ID de la entidad

Para los sistemas diseñados para varias entidades, se puede añadir en cada tabla entidad, un campo con la definición: `entity_id INT NOT NULL`, preferiblemente con una llave foránea hacia la tabla `entities.entity_id`.

### Timestamps

En cada registro de las tablas se controla la hora, fecha y usuario de creación y la hora, fecha y usuario de última actualización. Para tener control automatizado de la creación y actualización de los registros de una tabla, ésta debe contener los campos siguientes:
`creation_user INT NOT NULL,  
creation_time DATETIME NOT NULL,  
edition_user INT NOT NULL,  
edition_time DATETIME NOT NULL`

### Borrado lógico

Las tablas pueden soportar borrado lógico, si la tabla posee un campo con la definición siguiente:
`status TINYINT NOT NULL DEFAULT 1`
En este caso, se almacenará 0 en este campo cuando el elemento haya sido eliminado, y un valor diferente de cero para otros tipos de estados.
Si el registro genera conflicto por alguna llave única que exista en la tabla, entonces puede crearse un campo de estado con la siguiente definición:
`status TINYINT NULL DEFAULT 1`
En este caso, se almacenará NULL en el campo de estado cuando un registro haya sido aliminado, y un valor diferente de NULL y diferente de cero para otros casos.

---

## Scripts en el directorio raíz

Script utilitarios para el desarrollador.

**advances.php**: Al ejecutar este archivo, obtiene estadísticas de desarrollo del sistema, esto incluye: nombre de los ficheros, fecha de última actualización, líneas escritas y peso del archivo. Asimismo presenta estadísticas generales de todo el sistema clasificadas por tipo de archivo.
Este archivo puede lamarse de tres formas:

* /advances.php, la forma estándar, muestra el resultado en formato HTML.
* /advances.php?mode=text, modo texto
Se aconseja no incluir este archivo en las versiones de prodicción del sistema.

**error_log.php**: Con este fichero podrá obtener de manera sencilla una impresión del fichero error_log ubicado en el directorio raíz, cuando ocurra algun error durante la ejecución del sistema. (Sólo para implementaciones en línea con cPanel).

En las versiones más recientes, se está migrando todo al módulo devUtils, donde además se setán colocando una serie de herramientas importantes para el desarrollador, por ejemplo, se puede visitar: /devUtils/error_log, /devUtils/advances, devUtils/session_vars, devUtils/phpinfo

---

## Carpetas

### /libs/

En esta sessión se encuentra el núcleo del sistema, desde donde se controla la forma en que ha de interpretarse los controladores, las vistas y los modelos. Estos scripts se cargan siempre en cada petición al sistema.

### /controllers/

### /db/

### /documentation/

### /entities/

En este directorio se guadarán los datos de cada entidad (logo, fotos de los usuarios y otros archivos). Cada carpeta será llamada con el nombre del subdominio asignado a la entidad. En los casos de instalaciones para una sola entidad con acceso a través de direcciones IP, se utilizará la carpeta default.

---

## Modelos

Los modelos son archivos PHP conteniendo clases que representas tablas o vistas de la base de datos. Estos modelos se generan de manera automática con BlackPHPUpdater.

---

## Vistas

---

## Controladores

---

## Idioma

---

## Temas

---

## Librerías externas PHP

### PHPExcel

### ParseUserAgent

### zklibrary

---

## Librerías externas Javascript

### JQuery

### Select2

### JQuery UI

### jAlert

### jquery.floatThead

### jquery.jqpagination

### printThis

### image-uploader

### Chart.js

---

Edwin Fajardo
[www.edwinfajardo.com](https://www.edwinfajardo.com)
