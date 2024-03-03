# Instalación de BlackPHP

---

## Requisitos

- Apache 2.4
- MySQL 15.1
- PHP 8.2
- Módulos de PHP: gettext, intl, gd, zip, mbstring
- Módulos de Apache: mod-rewrite
- BlackPHP versión para producción

---

## Instalación para el uso

### Instalación local en Windows

- Instalar XAMP
- Sustituir la carpeta htdocs por la carpeta raíz del sistema (htdocs normalmente se encuentra en c:\xamp\htdocs)
- Iniciar los servicios HTTP y MySQL en XAMP
- Crear una base de datos MySQL vacía
- Crear un usuario de MySQL con todos los permisos hacia la base de datos creada
- Importar en la base de datos creada, los archivos:

> - *db/db_structure.sql*
> - *db/initial_data.sql*

- Copiar el archivo default_config.php en uno llamado config.php y ubicarlo en el directorio raíz.
- Abrir el archivo config.php ubicado en el directorio raíz del sistema, y modificar los campos correspondientes al acceso a la base de datos
- Según el caso, siga los pasos para instalación en Windows para una sola entidad o para varias entidades

#### Instalación local en Windows para una sola entidad

- Visitar, desde un navegador, la dirección: [127.0.0.1/Installation/](http://127.0.0.1/Installation/) para hacer la configuración inicial del sistema

#### Instalación local en Windows para varias entidades

### Instalación local en Linux

- Instalar Apache, MySQL, PHP y PHPMyAdmin e iniciar los servicios respectivos
- Pegar la carpeta del sistema en cualquier lugar en el ordenador; normalmente dentro de /var/www/
- Crear una base de datos MySQL vacía
- Crear un usuario de MySQL con todos los permisos hacia la base de datos creada
- Importar en la base de datos creada, el archivo *db/initial_db.sql*
- Abrir el archivo config.php ubicado en el directorio raíz del sistema, y modificar los campos correspondientes al acceso a la base de datos
- Crear un virtualhost en Apache que inicie en el directorio raiz del sistema
- Si el virtualhost creado incluye un nombre de dominio, deberá agregar el dominio al hosts del sistema (En Debian y afines, se encuentra en /etc/hosts)
- Reiniciar el servicio de Apache
- Visitar, desde un navegador, la dirección: [127.0.0.1/Installation/](http://127.0.0.1/Installation/) para hacer la configuración inicial del sistema; la dirección 127.0.0.1 puede ser sustituida por el nombre del virtualhost en caso de que se le haya asignado un nombre

#### Instalación el Linux para una sola entidad

#### Instalación en Linux para varias entidades

### Instalación en Docker

- En una carpeta vacía, cree un archivo con nombre Dockerfile, y agregue el siguiente contenido:
``

- Compile una imagen
`docker build .`

### Instalación local desde otra PC

- Desde PhpMyAdmin, exporte la base de datos de manera personalizada, incluyendo solamente las estructuras de cada tabla, a excepción de las tablas con el prefijo app_\* (como app_modules y app_methods) y las tablas con prefijo cie10_\*, las cuales deben ser exportadas completamente (Estructura y datos).
- Haga una copia de los archivos en la ruta de instalación (En Windows, podría ser c:/xamp/htdocs).
- Inicie los pasos de acuerdo con el sistema operativo en donde se encuentre instalando la aplicación.

### Instalación en línea desde blackphp.rti.li

- Escriba el dominio deseado en un navegador, por ejemplo: midominio.blackphp.rti.li
- El sistema lo guiará hacia la dirección en donde tendrá que acceder con sus credenciales de instalador.

---

## Instalación para desarrollo desde GitHub

### Requisitos iniciales

- Servidor de Apache y MySQL (XAMPP recomendado)
- Editor de texto (Visual Studio Code recomendado)
- Node.js versión 18.0 o superior
- npm versión 9.3 o superior
- Composer 2.5 o superior

### Pasos

- Instale un servidor con PHP y MySQL

- Clone el repositorio desde GitHub
`git clone https://github.com/AlgoritmiaSV/BlackPHP .`

- Instale y active las dependencias necesarias de PHP
`apt install php-gd php-mbstring php-zip php-curl php-intl`

- Instale Composer
(Ver instrucciones en getcomposer.org)

- Instale las librerías de Composer
`composer update`

- Instale las librerías desde npm
`npm install`

Edwin Fajardo
[www.edwinfajardo.com](https://www.edwinfajardo.com)
