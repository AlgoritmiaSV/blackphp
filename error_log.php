<?php
/**
 * error_log reader
 * 
 * Este fichero lee el archivo error_log que se genera en el servidor, en el directorio
 * raíz del sistema, y lo muestra en pantalla. También es posible descargarlo en formato txt.
 * 
 * Date-Time: 2021-09-18 15:16
 * @author Edwin Fajardo <contacto@edwinfajardo.com>
 * @link https://www.edwinfajardo.com
 */

#--------------------------------------------------------------------------
$time_diff = 0;
date_default_timezone_set('America/El_Salvador');
$html_begin = '<!Doctype html>
<html>
	<head>
		<title>Site error log</title>
		<style>
			article{overflow-y:scroll; height:100px; padding:5px; z-index:1;}
			body {font-family:monospace; overflow:hidden; position:relative; width:100%; height:100%; margin:0px;}
			h1 {margin:10px;}
			header {padding:5px; background-color:#eeeeee; box-shadow:0px 0px 5px 5px #999999; z-index:2;}
			html {position:absolute; width:100%; height:100%;}
			a {color:#0000ff;}
			a:hover {text-decoration:none;}
			pre {overflow-x: auto; white-space: pre-wrap; white-space: -moz-pre-wrap; white-space: -pre-wrap; white-space: -o-pre-wrap; word-wrap: break-word;}
		</style>
		<script type="text/javascript">
			window.onload = resize_content;
			function resize_content()
			{
				body = document.getElementsByTagName("body");
				header = document.getElementsByTagName("header");
				content_height = body[0].offsetHeight - header[0].offsetHeight - 10;
				content = document.getElementsByTagName("article");
				content[0].style.height = "" + content_height + "px";
			}
		</script>
	</head>
	<body>
		<header>
			<h1>Error log for ' . $_SERVER["HTTP_HOST"] . '</h1>
			<a href="error_log.php?mode=text">Download text file</a> |
			<a href="advances.php">Advances</a> |
		</header>
		<article>
			<pre>';
			$text = "";
			$text .= "\r\n            ERROR LOG\r\n";
			$text .= "    Generated " . Date("Y-m-d H:i:s", time() + $time_diff) . "\r\n";
			$text .= "           By: Edwin Fajardo.\r\n";
			$text .= "--------------------------------------------------------------\r\n";
			$text .= "\r\n";
			if(file_exists("error_log"))
			{
				$text .= file_get_contents("./error_log", FILE_USE_INCLUDE_PATH);
			}
			else
			{
				$text .= "No error was found.";
			}
$html_end = '			</pre>
		</article>
	</body>
</html>';
	if(!isset($_GET["mode"]) || $_GET["mode"] != "text")
	{
		echo $html_begin;
		echo $text;
		echo $html_end;
	}
	else
	{
		$filename = "error_log_" . Date("Y-m-d_H-i-s", time() + $time_diff) . ".txt";
		$report_file = fopen($filename, "w");
		fputs($report_file, $text);
		fclose($report_file);
		header('Content-Description: File Transfer');
		header('Content-type: application/force-download');
		header('Content-Disposition: attachment; filename='.basename($filename));
		header('Content-Transfer-Encoding: binary');
		header('Content-Length: '.filesize($filename));
		readfile($filename);
		unlink($filename);
	}
?>
