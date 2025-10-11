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
	use Entity, Preferences, Users, Roles, Information;
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
		$this->view->data["methods"] = availableMethodsModel::where("role_id", Session::get("role_id"))
		->where("module_id", $module->getModuleId())
		->orderBy("method_order")->getAllArray();
		$this->view->data["content"] = $this->view->render("generic_menu", true);
		$this->view->render("main");
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
		$result = [];
		switch($_POST["method"])
		{
			case "Entity":
				$result = $this->LoadEntityForm();
				break;
			case "NewUser":
			case "EditUser":
				$result = $this->LoadUserForm();
				break;
			case "Preferences":
				$result = $this->LoadPreferencesForm();
				break;
			case "NewRole":
			case "EditRole":
				$result = $this->LoadRoleForm();
				break;
			case "EditRoleMenu":
				$result = $this->LoadRoleMenuForm();
				break;
		}
		$this->json($result);
	}

	private function LoadEntityForm()
	{
		$result = [
			"update" => entitiesModel::find($this->entity_id)->toArray()
		];
		return $result;
	}

	private function LoadPreferencesForm()
	{
		$result = [
			"update" => []
		];
		$options = entityOptionsModel::join("app_options", "option_id")->getAll();
		foreach($options as $option)
		{
			$result["update"][$option["option_key"]] = $option["option_value"];
		}

		$optionValues = appOptionValuesModel::join("app_options", "option_id")->getAll();
		foreach($optionValues as $value)
		{
			if(!isset($result[$value["option_key"]]))
			{
				$result[$value["option_key"]] = [];
			}
			$result[$value["option_key"]][] = [
				"id" => $value["value_key"],
				"text" => _($value["value_label"])
			];
		}
		return $result;
	}

	private function LoadRoleForm()
	{
		$result = [
			"check" => []
		];
		$role = rolesModel::find($_POST["id"]);
		if($role->exists())
		{
			$result["update"] = $role->toArray();
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
			}
			unset($element);
			$result["check"]["read"] = $read;
			$result["check"]["create"] = $create;
			$result["check"]["update"] = $update;
			$result["check"]["delete"] = $delete;
		}
		else
		{
			$result["check"]["read"] = appElementsModel::select("element_id AS id")->getAll();
			$result["check"]["create"] = appElementsModel::select("element_id AS id")
				->where("is_creatable", 1)->getAll();
			$result["check"]["update"] = appElementsModel::select("element_id AS id")
				->where("is_updatable", 1)->getAll();
			$result["check"]["delete"] = appElementsModel::select("element_id AS id")
				->where("is_deletable", 1)->getAll();
		}
		return $result;
	}

	private function LoadRoleMenuForm()
	{
		$result = [];
		$role = rolesModel::find($_POST["id"]);
		if($role->exists())
		{
			$result = [
				"update" => $role->toArray(),
				"check" => [
					"modules" => roleModulesModel::select("module_id AS id")
						->where("role_id", $_POST["id"])->getAll(),
					"methods" => roleMethodsModel::select("method_id AS id")
						->where("role_id", $_POST["id"])->getAll()
				]
			];
		}
		return $result;
	}

	private function LoadUserForm()
	{
		# Cargando roles asignables
		$roles = rolesModel::getAll();
		$asignables = [];
		$permissions = Session::get("permissions");
		$result = [
			"asign" => []
		];
		foreach($roles as $role)
		{
			$elements = roleElementsModel::join("app_elements", "element_id")
				->where("role_id", $role->getRoleId())->getAll();
			$asignable = true;
			foreach($elements as $element)
			{
				$test = intval($element["permissions"]) & intval($permissions[$element["element_key"]]);
				if($permissions[$element["element_key"]] < $test)
				{
					$asignable = false;
				}
				$result["asign"][] = $test;
			}
			if($asignable)
			{
				$asignables[] = $role->getRoleId();
			}
		}
		$result["roles"] = rolesModel::whereIn($asignables)->list();
		if($_POST["method"] == "EditUser")
		{
			$result["update"] = usersModel::find($_POST["id"])->toArray();
			$result["check"] = [
				"modules" => userModulesModel::select("module_id AS id")
					->where("user_id", $_POST["id"])->getAll(),
				"methods" => userMethodsModel::select("method_id AS id")
					->where("user_id", $_POST["id"])->getAll()
			];
		}
		return $result;
	}
}
?>
