<?php

foreach(glob("controllers/installation/*") as $file)
{
	include $file;
}

/**
 * Instalación
 * 
 * Esta clase permite la configuración inicial de los módulos y los métodos a utilizar por una
 * entidad. Asimismo, permite establecer un usuario administrador dentro de dicha entidad.
 * 
 * Incorporado el 2020-06-18 23:46
 * 
 * @author Edwin Fajardo <contacto@edwinfajardo.com>
 * @link https://www.edwinfajardo.com
 */
class Installation extends Controller
{
	use Entity;
	use Menu;
	use RoleAndUser;
	use Users;
	
	/**
	 * Constructor de la clase de instalación
	 * 
	 * Inicializa la propiedad module con el nombre de la clase.
	 * 
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
		$this->module = get_class($this);
		$this->view->data["module"] = $this->module;
		$this->view->data["nav"] = empty(Session::get("entity/entity_id")) ? "" : $this->view->render("installation/nav", true);
	}

	################################ VISTAS
	/**
	 * Inicio de la instalación
	 * 
	 * Verifica que se haya abierto sesión como instalador, de lo sontrario, muestra el
	 * formulario de inicio de sesión.
	 * 
	 * @param string $subdomain El subdominio que se asignará a la entidad
	 * 
	 * @return void
	 */
	public function index($subdomain = "")
	{
		$this->NewEntity($subdomain);
	}

	/**
	 * Iniciar
	 * 
	 * Este método es utilizado para las instalaciones locales a través del cliente Windows, como
	 * primera pantalla antes de configurarse la entidad.
	 * 
	 * @return void
	 */
	function GetStarted()
	{
		$this->view->data["title"] = _("Start");
		$this->view->standard_error();
		$this->view->data["nav"] = "";
		$this->view->data["welcome"] = sprintf(_("Welcome to %s!"), $this->system_name);
		$this->view->data["content"] = $this->view->render("installation/install_missing_conf", true);
		$this->view->render('main');
	}

	/**
	 * Datos de la aplicación
	 * 
	 * Imprime el contenido del archivo app_info.json
	 * 
	 * @return void
	 */
	function AppInfo() 
	{
		$this->InstallerRequired();
		$this->view->data["title"] = _("App info");
		$this->view->standard_error();
		$this->view->data["text"] = file_get_contents("app_info.json");
		$this->view->data["content"] = $this->view->render("installation/text_viewer", true);
		$this->view->render('main');
	}

	/**
	 * Datos de la sesión
	 * 
	 * Imprime los datos contenidos en la variable global $_SESSION
	 * 
	 * @return void
	 */
	function SessionData() 
	{
		$this->InstallerRequired();
		$this->view->data["title"] = _("Session data");
		$this->view->standard_error();
		$this->view->data["text"] = json_encode($_SESSION, JSON_PRETTY_PRINT);
		$this->view->data["content"] = $this->view->render("installation/text_viewer", true);
		$this->view->render('main');
	}

	/**
	 * Acerca de
	 * 
	 * Muestra información acerca del sistema.
	 * 
	 * @return void
	 */
	public function PHPInfo()
	{
		$this->InstallerRequired();
		$this->view->data["title"] = _("PHP info");
		$this->view->standard_details();
		$this->view->data["content_id"] = "php_info";
		$this->view->data["content"] = $this->view->render("content_loader", true);
		$this->view->render('main');
	}

	/**
	 * Datos de la sesión
	 * 
	 * Imprime los datos contenidos en la variable global $_SESSION
	 * 
	 * @return void
	 */
	function Summary() 
	{
		$this->InstallerRequired();
		$this->view->data["title"] = _("Summary");
		$this->view->standard_error();
		/** Directorio inicial */
		$init_directory = ".";
		# Set time zone to UTC-6.
		$time_diff = 0;
		date_default_timezone_set('America/El_Salvador');
		#--------------------------------------------------------------------------
		$folders = Array(".");
		$this->add_folders(".", $folders);
		$self = "./" . pathinfo($_SERVER["PHP_SELF"], PATHINFO_BASENAME);
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
		$this->view->data["text"] = $text;
		$this->view->data["content"] = $this->view->render("installation/text_viewer", true);
		$this->view->render('main');
	}

	public function ErrorLog()
	{
		$this->InstallerRequired();
		$this->view->data["title"] = _("Error log");
		$this->view->standard_error();
		$text = "No errors found!";
		if(file_exists("error_log"))
		{
			$text = file_get_contents("error_log");
		}
		$this->view->data["text"] = $text;
		$this->view->data["content"] = $this->view->render("installation/text_viewer", true);
		$this->view->render('main');
	}

	################################ LISTAS Y FORMULARIOS
	/**
	 * Cargar datos de formulario.
	 * 
	 * Imprime en formato JSON los datos esenciales para el uso de formularios dentro del módulo.
	 * 
	 * @return void
	 */
	public function load_form_data()
	{
		$data = [];
		if($this->entity_id != null)
		{
			$entity = entitiesModel::find($this->entity_id)->toArray();
			$admin_user = usersModel::find($entity["admin_user"])->toArray();
			$data["update"] = array_merge($entity, $admin_user);
			$data["check"] = [
				"modules" => entityModulesModel::select("module_id AS id")->getAll(),
				"methods" => entityMethodsModel::select("method_id AS id")->getAll()
			];
			$admin_role = rolesModel::find($entity["admin_role"]);
			if($admin_role->exists())
			{
				$read = [];
				$create = [];
				$update = [];
				$delete = [];
				$elements = roleElementsModel::where("role_id", $admin_role->getRoleId())->getAll();
				foreach($elements as &$element)
				{
					if((intval($element->getPermissions()) & 8) != 0)
					{
						$read[] = ["id" => $element->getElementId()];
					}
					if((intval($element->getPermissions()) & 4) != 0)
					{
						$create[] = ["id" => $element->getElementId()];
					}
					if((intval($element->getPermissions()) & 2) != 0)
					{
						$update[] = ["id" => $element->getElementId()];
					}
					if((intval($element->getPermissions()) & 1) != 0)
					{
						$delete[] = ["id" => $element->getElementId()];
					}
					$data["check"]["read"] = $read;
					$data["check"]["create"] = $create;
					$data["check"]["update"] = $update;
					$data["check"]["delete"] = $delete;
				}
				unset($element);
			}
			else
			{
				$data["check"]["read"] = appElementsModel::select("element_id AS id")->getAll();
				$data["check"]["create"] = appElementsModel::select("element_id AS id")
					->where("is_creatable", 1)->getAll();
				$data["check"]["update"] = appElementsModel::select("element_id AS id")
					->where("is_updatable", 1)->getAll();
				$data["check"]["delete"] = appElementsModel::select("element_id AS id")
					->where("is_deletable", 1)->getAll();
			}
		}
		$this->json($data);
	}

	/**
	 * Verificación de sesión
	 * 
	 * Verifica elusuario y contraseña de instalador enviada a través del formulario de inicio de
	 * sesión. Si es correcto, inicia la sesión, de los contrario, imprime un mensaje de error.
	 * 
	 * @return void
	 */
	public function TestAuthorization()
	{
		$response = ["success" => false];
		if(empty($_POST["nickname"]) || empty($_POST["password"]))
		{
			$response += [
				"title" => "Error",
				"message" => _("Enter your installer user and password"),
				"theme" => "red"
			];
			$this->json($response);
			return;
		}
		$installer = appInstallersModel::where("installer_nickname", $_POST["nickname"])->get();
		if(password_verify($_POST["password"], $installer->getInstallerPassword()))
		{
			Session::set("authorization_code", true);
			Session::set("installer_id", $installer->getInstallerId());
			$response["reload"] = true;
		}
		else
		{
			$response += [
				"title" => "Error",
				"message" => _("Bad user or password"),
				"theme" => "red",
				"no_reset" => true
			];
		}
		$this->json($response);
	}

	public function php_info_loader()
	{
		ob_start () ;
		phpinfo () ;
		$pinfo = ob_get_contents () ;
		ob_end_clean () ;
		$pinfo = preg_replace('/\s+/', ' ', $pinfo);
		$pinfo = preg_replace('/(,)(?=[^\s])/', ', ', $pinfo);
		$pinfo = preg_replace('/(:\/)(?=[^\/])/', ': /', $pinfo);
		$pinfo = substr($pinfo, strpos($pinfo, '<body>') + 6);
		$pinfo = substr($pinfo, 0, strpos($pinfo, '</body>'));
		$pinfo = str_replace('<table', '<table class="show_details_table"', $pinfo);
		echo $pinfo;
	}
	################################ GUARDADO DE DATOS

	/**
	 * Validación de sesión de instalador
	 * 
	 * Verifica si hay una sesión abierta para el instalador.
	 * 
	 * @param string $type Tipo de respuesta esperada (html, internal o JSON)
	 */
	protected function InstallerRequired($type = 'html')
	{
		# Validar si el sistema permite el registro de nuevas entidades
		if(Session::get("entity/entity_id") == null && CREATE_ENTITY != "OPEN")
		{
			$this->view->data["title"] = _("Installation");
			$this->view->standard_error();
			$this->view->data["nav"] = "";
			$this->view->data["content"] = $this->view->render("installation/closed", true);
			$this->view->render('main');
			exit();
		}

		if(Session::get("installer_id") == null)
		{
			if($type == 'json')
			{
				$this->json([
					"success" => false,
					"error" => true,
					"message" => _("You are not logged in"),
					"title" => "Error",
					"theme" => "red"
				]);
			}
			elseif($type == 'internal')
			{
				$this->view->render("main/error");
			}
			else
			{
				$this->view->data["title"] = _("Log in");
				$this->view->standard_form();
				$this->view->data["nav"] = "";
				$this->view->data["content"] = $this->view->render("installation/install_login", true);
				$this->view->render('main');
			}
			exit();
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
