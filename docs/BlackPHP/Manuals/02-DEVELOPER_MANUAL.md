Manual del desarrollador de BlackPHP
====================================

Contenido
---------
* [Generalidades](#Generalidades)
* [Base de datos](#Base-de-datos)
* [Scripts en el directorio raíz](#Scripts-en-el-directorio-raíz)
* [Carpetas](#Carpetas)
* [Modelos](#Modelos)
* [Vistas](#Vistas)
* [Controladores](#Controladores)
* [Idioma](#Idioma)
* [Temas](#Temas)
* [Librerías externas PHP](#Librerías-externas-PHP)
* [Librerías externas Javascript](#Librerías-externas-Javascript)

Generalidades
-----------------
**BlackPHP** es un Framework escalable desarrollado por miembros de la sociedad **Red Teleinformática** en Centroamérica; la intención preliminar fue acelerar la producción de los sistemas que comparten características similares, tales como la creación de múltiples entidades y múltiples usuarios por entidad.

**BlackPHP** está desarrollado en PHP y sigue la lógina de una estructura MVC estándar.

Base de datos
-------------
La base de datos de **BlackPHP** comprende básicamente dos partes: Un conjunto de tablas de uso del sistema (todas las que inician con app_), y las tablas para el almacenamiento de datos de los negocios durante el uso.

Scripts en el directorio raíz
-----------------------------
*Script utilitarios para el desarrollador*

**advances.php**: Al ejecutar este archivo, obtiene estadísticas de desarrollo del sistema, esto incluye: nombre de los ficheros, fecha de última actualización, líneas escritas y peso del archivo. Asimismo presenta estadísticas generales de todo el sistema clasificadas por tipo de archivo.

**error_log.php**: Con este fichero podrá obtener de manera sencilla una impresión del fichero error_log ubicado en el directorio raíz, cuando ocurra algun error durante la ejecución del sistema. (Sólo para implementaciones en línea con cPanel).

Carpetas
--------
*/controllers/*

*/db/*

*/documentation/*

*/entities/*

*/libs/*

Modelos
-------

Vistas
------

Controladores
-------------

Idioma
------

Temas
-----

Librerías externas PHP
--------------------------------------
*PHPExcel*

*ParseUserAgent*

*zklibrary*

Librerías externas Javascript
---------------------------------------------
*JQuery*

*Select2*

*JQuery UI*

*jAlert*

*jquery.floatThead*

*jquery.jqpagination*

*printThis*

*image-uploader*

*Chart.js*

Edwin Fajardo
[www.edwinfajardo.com](https://www.edwinfajardo.com)
