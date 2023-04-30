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
		$this->view->data["title"] = _("Installation");
		$this->view->standard_form();
		$this->view->data["nav"] = "";
		if(Session::get("authorization_code") == null)
		{
			$this->view->data["content"] = $this->view->render("installation/install_login", true);
			$this->view->render('main');
			return;
		}
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
		$this->view->data["title"] = _("Installation");
		$this->view->standard_form();
		$this->view->data["nav"] = "";
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
			$this->view->data["module_name"] = $module->getModuleName();
			$this->view->data["elements"] = $elements;
			$role_elements .= $this->view->render("installation/role_elements", true);
		}
		$this->view->data["role_elements"] = $role_elements;
		$this->view->data["content"] = $this->view->render("installation/role_and_user", true);
		$this->view->render('main');
	}

	public function Menu()
	{
		$this->view->data["title"] = _("Installation");
		$this->view->standard_form();
		$this->view->data["nav"] = "";
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
		$data = Array();
		if($this->entity_id != null)
		{
			$entity = entitiesModel::find($this->entity_id)->toArray();
			$user = usersModel::find($entity["admin_user"])->toArray();
			$data["update"] = array_merge($entity, $user);
			$data["check"] = Array(
				"modules" => entityModulesModel::select("module_id AS id")->getAll(),
				"methods" => entityMethodsModel::select("method_id AS id")->getAll()
			);
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
		if(!empty($_FILES["images"]["name"][0]))
		{
			$extension = strtolower(pathinfo($_FILES["images"]["name"][0], PATHINFO_EXTENSION));
			$dir = "entities/" . $data["subdomain"] . "/";
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
			move_uploaded_file($_FILES["images"]["tmp_name"][0], $file);
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
			"password" => empty($data["password"]) ? $user->getPassword() : md5($data["password"]),
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
}
?>
