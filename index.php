<?php
/**
 * Index
 * 
 * Este fichero contiene la carga inicial del sistema. Se incluyen los archivos generales cuyas
 * clases pueden ser accedidas desde cualquier punto del sistema.
 * 
 * Archivo config.php
 * 
 * El archivo config.php contiene los datos de acceso a la base de datos, y puede estar ubicado en:
 * 1) El directorio raíz.
 * 2) En la carpeta de la entidad /entities/<Subdominio>. En caso de existir el archivo config en
 * la carpeta de la entidad, se prioriza, de lo contrario se busca en el directorio raíz, y en última
 * instancia, se carga default_config.php
 * 
 * Date-time: 2017-09-12 00:00
 * @author Edwin Fajardo <contacto@edwinfajardo.com>
 * @version 1.0.0 Primera edición
 */
$server_name = explode(".", $_SERVER["SERVER_NAME"]);
if(file_exists("entities/" . $server_name[0] . "/config.php"))
{
	include "entities/" . $server_name[0] . "/config.php";
}
elseif(file_exists('config.php'))
{
	include 'config.php';
}
else
{
	include 'default_config.php';
}

# Ficheros de la carpeta libs
foreach(glob("libs/*") as $file)
{
	include $file;
}

# Ficheros de la carpeta utils
foreach(glob("utils/*") as $file)
{
	include $file;
}

/**
 * site_autoload
 * 
 * Este método incluye automáticamente el controlador o el modelo con el nombre de la clase
 * en el momento que se crea un objeto de la clase. Los nombres de archivos en los modelos
 * terminan siempre con la palabra _model.php
 * 
 * @param string $class El nombre de la clase que se va a cargar
 * 
 * @return void
 */
function site_autoload($class)
{
	if(file_exists("models/orm/$class.php"))
	{
		require "models/orm/$class.php";
	}
	else
	{
		require 'controllers/' . $class .".php";
	}
}
spl_autoload_register('site_autoload');	

# Iniciar la sesión
Session::init();

# Crear un objeto del tipo bootstrap
$Bootstrap = new Bootstrap();
$Bootstrap->init();
?>
