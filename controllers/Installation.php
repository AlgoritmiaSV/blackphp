<?php
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
		$this->installer_required();
		$this->view->data["title"] = _("Installation");
		$this->view->standard_form();
		$this->view->data["subdomain"] = $subdomain;
		if(Session::get("user_id") == null)
		{
			$this->view->restrict[] = "inside_installation";
		}
		else
		{
			$this->view->restrict[] = "outside_installation";
		}
		$this->view->data["content"] = $this->view->render("installation/entity_data", true);
		$this->view->render('main');
	}

	/**
	 * Iniciar
	 * 
	 * Este método es utilizado para las instalaciones locales a través del cliente Windows, como
	 * primera pantalla antes de configurarse la entidad.
	 * 
	 * @return void
	 */
	function getStarted() 
	{
		$this->view->data["title"] = _("Start");
		$this->view->standard_error();
		$this->view->data["nav"] = "";
		$this->view->data["welcome"] = sprintf(_("Welcome to %s!"), $this->system_name);
		$this->view->data["content"] = $this->view->render("installation/install_missing_conf", true);
		$this->view->render('main');
	}

	/**
	 * Nueva entidad
	 * 
	 * Alias de index()
	 * 
	 * @param string $subdomain El subdominio con el que se registrará la entidad
	 * 
	 * @return void
	 */
	public function NewEntity($subdomain)
	{
		$this->index($subdomain);
	}

	public function RoleAndUser()
	{
		$this->installer_required();
		$this->view->data["title"] = _("Role and user");
		$this->view->standard_form();
		if(Session::get("authorization_code") == null)
		{
			$this->view->data["content"] = $this->view->render("installation/install_login", true);
			$this->view->render('main');
			return;
		}
		if(Session::get("user_id") == null)
		{
			$this->view->restrict[] = "inside_installation";
		}
		else
		{
			$this->view->restrict[] = "outside_installation";
		}
		$role_elements = "";
		$modules = appModulesModel::getAll();
		foreach($modules as $module)
		{
			$elements = appElementsModel::where("module_id", $module->getModuleId())->getAllArray();
			foreach($elements as &$element)
			{
				$element["element_name"] = _($element["element_name"]);
				if($element["is_creatable"] == 0)
				{
					$element["creatable"] = "disabled";
				}
				if($element["is_updatable"] == 0)
				{
					$element["updatable"] = "disabled";
				}
				if($element["is_deletable"] == 0)
				{
					$element["deletable"] = "disabled";
				}
			}
			unset($element);
			$this->view->data["module_id"] = $module->getModuleId();
			$this->view->data["module_name"] = _($module->getModuleName());
			$this->view->data["elements"] = $elements;
			$role_elements .= $this->view->render("installation/role_elements", true);
		}
		$this->view->data["role_elements"] = $role_elements;
		$this->view->data["content"] = $this->view->render("installation/role_and_user", true);
		$this->view->render('main');
	}

	public function Menu()
	{
		$this->installer_required();
		$this->view->data["title"] = _("Menu");
		$this->view->standard_form();
		if(Session::get("authorization_code") == null)
		{
			$this->view->data["content"] = $this->view->render("installation/install_login", true);
			$this->view->render('main');
			return;
		}
		$modules = appModulesModel::orderBy("default_order")->getAllArray();
		$this->view->data["modules"] = "";
		foreach($modules as $module)
		{
			foreach($module as $key => $item)
			{
				$this->view->data[$key] = $item;
			}
			$this->view->data["methods"] = appMethodsModel::where("module_id", $module["module_id"])->orderBy("default_order")->getAllArray();
			$this->view->data["modules"] .= $this->view->render("modules", true);
		}
		if(Session::get("user_id") == null)
		{
			$this->view->restrict[] = "inside_installation";
		}
		else
		{
			$this->view->restrict[] = "outside_installation";
		}
		$this->view->data["content"] = $this->view->render("installation/menu", true);
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
		$this->installer_required();
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
		$this->installer_required();
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
		$this->installer_required();
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
		$this->installer_required();
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
		$this->installer_required();
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
	public function test_authorization()
	{
		$data = $_POST;
		$data["success"] = false;
		if(empty($data["nickname"]) || empty($data["password"]))
		{
			$data["title"] = "Error";
			$data["message"] = _("Enter your installer user and password");
			$data["theme"] = "red";
			$this->json($data);
			return;
		}
		$installer = appInstallersModel::where("installer_nickname", $data["nickname"])->get();
		if(password_verify($data["password"], $installer->getInstallerPassword()))
		{
			Session::set("authorization_code", true);
			Session::set("installer_id", $installer->getInstallerId());
			$data["reload"] = true;
		}
		else
		{
			$data["title"] = "Error";
			$data["message"] = _("Bad user or password");
			$data["theme"] = "red";
			$data["no_reset"] = true;
		}
		$this->json($data);
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
	 * Guardar datos de la entidad.
	 * 
	 * Crea o actualiza una entidad, crea o actualiza un usuario administrador, guarda el logotipo
	 * enviado desde el formulario, y asigna los permisos a los métodos y módulos correspondientes
	 * tanto a la entidad como al usuario administrador.
	 * Finalmente, redirige hacia el subdominio creado.
	 * 
	 * @return void
	 */
	public function save_entity()
	{
		$data = $_POST;
		$data["success"] = false;
		$now = Date("Y-m-d H:i:s");
		$today = Date("Y-m-d");
		#Check session type
		$entity_id = $this->entity_id;
		if($entity_id == null)
		{
			#New installation
			#Check subdomain
			$reserved_subdomains = Array("www", "master", "admin", "installer", "negkit", "fayrasystems", "system", "sistema", "administrador", "administrator", "redteleinformatica", "local", "blackphp");
			if($_SERVER["SERVER_NAME"] != $_SERVER["SERVER_ADDR"])
			{
				if(empty($data["subdomain"]))
				{
					$data["title"] = "Error";
					$data["message"] = _("No subdomain chosen");
					$data["theme"] = "red";
					$this->json($data);
					return;
				}
			}
			$entity = entitiesModel::where("entity_subdomain", $data["subdomain"])->get()->toArray();
			if(isset($entity["entity_id"]) || in_array($data["subdomain"], $reserved_subdomains))
			{
				$data["title"] = "Error";
				$data["message"] = sprintf(_("The subdomain %s is not available"), $data["subdomain"]);
				$data["theme"] = "red";
				$this->json($data);
				return;
			}
		}

		$entity = entitiesModel::find($this->entity_id);
		$subdomain = empty($data["subdomain"]) ? $entity->getEntitySubdomain() : $data["subdomain"];
		if(empty($entity->getEntityId()))
		{
			$entity->set(Array(
				"entity_subdomain" => $subdomain,
				"entity_date" => $today,
				"entity_begin" => $today,
				"creation_installer" => Session::get("installer_id"),
				"creation_time" => $now,
				"edition_installer" => Session::get("installer_id"),
				"installer_edition_time" => $now
			));
		}
		$entity->set(Array(
			"entity_name" => $data["entity_name"],
			"app_name" => empty($data["app_name"]) ? ucfirst($subdomain) : $data["app_name"],
			"entity_slogan" => $data["entity_slogan"],
			"edition_user" => Session::get("user_id") == null ? 0 : Session::get("user_id"),
			"user_edition_time" => $now
		));
		$entity->save();
		if(empty($entity->getEntityId()))
		{
			$data["title"] = "Error";
			$data["message"] = _("Failed to create the entity");
			$data["theme"] = "red";
			$this->json($data);
			return;
		}

		#Logo
		if(!empty($_FILES["logo"]["name"]))
		{
			$extension = strtolower(pathinfo($_FILES["logo"]["name"], PATHINFO_EXTENSION));
			$dir = "entities/" . $subdomain . "/";
			if($_SERVER["SERVER_NAME"] == $_SERVER["SERVER_ADDR"])
			{
				$dir = "entities/local/";
			}
			$file = $dir . "logo." . $extension;
			$generic_file = glob($dir . "logo.*");
			if(!is_dir($dir))
			{
				mkdir($dir, 0755, true);
			}
			else
			{
				foreach($generic_file as $previous)
				{
					unlink($previous);
				}
			}
			move_uploaded_file($_FILES["logo"]["tmp_name"], $file);
		}

		#Finish and response
		$data["success"] = true;
		$data["title"] = _("Success");
		$data["message"] = _("Installation completed successfully");
		$data["theme"] = "green";
		$data["no_reset"] = true;
		if($_SERVER["SERVER_NAME"] != $_SERVER["SERVER_ADDR"])
		{
			$protocol = "http";
			if((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443 ){
				$protocol .= "s";
			}
			$data["redirect_after"] = $protocol . "://" . str_replace("installer", $data["subdomain"], $_SERVER["SERVER_NAME"]) . "/Installation/RoleAndUser/";
		}
		else
		{
			$data["reload_after"] = true;
		}
		$this->json($data);
	}

	public function save_role_and_user()
	{
		$data = $_POST;
		$data["success"] = false;
		$now = Date("Y-m-d H:i:s");
		$today = Date("Y-m-d");

		#Check session type
		$entity = entitiesModel::find($this->entity_id);

		# Creación del rol administrador
		$role = rolesModel::find($entity->getAdminRole());
		if(!$role->exists())
		{
			$role->set([
				"entity_id" => $entity->getEntityId(),
				"role_name" => "Administrator"
			])->save();
			$entity->setAdminRole($role->getRoleId());
			$entity->save();
		}
		$elements = Array();
		foreach($_POST["read"] as $element)
		{
			$elements[$element] = 8;
		}
		foreach($_POST["create"] as $element)
		{
			$elements[$element] = intval($elements[$element]) + 4;
		}
		foreach($_POST["update"] as $element)
		{
			$elements[$element] = intval($elements[$element]) + 2;
		}
		foreach($_POST["delete"] as $element)
		{
			$elements[$element] = intval($elements[$element]) + 1;
		}
		foreach($elements as $element_id => $permissions)
		{
			$role_element = roleElementsModel::where("role_id", $role->getRoleId())->where("element_id", $element_id)->get();
			$role_element->set([
				"role_id" => $role->getRoleId(),
				"element_id" => $element_id,
				"permissions" => $permissions,
				"status" => 1
			])->save();
		}
		roleElementsModel::where("role_id", $role->getRoleId())->whereNotIn(array_keys($elements), "element_id")->update(["status" => 0]);
		$data["elements"] = $elements;

		#Save default user
		$user = usersModel::find($data["admin_user"]);

		$user->set(Array(
			"entity_id" => $entity->getEntityId(),
			"user_name" => $data["user_name"],
			"nickname" => $data["nickname"],
			"password" => empty($data["password"]) ? $user->getPassword() : "HASH",
			"password_hash" => empty($data["password"]) ? $user->getPasswordHash() : password_hash($_POST["password"], PASSWORD_BCRYPT),
			"role_id" => $role->getRoleId(),
			"theme_id" => 1
		))->save();
		$entity->setAdminUser($user->getUserId());
		$entity->save();

		#Finish and response
		$data["success"] = true;
		$data["title"] = _("Success");
		$data["message"] = _("Installation completed successfully");
		$data["theme"] = "green";
		$data["no_reset"] = true;
		if($_SERVER["SERVER_NAME"] != $_SERVER["SERVER_ADDR"])
		{
			$protocol = "http";
			if((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443 ){
				$protocol .= "s";
			}
			$data["redirect_after"] = $protocol . "://" . str_replace("installer", $data["subdomain"], $_SERVER["SERVER_NAME"]) . "/Installation/Menu/";
		}
		else
		{
			$data["reload_after"] = true;
		}
		$this->json($data);
	}

	public function save_menu()
	{
		$data = $_POST;
		$data["success"] = false;
		$now = Date("Y-m-d H:i:s");
		$today = Date("Y-m-d");
		#Check session type
		$entity_id = $this->entity_id;
		if($entity_id == null)
		{
			header("Location: /" . $this->module . "/");
		}

		$entity = entitiesModel::find($this->entity_id);

		#Set modules
		$i = 0;
		entityModulesModel::where("entity_id", $entity->getEntityId())->whereNotIn($data["modules"], "module_id")->update(Array("status" => 0));
		foreach($data["modules"] as $module_id)
		{
			$i++;
			$module = entityModulesModel::where("module_id", $module_id)->where("entity_id", $entity->getEntityId())->where("status", ">=", 0)->get();
			if(empty($module->getEmoduleId()))
			{
				$module->set(Array(
					"entity_id" => $entity->getEntityId(),
					"module_id" => $module_id,
					"creation_time" => $now,
				));
			}
			$module->set(Array(
				"module_order" => $i,
				"edition_time" => $now,
				"status" => 1
			))->save();
		}

		#Set methods
		entityMethodsModel::where("entity_id", $entity->getEntityId())->whereNotIn($data["methods"], "method_id")->update(Array("status" => 0));
		$i = 0;
		foreach($data["methods"] as $method_id)
		{
			$i++;
			$method = entityMethodsModel::where("method_id", $method_id)->where("entity_id", $entity->getEntityId())->where("status", ">=", 0)->get();
			if(empty($method->getEmethodId()))
			{
				$method->set(Array(
					"entity_id" => $entity->getEntityId(),
					"method_id" => $method_id,
					"creation_time" => $now,
				));
			}
			$method->set(Array(
				"method_order" => $i,
				"edition_time" => $now,
				"status" => 1
			))->save();
		}

		$user = usersModel::find($entity->getAdminUser());
		#Set user modules permissions
		if($user->getUserId() != Session::get("user_id"))
		{
			#Set modules
			userModulesModel::where("user_id", $user->getUserId())->whereNotIn($data["modules"], "module_id")->update(Array("status" => 0));
			foreach($data["modules"] as $module_id)
			{
				$module = userModulesModel::where("module_id", $module_id)->where("user_id", $user->getUserId())->where("status", ">=", 0)->get();
				$module->set(Array(
					"module_id" => $module_id,
					"user_id" => $user->getUserId(),
					"status" => 1
				))->save();
			}

			#Set methods
			userMethodsModel::where("user_id", $user->getUserId())->whereNotIn($data["methods"], "method_id")->update(Array("status" => 0));
			foreach($data["methods"] as $method_id)
			{
				$method = userMethodsModel::where("method_id", $method_id)->where("user_id", $user->getUserId())->where("status", ">=", 0)->get();
				$method->set(Array(
					"user_id" => $user->getUserId(),
					"method_id" => $method_id,
					"status" => 1
				))->save();
			}
		}
		#Finish and response
		$data["success"] = true;
		$data["title"] = _("Success");
		$data["message"] = _("Installation completed successfully");
		$data["theme"] = "green";
		$data["no_reset"] = true;
		if($_SERVER["SERVER_NAME"] != $_SERVER["SERVER_ADDR"])
		{
			$protocol = "http";
			if((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443 ){
				$protocol .= "s";
			}
			$data["redirect_after"] = $protocol . "://" . str_replace("installer", $data["subdomain"], $_SERVER["SERVER_NAME"]);
		}
		else
		{
			$data["reload_after"] = true;
		}
		#Close installer session
		Session::destroy();
		$this->json($data);
	}

	/**
	 * Validación de sesión de instalador
	 * 
	 * Verifica si hay una sesión abierta para el instalador.
	 * 
	 * @param string $type Tipo de respuesta esperada (html, internal o JSON)
	 */
	protected function installer_required($type = 'html')
	{
		if(Session::get("installer_id") == null)
		{
			if($type == 'json')
			{
				$this->json(Array(
					"success" => false,
					"error" => true,
					"message" => _("You are not logged in"),
					"title" => "Error",
					"theme" => "red"
				));
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
