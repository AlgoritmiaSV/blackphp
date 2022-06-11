<?php
/*
#	Index
#	By: Edwin Fajardo
#	Date-time: 2017-09-12 00:00
*/

$server_name = explode(".", $_SERVER["SERVER_NAME"]);

############ Redirección temporal
if($server_name[0] == "fccastillo" && $server_name[1] != "mimakit")
{
	header("Location: https://fccastillo.mimakit.com/");
	exit();
}
############ Fin de redirección temporal

############ Config (Entity, Main, BlackPHP)
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
#Include all php files from libs folder
foreach(glob("libs/*") as $file)
{
	include $file;
}
#Include all php files from utils folder
foreach(glob("utils/*") as $file)
{
	include $file;
}
#Load the controller automatically if class not exists
function site_autoload($class)
{
	require 'controllers/' . $class .".php";
}
spl_autoload_register('site_autoload');	

#Start the session
Session::init();

#Create an object of type bootstrap
$Bootstrap = new Bootstrap();
$Bootstrap->init();
?>
