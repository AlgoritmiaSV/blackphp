<?php
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
		$this->view->data["nav"] = $this->view->render("nav", true);
		$module = app_modules_model::where("module_url", $this->module)->get();
		$this->view->data["title"] = _($module->getModule_name());
		$this->view->data["methods"] = available_methods_model::where("user_id", Session::get("user_id"))
		->where("module_id", $module->getModule_id())
		->orderBy("method_order")->getAllArray();
		$this->view->data["content"] = $this->view->render("generic_menu", true);
		$this->view->render("main");
	}

	/**
	 * Datos de la entidad
	 * 
	 * Muestra un formulario para modificar información general de la entidad, como el nombre,
	 * dirección, logotipo y formas de contacto.
	 * 
	 * @return void
	 */
	public function Entity()
	{
		$this->session_required("html", $this->module);
		$this->view->data["title"] = _("Entity data");
		$this->view->standard_form();
		$this->view->data["nav"] = $this->view->render("nav", true);
		$this->view->data["content"] = $this->view->render("settings/entity", true);
		$this->view->render('main');
	}

	/**
	 * Preferencias del sistema
	 * 
	 * Muestra un formulario para cambiar datos opcionales en el sistema
	 * 
	 * @return void
	 */
	public function Preferences()
	{
		$this->session_required("html", $this->module);
		$this->view->data["title"] = _("Preferences");
		$this->view->standard_form();
		$this->view->data["nav"] = $this->view->render("nav", true);
		$this->view->data["config_modules"] = Array();
		foreach($this->view->data["modules"] as $key => $module)
		{
			$switches = entity_options_model::join("app_options", "option_id")->where("module_id", $module["module_id"])->where("option_type", 1)->getAllArray();
			$fields = entity_options_model::join("app_options", "option_id")->where("module_id", $module["module_id"])->where("option_type", 2)->getAllArray();
			if(count($switches) > 0 || count($fields) > 0)
			{
				$this->view->data["switches"] = $switches;
				$this->view->data["fields"] = $fields;
				$this->view->data["config_modules"][] = Array(
					"module_name" => $module["module_name"],
					"preferences" => $this->view->render("settings/preference_item", true)
				);
			}
		}
		$this->view->data["content"] = $this->view->render("settings/preferences", true);
		$this->view->render('main');
	}

	/**
	 * Usuarios
	 * 
	 * Muestra una lista de usuarios del sistema
	 * 
	 * @return void
	 */
	public function Users()
	{
		$this->session_required("html", $this->module);
		$this->view->data["title"] = _("Users");
		$this->view->standard_list();
		$this->view->data["nav"] = $this->view->render("nav", true);
		$this->view->data["print_title"] = _("Users");
		$this->view->data["print_header"] = $this->view->render("print_header", true);
		$this->view->data["content"] = $this->view->render("settings/user_list", true);
		$this->view->render('main');
	}

	/**
	 * Nuevo usuario
	 * 
	 * Muestra un formulario que permite registrar nuevos usuarios en el sistema, y asignarles
	 * permisos a diferentes módulos. Un usuario autorizado para registrar usuarios, sólo puede
	 * otorgar permisos que le han sido otorgados.
	 * 
	 * @return void
	 */
	public function NewUser()
	{
		$this->session_required("html", $this->module);
		$this->view->data["title"] = _("New user");
		$this->view->standard_form();
		$this->view->data["nav"] = $this->view->render("nav", true);
		$this->view->restrict[] = "edition";
		$modules = available_modules_model::where("user_id", Session::get("user_id"))->orderBy("module_order")->getAllArray();
		$this->view->data["modules"] = "";
		foreach($modules as $module)
		{
			foreach($module as $key => $item)
			{
				$this->view->data[$key] = $item;
			}
			$this->view->data["methods"] = available_methods_model::where("user_id", Session::get("user_id"))
			->where("module_id", $module["module_id"])
			->orderBy("method_order")->getAllArray();
			$this->view->data["modules"] .= $this->view->render("modules", true);
		}
		$this->view->data["content"] = $this->view->render("settings/user_edit", true);
		$this->view->render('main');
	}

	/**
	 * Editar usuario
	 * 
	 * Permite editar los datos y los permisos de un usuario.
	 * 
	 * @return void
	 */
	public function EditUser($user_id)
	{
		$this->session_required("html", $this->module);
		$this->view->data["title"] = _("Edit user");
		$this->view->standard_form();
		$this->view->data["nav"] = $this->view->render("nav", true);
		if($user_id == Session::get("user_id"))
		{
			$this->view->restrict[] = "no_self";
		}
		$this->view->restrict[] = "creation";
		$modules = available_modules_model::where("user_id", Session::get("user_id"))->orderBy("module_order")->getAllArray();
		$this->view->data["modules"] = "";
		foreach($modules as $module)
		{
			foreach($module as $key => $item)
			{
				$this->view->data[$key] = $item;
			}
			$this->view->data["methods"] = available_methods_model::where("user_id", Session::get("user_id"))
			->where("module_id", $module["module_id"])
			->orderBy("method_order")->getAllArray();
			$this->view->data["modules"] .= $this->view->render("modules", true);
		}
		$this->view->data["content"] = $this->view->render("settings/user_edit", true);
		$this->view->render('main');
	}

	################################ LISTAS Y FORMULARIOS
	/**
	 * Cargar tabla de usuarios
	 * 
	 * Devuelve, en formato JSON o en un archivo Excel, la lista de usuarios.
	 * 
	 * @return void
	 */
	public function users_table_loader($response = "JSON")
	{
		$this->session_required("json");
		$data = Array();
		$users = users_model::getAllArray();
		$status = Array(_("Deleted"), _("Active"), _("Inactive"));
		foreach($users as $key => $user)
		{
			$users[$key]["status"] = $status[$users[$key]["status"]];
		}
		$data["content"] = $users;
		if($response == "Excel")
		{
			$data["title"] = _("Users");
			$data["headers"] = Array(_("User"), _("Complete name"), _("Status"));
			$data["fields"] = Array("nickname", "user_name", "status");
			excel::create_from_table($data, "Users_" . Date("YmdHis") . ".xlsx");
		}
		else
		{
			echo json_encode($data);
		}
	}

	/**
	 * Guardar usuario
	 * 
	 * Crea o actualiza un usuario en la base de datos, asignándole permisos a cada módulo
	 * y a cada método seleccionado en el formulario.
	 * 
	 * @return void
	 */
	public function save_user()
	{
		$this->session_required("json");
		$data = Array("success" => false);
		if(empty($_POST["user_name"]))
		{
			echo json_encode($data);
			return;
		}

		#Validate nickname
		$test = users_model::where("nickname", $_POST["nickname"])
			->where("user_id", "!=", $_POST["user_id"])->get();
		if(!empty($test->getUser_id()))
		{
			$data["title"] = "Error";
			$data["message"] = _("The nickname already exists!");
			$data["theme"] = "red";
			echo json_encode($data);
			return;
		}

		$time = Date("Y-m-d H:i:s");
		$user_id = 0;
		$user = users_model::find($_POST["user_id"])
			->set(Array(
				"user_name" => $_POST["user_name"],
				"nickname" => $_POST["nickname"],
				"entity_id" => $this->entity_id
			));
		if(!empty($_POST["password"]))
		{
			$user->setPassword(md5($_POST["password"]));
		}
		if(empty($user->getPassword()))
		{
			$user->setPassword("");
		}
		$user->save();

		$user_id = $user->getUser_id();
		if($user_id != Session::get("user_id"))
		{
			#Module access
			user_modules_model::where("user_id", $user_id)->update(Array("status" => 0));
			foreach($_POST["modules"] as $module_id)
			{
				user_modules_model::where("user_id", $user_id)
					->where("module_id", $module_id)
					->where("status", ">=", 0)
					->get()->set(Array(
						"module_id" => $module_id,
						"user_id" => $user_id,
						"access_type" => 1,
						"status" => 1
					))->save();
			}

			#Method access
			user_methods_model::where("user_id", $user_id)->update(Array("status" => 0));
			foreach($_POST["methods"] as $method_id)
			{
				user_methods_model::where("user_id", $user_id)
					->where("method_id", $method_id)
					->where("status", ">=", 0)
					->get()->set(Array(
						"method_id" => $method_id,
						"user_id" => $user_id,
						"access_type" => 1,
						"status" => 1
					))->save();
			}
		}
		$data["success"] = true;
		$data["title"] = _("Success");
		$data["message"] = _("Changes have been saved");
		$data["theme"] = "green";
		$data["reload_after"] = true;
		echo json_encode($data);
	}

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
			$data["update"] = entities_model::find($this->entity_id)->toArray();
		}
		if($_POST["method"] == "EditUser")
		{
			$data["update"] = users_model::find($_POST["id"])->toArray();
			$data["check"] = Array();
			$data["check"]["modules"] = user_modules_model::select("module_id AS id")
				->where("user_id", $_POST["id"])->getAll();
			$data["check"]["methods"] = user_methods_model::select("method_id AS id")
				->where("user_id", $_POST["id"])->getAll();
		}
		if($_POST["method"] == "Preferences")
		{
			$options = entity_options_model::join("app_options", "option_id")->getAll();
			$data["update"] = Array();
			foreach($options as $option)
			{
				$data["update"][$option["option_key"]] = $option["option_value"];
			}
		}
		echo json_encode($data);
	}

	public function save_entity()
	{
		$this->session_required("json");
		$data = Array("success" => false);
		if(!empty($_POST["entity_name"]))
		{
			$time = Date("Y-m-d H:i:s");
			$entity = entities_model::find($this->entity_id);
			$entity->set(Array(
				"entity_name" => $_POST["entity_name"],
				"entity_slogan" => $_POST["entity_slogan"],
				"edition_user" => Session::get("user_id"),
				"user_edition_time" => $time
			))->save();
			Session::set("entity", $entity->toArray());
			$data["success"] = true;
			$data["title"] = _("Success");
			$data["message"] = _("Changes have been saved");
			$data["theme"] = "green";
			$data["no_reset"] = true;

			#Save image
			if(!empty($_FILES["images"]["name"][0]))
			{
				$extension = strtolower(pathinfo($_FILES["images"]["name"][0], PATHINFO_EXTENSION));
				if($_SERVER["SERVER_NAME"] == $_SERVER["SERVER_ADDR"])
				{
					$dir = "entities/local/";
				}
				else
				{
					$dir = "entities/" . $this->entity_subdomain . "/";
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
		}
		echo json_encode($data);
	}

	public function delete_user()
	{
		$this->session_required("json");
		$data = Array("deleted" => false);
		if(empty($_POST["id"]))
		{
			echo json_encode($data);
			return;
		}
		$user = users_model::find($_POST["id"]);
		$user->setNickname(null);
		$affected = $user->delete();
		$data["deleted"] = $affected > 0;
		echo json_encode($data);
	}

	public function About()
	{
		$this->session_required("html", $this->module);
		$this->view->data["title"] = _("About BlackPHP");
		$this->view->standard_details();
		$this->view->data["nav"] = $this->view->render("nav", true);
		$this->view->data["content_id"] = "info_details";
		$this->view->data["content"] = $this->view->render("content_loader", true);
		$this->view->render('main');
	}

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
			$this->view->data["title"] = _("About BlackPHP");
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

	/**
	 * Guardar preferencias
	 * 
	 * Guarda las preferencias del usuario con los datros recibidos del formulario de preferencias.
	 * 
	 * @return void
	 */
	public function save_preferences()
	{
		$this->session_required("json");
		$data = $_POST;
		$data["success"] = false;
		entity_options_model::where("option_id IN (SELECT option_id FROM app_options WHERE option_type = 1)")->update(Array("option_value" => 0));
		foreach($_POST as $key => $value)
		{
			$option = app_options_model::where("option_key", $key)->get();
			$entity_option = $option->entity_options()->get();
			$entity_option->setOption_value($value);
			$entity_option->save();
		}
		$data["success"] = true;
		$data["title"] = _("Success");
		$data["message"] = _("Changes have been saved");
		$data["theme"] = "green";
		$data["no_reset"] = true;
		echo json_encode($data);
	}

	public function UserDetails()
	{
		$this->session_required("html", $this->module);
		$this->view->data["title"] = _("User details");
		$this->view->standard_details();
		$this->view->data["system_short_date"] = Date("d/m/Y");
		$this->view->data["nav"] = $this->view->render("nav", true);
		$this->view->data["content_id"] = "user_details";
		$this->view->data["content"] = $this->view->render("content_loader", true);
		$this->view->render('main');
	}

	public function user_details_loader()
	{
		$this->session_required("internal");
		$id = $_POST["id"];
		$user = users_model::find($id)->toArray();
		$this->view->data = array_merge($this->view->data, $user);
		$sessions = user_sessions_model::join("browsers", "browser_id")->where("user_sessions.user_id", $id)->orderBy("date_time", "DESC")->addCounter("item")->get(10);
		foreach($sessions as $key => $session)
		{
			$time = strtotime($session["date_time"]);
			$sessions[$key]["session_date"] = Date("d/m/Y", $time);
			$sessions[$key]["session_time"] = Date("h:i a", $time);
		}
		$this->view->data["sessions"] = $sessions;

		$modules = available_modules_model::where("user_id", $id)->getAllArray();
		$i = -1;
		$j = 0;
		$modules_table = Array();
		foreach($modules as $k => $module)
		{
			if($k % 4 == 0)
			{
				$i++;
				$modules_table[$i] = Array();
				$j = 0;
			}
			$modules_table[$i]["module_" . $j] = $module["module_name"];
			$j++;
			$k++;
		}
		while($j % 4 != 0)
		{
			$modules_table[$i]["module_" . $j] = "";
			$j++;
		}
		$this->view->data["user_modules"] = $modules_table;
		#User photo
		$photo = glob("entities/" . $this->entity_id . "/users/profile_" . $user["user_id"] . ".*");
		if(count($photo) > 0)
		{
			$this->view->data["user_photo"] = $photo[0];
		}
		else
		{
			$this->view->data["user_photo"] = "public/images/user.png";
		}
		$this->user_actions($user);
		if($id == Session::get("user_id"))
		{
			$this->view->restrict[] = "no_self";
		}
		$this->view->data["print_title"] = _("User details");
		$this->view->data["print_header"] = $this->view->render("print_header", true);
		$this->view->render("settings/user_details");
	}
}
?>
