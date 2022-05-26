<?php

#	Settings controller
#	By: Edwin Fajardo
#	Date-time: 2020-06-18 23:46

class Settings extends Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->module = get_class($this);
		$this->view->data["module"] = $this->module;
	}

	public function index()
	{
		$this->session_required("html", $this->module);
		$this->view->data["title"] = _("Settings");
		$this->view->standard_menu();
		$this->view->data["nav"] = $this->view->render("nav", true);
		$this->loadModel("entity");
		$module = $this->model->get_module_by_name($this->module);
		$this->view->data["methods"] = $this->model->get_entity_methods($this->entity_id, $module["module_id"]);
		$this->view->data["content"] = $this->view->render("generic_menu", true);
		$this->view->render('main');
	}

	public function Entity()
	{
		$this->session_required("html", $this->module);
		$this->view->data["title"] = _("Entity data");
		$this->view->standard_form();
		$this->view->data["nav"] = $this->view->render("nav", true);
		$this->view->data["content"] = $this->view->render("settings/entity", true);
		$this->view->render('main');
	}

	public function Preferences()
	{
		$this->session_required("html", $this->module);
		$this->view->data["title"] = _("Preferences");
		$this->view->standard_form();
		$this->view->data["nav"] = $this->view->render("nav", true);
		$this->view->data["content"] = $this->view->render("settings/preferences", true);
		$this->view->render('main');
	}

	public function MyAccount()
	{
		$this->session_required("html", $this->module);
		$this->view->data["title"] = _("My account");
		$this->view->standard_form();
		$this->view->data["nav"] = $this->view->render("nav", true);
		$this->view->data["content"] = $this->view->render("settings/my_account", true);
		$this->view->render('main');
	}

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

	public function NewUser()
	{
		$this->session_required("html", $this->module);
		$this->view->data["title"] = _("New user");
		$this->view->standard_form();
		$this->view->data["nav"] = $this->view->render("nav", true);
		$this->view->restrict[] = "edition";
		$this->loadModel("user");
		$this->view->data["content"] = $this->view->render("settings/user_edit", true);
		$this->view->render('main');
	}

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
		else
		{
			$this->loadModel("user");
			$this->view->data["branches"] = $this->model->get_user_branches(Session::get("user_id"));
		}
		$this->view->restrict[] = "creation";
		$this->view->data["content"] = $this->view->render("settings/user_edit", true);
		$this->view->render('main');
	}

	public function users_table_loader($response = "JSON")
	{
		$this->session_required("json");
		$this->loadModel("user");
		$data = Array();
		$users = $this->model->get_all($this->entity_id);
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

	public function save_user()
	{
		$this->session_required("json");
		$data = Array("success" => false);
		if(empty($_POST["user_name"]))
		{
			echo json_encode($data);
			return;
		}
		$time = Date("Y-m-d H:i:s");
		$this->loadModel("user");
		$user_id = 0;
		if(!empty($_POST["user_id"]))
		{
			$data_set = Array(
				"user_id" => $_POST["user_id"],
				"user_name" => $_POST["user_name"],
				"edition_user" => Session::get("user_id"),
				"edition_time" => $time
			);
			if(!empty($_POST["password"]))
			{
				$data_set["password"] = md5($_POST["password"]);
			}
			$data = $this->model->update_user($data_set);
			$user_id = $_POST["user_id"];
		}
		else
		{
			$comp_id = $this->entity_id;
			$nickname = $this->model->get_user_by_nickname($_POST["nickname"], $comp_id);
			if(isset($nickname["user_id"]))
			{
				$data["title"] = "Error";
				$data["message"] = _("The nickname already exists!");
				$data["theme"] = "red";
				echo json_encode($data);
				return;
			}
			$data_set = Array(
				"user_name" => $_POST["user_name"],
				"nickname" => $_POST["nickname"],
				"entity_id" => $comp_id,
				"password" => md5($_POST["password"]),
				"creation_user" => Session::get("user_id"),
				"creation_time" => $time,
				"edition_user" => Session::get("user_id"),
				"edition_time" => $time
			);
			$data = $this->model->set_user($data_set);
			$user_id = $data["id"];
		}

		if($user_id != Session::get("user_id"))
		{
			#Set module access
			$data_set = Array(
				"user_id" => $user_id,
				"edition_user" => Session::get("user_id"),
				"edition_time" => $time,
				"status" => 0
			);
			$this->model->revoke_permissions($data_set);
			foreach($_POST["modules"] as $module_id)
			{
				#$module = $this->model->get_module_by_name($module_name);
				$access = $this->model->get_module_access($user_id, $module_id);
				if(isset($access["user_id"]))
				{
					$data_set = Array(
						"module_id" => $module_id,
						"user_id" => $user_id,
						"access_type" => 1,
						"edition_user" => Session::get("user_id"),
						"edition_time" => $time,
						"status" => 1
					);
					$this->model->update_access($data_set);
				}
				else
				{
					$data_set = Array(
						"module_id" => $module_id,
						"user_id" => $user_id,
						"access_type" => 1,
						"creation_user" => Session::get("user_id"),
						"creation_time" => $time,
						"edition_user" => Session::get("user_id"),
						"edition_time" => $time
					);
					$this->model->set_access($data_set);
				}
			}
		}
		$data["success"] = true;
		$data["title"] = _("Success");
		$data["message"] = _("Changes have been saved");
		$data["theme"] = "green";
		$data["reload_after"] = true;
		echo json_encode($data);
	}

	public function load_form_data()
	{
		$data = Array();
		if($_POST["method"] == "Entity")
		{
			$this->loadModel("entity");
			$data["update"] = $this->model->get_entity_by_id($this->entity_id);
		}
		if($_POST["method"] == "EditUser")
		{
			$this->loadModel("user");
			$data["update"] = $this->model->get_user($_POST["id"]);
			$data["check"] = Array();
			$data["check"]["modules"] = $this->model->get_all_permissions($_POST["id"]);
		}
		if($_POST["method"] == "Preferences")
		{
			$this->loadModel("entity");
			$themes = $this->model->get_themes();
			foreach($themes as $key => $theme)
			{
				$themes[$key]["text"] = _($theme["text"]);
			}
			$data["themes"] = $themes;
			$data["update"] = Array(
				"theme_id" => Session::get("theme_id")
			);
		}
		if($_POST["method"] == "MyAccount")
		{
			$this->loadModel("entity");
			$themes = $this->model->get_themes();
			foreach($themes as $key => $theme)
			{
				$themes[$key]["text"] = _($theme["text"]);
			}
			$data["themes"] = $themes;
			$data["locales"] = Array(
				Array("id" => "en_US", "text" => _("English")),
				Array("id" => "es_ES", "text" => _("Spanish"))
			);
			$data["update"] = Array(
				"theme_id" => Session::get("theme_id"),
				"locale" => Session::get("locale")
			);
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
			$data_set = Array(
				"entity_id" => $this->entity_id,
				"entity_name" => $_POST["entity_name"],
				"entity_slogan" => $_POST["entity_slogan"],
				"edition_user" => Session::get("user_id"),
				"user_edition_time" => $time
			);
			$this->loadModel("entity");
			$data = $this->model->update_entity($data_set);
			Session::set("entity", $this->model->get_entity_by_id($this->entity_id));
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
		$this->loadModel("user");
		$user = $this->model->get_user($_POST["id"]);
		$time = Date("Y-m-d H:i:s");
		$data_set = Array(
			"user_id" => $user["user_id"],
			"nickname" => null,
			"status" => 0,
			"edition_user" => Session::get("user_id"),
			"edition_time" => $time
		);
		$data = $this->model->update_user($data_set);
		$data["deleted"] = $data["affected"] > 0;
		echo json_encode($data);
	}

	public function About()
	{
		$this->session_required("html", $this->module);
		$this->view->data["title"] = _("About BlackPHP");
		$this->view->standard_details();
		$this->view->data["system_short_date"] = Date("d/m/Y");
		$this->view->data["nav"] = $this->view->render("nav", true);
		$this->view->data["content_id"] = "info_details";
		$this->view->data["content"] = $this->view->render("content_loader", true);
		$this->view->render('main');
	}

	public function info_details_loader()
	{
		$this->session_required("internal");
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
		$this->view->render('settings/info_details');
	}

	public function save_preferences()
	{
		$this->session_required("json");
		$data = $_POST;
		$data["success"] = false;
		if($data["theme_id"] != Session::get("theme_id"))
		{
			$this->loadModel("user");
			$user = $this->model->get_user(Session::get("user_id"));
			$user["theme_id"] = $data["theme_id"];
			$this->model->update_user($user);
			$theme = $this->model->get_theme($data["theme_id"]);
			Session::set("theme_id", $theme["theme_id"]);
			Session::set("theme_url", $theme["theme_url"]);
			$data["reload_after"] = true;
		}
		$data["success"] = true;
		$data["title"] = _("Success");
		$data["message"] = _("Changes have been saved");
		$data["theme"] = "green";
		echo json_encode($data);
	}

	public function save_my_account()
	{
		$this->session_required("json");
		$data = $_POST;
		$data["success"] = false;
		$this->loadModel("user");
		$user = $this->model->get_user(Session::get("user_id"));
		if($data["theme_id"] != Session::get("theme_id"))
		{
			$user["theme_id"] = $data["theme_id"];
			$theme = $this->model->get_theme($data["theme_id"]);
			Session::set("theme_id", $theme["theme_id"]);
			Session::set("theme_url", $theme["theme_url"]);
		}
		if($data["locale"] != Session::get("locale"))
		{
			$user["locale"] = $data["locale"];
			Session::set("locale", $data["locale"]);
		}
		$this->model->update_user($user);
		$data["reload_after"] = true;
		$data["success"] = true;
		$data["title"] = _("Success");
		$data["message"] = _("Changes have been saved");
		$data["theme"] = "green";
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
		$this->loadModel("user");
		$id = $_POST["id"];
		$user = $this->model->get_user($id);
		$this->view->data = array_merge($this->view->data, $user);
		$sessions = $this->model->get_sessions_by_user(0, $id);
		$i = 0;
		foreach($sessions as $key => $session)
		{
			$sessions[$key]["item"] = ++$i;
			$time = strtotime($session["date_time"]);
			$sessions[$key]["session_date"] = Date("d/m/Y", $time);
			$sessions[$key]["session_time"] = Date("h:i a", $time);
			$purchases[$key]["amount"] = number_format($purchase["amount"], 2);
		}
		$this->view->data["sessions"] = $sessions;

		$modules = $this->model->get_user_entity_modules($this->entity_id, $id);
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
