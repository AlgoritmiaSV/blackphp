<?php
/**
 * Utilidades para el desarrollador
 * 
 * Herramientas de depuración para desarrolladores
 * Incorporado el 2020-6-12 23:55
 * 
 * @author Edwin Fajardo <contacto@edwinfajardo.com>
 */

class devUtils extends Controller
{
	/**
	 * Constructor de la clase
	 * 
	 * Inicializa la clase y llama al constructor de la clase base.
	 */
	public function __construct()
	{
		parent::__construct();
		$this->module = get_class($this);
	}

	/**
	 * Vista principal
	 * @todo Registrar el módulo y los mçétodos en la base de datos para poderlos desplegar, p
	 * añadirlos a un fichero, ya que se debe procurar que este módulo no utilice la base de datos.
	 */
	public function index()
	{
	}

	/**
	 * Información de PHP
	 * 
	 * Llama a la función phpinfo()
	 */
	public function phpinfo()
	{
		phpinfo();
	}

	/**
	 * Variables de sesión
	 * 
	 * Imprime un arreglo con las variables de sesión al momento de ejecución
	 */
	public function session_vars()
	{
		echo '<pre>' . print_r($_SESSION, true) . '</pre>';
	}

	/**
	 * Registro de errores
	 * 
	 * En la implementación en línea, imprime en pantalla el registro de excepciones almacenado
	 * en el archivo error_log en el directorio raíz del sistema.
	 * 
	 * @param string $mode Modo de respuesta (html o txt)
	 * 
	 * @return void Todos los valores se imprimen
	 */
	public function error_log($mode = "html")
	{
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
		if($mode != "text")
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
	}

	/**
	 * Resumen
	 * 
	 * Muestra un resumen de los datos principales de todos los ficheros de texto del sistema,
	 * tales como: ubicación, nombre, fecha de última modificación y tamaño. Finalmente también
	 * muestra un resumen general y los promedios del código escrito. Esata funcionalidad fue
	 * incorporada el 2016-03-27 09:09
	 * 
	 * @param string $mode Modo en que será impreso el resumen (html o txt)
	 * 
	 * @return void No retorna valores, todos se imprimen
	 */
	public function summary($mode = "html")
	{
		/** Directorio inicial */
		$init_directory = ".";
		# Set time zone to UTC-6.
		$time_diff = 0;
		date_default_timezone_set('America/El_Salvador');
		#--------------------------------------------------------------------------

		$folders = Array(".");
		$this->add_folders(".", $folders);
		$self = "./" . pathinfo($_SERVER["PHP_SELF"], PATHINFO_BASENAME);
		$html_begin = '<!Doctype html>
		<html>
			<head>
				<title>Site stadistics</title>
				<style>
					article{overflow-y:scroll; height:100px; padding:5px; z-index:1;}
					body {font-family:monospace; overflow:hidden; position:relative; width:100%; height:100%; margin:0px;}
					h1 {margin:10px;}
					header {padding:5px; background-color:#eeeeee; box-shadow:0px 0px 5px 5px #999999; z-index:2;}
					html {position:absolute; width:100%; height:100%;}
					a {color:#0000ff;}
					a:hover {text-decoration:none;}
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
					<h1>Advances report for ' . $_SERVER["HTTP_HOST"] . '</h1>
					<a href="advances.php?mode=text">Download text file</a> |
					<a href="error_log.php">Error log</a> |
				</header>
				<article>
					<pre>';
					$text = "";
					$text .= "\r\n            DETAILS OF SITE\r\n";
					$text .= "    Generated " . Date("Y-m-d H:i:s", time() + $time_diff) . "\r\n";
					$text .= "           By: Edwin Fajardo.\r\n";
					$text .= "--------------------------------------------------------------\r\n";
					$text .= "\r\n";
					$total_files = 0;
					$total_size = 0;
					$total_lines = 0;
					$total_types = Array();
					$type_lines = Array();
					$finfo = finfo_open(FILEINFO_MIME_TYPE);
					foreach($folders as $folder)
					{
						$object = new DirectoryIterator($folder);
						$folder_files = 0;
						$folder_size = 0;
						$folder_lines = 0;
						$folder_text = "\r\nFolder: " . $folder . "\r\n\r\n";
						$folder_text .= "Lines\t|Size\t|Last modified\t\t|Filename\r\n";
						$folder_text .= "--------+-------+-----------------------+---------------------\r\n";
						$files = Array();
						foreach($object as $file_object)
						{
							$files[] = Array($file_object->getFilename(), $file_object->getMTime());
						}
						asort($files);
						foreach($files as $file_info)
						{
							$file = $folder . "/" . $file_info[0];
							$mime_type = finfo_file($finfo, $file);
							if(strncmp($mime_type, "text", 4) != 0 || $file == $self)
							{
								continue;
							}
							$modified = $file_info[1];
							$size = filesize($file);
							$extension = pathinfo($file, PATHINFO_EXTENSION);
							if(isset($total_types[$extension]))
							{
								$total_types[$extension]++;
							}
							else
							{
								$total_types[$extension] = 1;
							}
							$lines = 0;
							$file_descriptor = fopen($file, "r");
							while(!feof($file_descriptor))
							{
								$line = fgets($file_descriptor);
								$lines++;
							}
							$folder_text .= $lines . "\t|" . $size . "\t|" . Date("Y-m-d H:i:s", $modified + $time_diff) . "\t|" . $file_info[0]. "\r\n";
							fclose($file_descriptor);
							$folder_files++;
							$folder_size += $size;
							$folder_lines += $lines;
							if(isset($type_lines[$extension]))
							{
								$type_lines[$extension] += $lines;
							}
							else
							{
								$type_lines[$extension] = $lines;
							}
						}
						$folder_text .= "--------+-------+-----------------------+---------------------\r\n";
						$folder_text .= $folder_files . " files; " . $folder_lines . " lines; " . $folder_size . " bytes.\r\n";
						if($folder_files == 0)
						{
							continue;
						}
						else
						{
							$text .= $folder_text;
						}
						$total_files += $folder_files;
						$total_size += $folder_size;
						$total_lines += $folder_lines;
					}
					finfo_close($finfo);
					$text .= "\r\n\r\n";
					$text .= "                    TOTALS\r\n";
					$text .= "--------------------------------------------------------------\r\n";
					$text .= "Files: " . $total_files . "\r\n";
					$text .= "Bytes: " . $total_size . "\r\n";
					$text .= "Lines: " . $total_lines . "\r\n";
					$text .= "\r\n";
					$text .= "Lines per file: " . ($total_lines / $total_files) . "\r\n";
					$text .= "Bytes per line: " . ($total_size / $total_lines) . "\r\n";
					$text .= "Bytes per file: " . ($total_size / $total_files) . "\r\n";
					$text .= "\r\n";
					$text .= "--------File types--------\r\n";
					foreach($total_types as $key => $total)
					{
						$text .= $key . (strlen($key) > 7 ? "\t" : "\t\t") . $total . "\r\n";
					}
					$text .= "\r\n";
					$text .= "--------Lines by type of file--------\r\n";
					foreach($type_lines as $key => $total)
					{
						$text .= $key . (strlen($key) > 7 ? "\t" : "\t\t") . $total . "\r\n";
					}
		$html_end = '			</pre>
				</article>
			</body>
		</html>';
		if($mode != "text")
		{
			echo $html_begin;
			echo $text;
			echo $html_end;
		}
		else
		{
			$filename = "report_file_" . Date("Y-m-d_H-i-s", time() + $time_diff) . ".txt";
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
	}

	/**
	 * Agregado de carpetas
	 * 
	 * Esta función agregará a una lista cada uno de los archivos que serán procesados para el 
	 * cálculo de las estadísticas de código.
	 * 
	 * En la variable local $excluded_folders, debe agregarse sólo las carpetas que incluyan archivos
	 * de texto (Como la carpeta vendor/). Las carpetas que no incluyen archivos de texto son 
	 * excluídas de forma automática.
	 * 
	 * @param string $dir Directorio que se va a agregar
	 * @param array $array Arreglo en donde se agregará el directorio
	 * 
	 * @return void El resultado es recogido en la variable $array que es pasada por referencia.
	 */
	function add_folders($dir, &$array)
	{
		# Carpetas a excluir
		$excluded_folders = Array("./public/external", "./plugins", "./db", "./public/icons", "./vendor", "./node_modules");
		$list = glob($dir . "/*", GLOB_ONLYDIR);
		foreach($list as $directory)
		{
			if(in_array($directory, $excluded_folders))
			{
				continue;
			}
			array_push($array, $directory);
			$this->add_folders($directory, $array);
		}
	}
}
?>
