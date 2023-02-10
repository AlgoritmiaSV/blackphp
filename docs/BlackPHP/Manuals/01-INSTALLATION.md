Requisitos
==========
- Apache 2.4
- MySQL 15.1
- PHP 8.2
- Módulos de PHP: gettext, intl, gd, zip, mbstring
- Módulos de Apache: mod-rewrite

Instalación para el uso
=======================

Instalación local en Windows para una sola entidad
--------------------------------------------------
- Instalar XAMP (El sistema también ha sido probado con WAMP, pero ocurrieron problemas, porque en WAMP se ejecuta index como el constructor de la clase de PHP, en vez de __construct).
- Sustituir la carpeta htdocs por la carpeta raíz del sistema (htdocs normalmente se encuentra en c:\xamp\htdocs)
- Iniciar los servicios HTTP y MySQL en XAMP
- Crear una base de datos MySQL vacía
- Crear un usuario de MySQL con todos los permisos hacia la base de datos creada
- Importar en la base de datos creada, los archivos:
> - *db/db_structure.sql*
> - *db/initial_data.sql*
- Abrir el archivo config.php ubicado en el directorio raíz del sistema, y modificar los campos correspondientes al acceso a la base de datos
- Visitar, desde un navegador, la dirección: http://127.0.0.1/Instalacion/ para hacer la configuración inicial del sistema

Instalación local en Linux
--------------------------
- Instalar Apache, MySQL, PHP y PHPMyAdmin e iniciar los servicios respectivos
- Pegar la carpeta del sistema en cualquier lugar en el ordenador; normalmente dentro de /var/www/
- Crear una base de datos MySQL vacía
- Crear un usuario de MySQL con todos los permisos hacia la base de datos creada
- Importar en la base de datos creada, el archivo *db/initial_db.sql*
- Abrir el archivo config.php ubicado en el directorio raíz del sistema, y modificar los campos correspondientes al acceso a la base de datos
- Crear un virtualhost en Apache que inicie en el directorio raiz del sistema
- Si el virtualhost creado incluye un nombre de dominio, deberá agregar el dominio al hosts del sistema (En Debian y afines, se encuentra en /etc/hosts)
- Reiniciar el servicio de Apache
- Visitar, desde un navegador, la dirección: http://127.0.0.1/Instalacion/ para hacer la configuración inicial del sistema; la dirección 127.0.0.1 puede ser sustituida por el nombre del virtualhost en caso de que se le haya asignado un nombre

Instalación local desde otra PC
--------------------------------
- Desde PhpMyAdmin, exporte la base de datos de manera personalizada, incluyendo solamente las estructuras de cada tabla, a excepción de las tablas con el prefijo app_* (como app_modules y app_methods) y las tablas con prefijo cie10_*, las cuales deben ser exportadas completamente (Estructura y datos).
- Haga una copia de los archivos en la ruta de instalación (En Windows, podría ser c:/xamp/htdocs).
- Inicie los pasos de acuerdo con el sistema operativo en donde se encuentre instalando la aplicación.

Instalación en línea desde negkit.com
-------------------------------------
- Escriba el dominio deseado en un navegador, por ejemplo: midominio.negkit.com
- El sistema lo guiará hacia la dirección en donde tendrá que acceder con sus credenciales de instalador.

Instalación para desarrollo desde GitHub
========================================

Requisitos:
-----------
- Servidor de Apache y MySQL (XAMPP recomendado)
- Editor de texto (Visual Studio Code recomendado)
- Node.js versión 18.0 o superior
- npm versión 9.3 o superior
- Composer 2.5 o superior

Pasos:
------
- Instale un servidor con PHP y MySQL

- Clone el repositorio desde GitHub
`git clone RedTeleinformatica/BlackPHP .`

- Instale y active las dependencias necesarias de PHP
`apt install php-gd php-mbstring php-zip php-curl php-intl`

- Instale Composer
(Ver instrucciones en getcomposer.org)

- Instale las librerías de Composer
`composer install`

- Instale las librerías desde npm
`npm install`

Edwin Fajardo
[www.edwinfajardo.com](https://www.edwinfajardo.com)
