<?php

foreach(glob("controllers/settings/*") as $file)
{
	include $file;
}

/**
 * Controlador de configuraciones
 * 
 * Permite gestionar datos de la entidad y de los usuarios.
 * 
 * Incorporado el 2020-06-18 23:46
 * @author Edwin Fajardo <contacto@edwinfajardo.com>
 * @link https://www.edwinfajardo.com
 */

class Settings extends Controller
{
	use Entity, Preferences, Users, Roles;
	/**
	 * Constructor de la clase
	 * 
	 * Inicializa la propiedad module con el nombre de la clase
	 */
	public function __construct()
	{
		parent::__construct();
		$this->module = get_class($this);
		$this->view->data["module"] = $this->module;
	}

	################################ VISTAS
	/**
	 * Vista principal
	 * 
	 * Muestra un menú con las principales actividades que se pueden realizar en el módulo.
	 * 
	 * @return void
	 */
	public function index()
	{
		$this->session_required("html", $this->module);
		$this->view->standard_menu();
		$this->view->data["nav"] = $this->view->render("main/nav", true);
		$module = appModulesModel::findBy("module_url", $this->module);
		$this->view->data["title"] = _($module->getModuleName());
		$this->view->data["methods"] = availableMethodsModel::where("user_id", Session::get("user_id"))
		->where("module_id", $module->getModuleId())
		->orderBy("method_order")->getAllArray();
		$this->view->data["content"] = $this->view->render("generic_menu", true);
		$this->view->render("main");
	}

	/**
	 * Acerca de
	 * 
	 * Muestra información acerca del sistema.
	 * 
	 * @return void
	 */
	public function About()
	{
		$this->session_required("html", $this->module);
		$this->view->data["title"] = sprintf(_("About %s"), $this->system_name);
		$this->view->standard_details();
		$this->view->data["nav"] = $this->view->render("main/nav", true);
		$this->view->data["content_id"] = "info_details";
		$this->view->data["content"] = $this->view->render("content_loader", true);
		$this->view->render('main');
	}

	################################ LISTAS Y FORMULARIOS
	/**
	 * Carga de datos de formulario.
	 * 
	 * Imprime, en formato JSON, los datos iniciales para la carga de formularios.
	 * 
	 * @return void
	 */
	public function load_form_data()
	{
		$data = Array();
		if($_POST["method"] == "Entity")
		{
			$data["update"] = entitiesModel::find($this->entity_id)->toArray();
		}
		if($_POST["method"] == "NewUser" || $_POST["method"] == "EditUser")
		{
			# Cargando roles asignables
			$roles = rolesModel::getAll();
			$asignables = [];
			$permissions = Session::get("permissions");
			$data["asign"] = Array();
			foreach($roles as $role)
			{
				$elements = roleElementsModel::join("app_elements", "element_id")->where("role_id", $role->getRoleId())->getAll();
				$asignable = true;
				foreach($elements as $element)
				{
					$test = intval($element["permissions"]) & intval($permissions[$element["element_key"]]);
					if($permissions[$element["element_key"]] < $test)
					{
						$asignable = false;
					}
					$data["asign"][] = $test;
				}
				if($asignable)
				{
					$asignables[] = $role->getRoleId();
				}
			}
			$data["roles"] = rolesModel::whereIn($asignables)->list();
		}
		if($_POST["method"] == "EditUser")
		{
			$data["update"] = usersModel::find($_POST["id"])->toArray();
			$data["check"] = Array();
			$data["check"]["modules"] = userModulesModel::select("module_id AS id")
				->where("user_id", $_POST["id"])->getAll();
			$data["check"]["methods"] = userMethodsModel::select("method_id AS id")
				->where("user_id", $_POST["id"])->getAll();
		}
		if($_POST["method"] == "Preferences")
		{
			$options = entityOptionsModel::join("app_options", "option_id")->getAll();
			$data["update"] = Array();
			foreach($options as $option)
			{
				$data["update"][$option["option_key"]] = $option["option_value"];
			}
		}
		if($_POST["method"] == "NewRole" || $_POST["method"] == "EditRole")
		{
			$role = rolesModel::find($_POST["id"]);
			if($role->exists())
			{
				$read = [];
				$create = [];
				$update = [];
				$delete = [];
				$elements = roleElementsModel::where("role_id", $role->getRoleId())->getAll();
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
	 * Carga de información del sistema
	 * 
	 * Imprime, en formato HTML, la información del sistema: datos de la última actualización e 
	 * información de contacto.
	 * 
	 * @return void
	 */
	public function info_details_loader($mode = "embedded")
	{
		$info = Array();
		if(file_exists("app_info.json"))
		{
			$info = json_decode(file_get_contents("app_info.json"), true);
			$info["last_update_ago"] = date_utilities::sql_date_to_ago($info["last_update"]);
			$info["last_update"] = date_utilities::sql_date_to_string($info["last_update"], true);
		}
		else
		{
			$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($_SERVER["DOCUMENT_ROOT"]), RecursiveIteratorIterator::SELF_FIRST);
			$last_modified = 0;
			foreach($files as $file_object)
			{
				$modified = $file_object->getMTime();
				if($modified > $last_modified)
				{
					$last_modified = $modified;
				}
			}
			$info["last_update"] = date_utilities::sql_date_to_string(Date("Y-m-d H:i:s", $last_modified), true);
			$info["last_update_ago"] = date_utilities::sql_date_to_ago(Date("Y-m-d H:i:s", $last_modified));
			$info["version"] = "1.0";
			$info["number"] = "0";
		}
		foreach($info as $key => $item)
		{
			$this->view->data[$key] = $item;
		}
		if($mode == "standalone")
		{
			$this->view->data["title"] = sprintf(_("About %s"), $this->system_name);
			$this->view->standard_details();
			$this->view->add("styles", "css", Array(
				'styles/standalone.css'
			));
			$this->view->restrict[] = "embedded";
			$this->view->data["content"] = $this->view->render('settings/info_details', true);
			$this->view->render('clean_main');
		}
		else
		{
			$this->view->render('settings/info_details');
		}
	}
}
?>
