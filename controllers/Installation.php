<?php

#	Installation controller
#	By: Edwin Fajardo
#	Date-time: 2020-06-18 23:46

class Installation extends Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->module = get_class($this);
		$this->view->data["module"] = $this->module;
	}

	public function index($subdomain = "")
	{
		#$this->session_required("html", $this->module);
		$this->view->data["title"] = _("Installation");
		$this->view->standard_form();
		$this->view->data["nav"] = "";
		if(Session::get("authorization_code") != null)
		{
			$this->loadModel("entity");
			$modules = $this->model->get_all_modules();
			$this->view->data["modules"] = "";
			foreach($modules as $module)
			{
				foreach($module as $key => $item)
				{
					$this->view->data[$key] = $item;
				}
				$this->view->data["methods"] = $this->model->get_methods_by_module($module["module_id"]);
				$this->view->data["modules"] .= $this->view->render("modules", true);
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
			$this->view->data["content"] = $this->view->render("installation/install", true);
		}
		else
		{
			$this->view->data["content"] = $this->view->render("installation/install_login", true);
		}
		$this->view->render('main');
	}

	public function NewEntity($subdomain)
	{
		$this->index($subdomain);
	}

	public function load_form_data()
	{
		$data = Array();
		$this->loadModel("entity");
		$entity_id = $this->entity_id;
		if($entity_id != null)
		{
			$data["update"] = $this->model->get_entity_to_update($this->entity_id);
			$data["check"] = Array(
				"modules" => $this->model->get_entity_modules($this->entity_id),
				"methods" => $this->model->get_all_entity_methods($this->entity_id)
			);
		}
		echo json_encode($data);
	}

	public function save_installation()
	{
		$data = $_POST;
		$data["success"] = false;
		$now = Date("Y-m-d H:i:s");
		$today = Date("Y-m-d");
		$this->loadModel("entity");
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
					echo json_encode($data);
					return;
				}
			}
			$entity = $this->model->get_entity_by_subdomain($data["subdomain"]);
			if(isset($entity["entity_id"]) || in_array($data["subdomain"], $reserved_subdomains))
			{
				$data["title"] = "Error";
				$data["message"] = _("The subdomain") . " " . $data["subdomain"] . " " . _("is no available");
				$data["theme"] = "red";
				echo json_encode($data);
				return;
			}
			#Save entity
			$data_set = Array(
				"entity_name" => $data["entity_name"],
				"entity_slogan" => $data["entity_slogan"],
				"entity_date" => $today,
				"entity_begin" => $today,
				"entity_subdomain" => $data["subdomain"],
				"sys_name" => $data["sys_name"],
				"creation_installer" => Session::get("installer_id"),
				"creation_time" => $now,
				"edition_installer" => Session::get("installer_id"),
				"installer_edition_time" => $now
			);
			$entity = $this->model->set_entity($data_set);
			$entity_id = isset($entity["id"]) ? $entity["id"] : null;
		}
		else
		{
			#Update entity
			$data_set = Array(
				"entity_id" => $data["entity_id"],
				"entity_name" => $data["entity_name"],
				"entity_slogan" => $data["entity_slogan"],
				"sys_name" => $data["sys_name"],
				"edition_user" => Session::get("user_id") == null ? 0 : Session::get("user_id"),
				"user_edition_time" => $now
			);
			$this->model->update_entity($data_set);
		}
		if(empty($entity_id))
		{
			$data["title"] = "Error";
			$data["message"] = _("Failed to create the entity");
			$data["theme"] = "red";
			echo json_encode($data);
			return;
		}

		#Default branch
		$entity = $this->model->get_entity_by_id($entity_id);

		#Set modules
		$i = 0;
		$this->model->revoke_entity_modules($entity_id);
		foreach($data["modules"] as $module_id)
		{
			$i++;
			$module = $this->model->get_entity_module($entity_id, $module_id);
			if(!isset($module["cmodule_id"]))
			{
				$data_set = Array(
					"entity_id" => $entity_id,
					"module_id" => $module_id,
					"module_order" => $i,
					"creation_time" => $now,
					"edition_time" => $now
				);
				$this->model->set_entity_module($data_set);
			}
			else
			{
				$data_set = Array(
					"entity_id" => $entity_id,
					"module_id" => $module_id,
					"module_order" => $i,
					"edition_time" => $now,
					"status" => 1
				);
				$this->model->update_entity_module($data_set);
			}
		}

		#Set methods
		$this->model->revoke_entity_methods($entity_id);
		$i = 0;
		foreach($data["methods"] as $method_id)
		{
			$i++;
			$method = $this->model->get_entity_method($entity_id, $method_id);
			if(!isset($method["cmet_id"]))
			{
				$data_set = Array(
					"entity_id" => $entity_id,
					"method_id" => $method_id,
					"method_order" => $i,
					"creation_time" => $now,
					"edition_time" => $now
				);
				$this->model->set_entity_method($data_set);
			}
			else
			{
				$data_set = Array(
					"entity_id" => $entity_id,
					"method_id" => $method_id,
					"method_order" => $i,
					"edition_time" => $now,
					"status" => 1
				);
				$this->model->update_entity_method($data_set);
			}
		}

		#Save default user
		$user_model = $this->loadModel("user", "models/", false);
		$user_id = null;
		$data_set = Array(
			"entity_id" => $entity_id,
			"user_name" => $data["user_name"],
			"nickname" => $data["nickname"],
			"theme_id" => 1,
			"edition_user" => 0,
			"edition_time" => $now
		);
		if(!empty($data["password"]))
		{
			$data_set["password"] = md5($data["password"]);
		}
		$user_id = $data["admin_user"];
		if($user_id == null)
		{
			$data_set["creation_user"] = 0;
			$data_set["creation_time"] = $now;
			$user = $user_model->set_user($data_set);
			$user_id = $user["id"];
	
			$data_set = Array(
				"entity_id" => $entity_id,
				"admin_user" => $user_id
			);
			$this->model->update_entity($data_set);
		}
		else
		{
			#Update user
			$data_set["user_id"] = $user_id;
			$user_model->update_user($data_set);
		}

		#Set user modules permissions
		$data_set = Array(
			"user_id" => $user_id,
			"edition_user" => Session::get("user_id") == null ? 0 : Session::get("user_id"),
			"edition_time" => $now,
			"status" => 0
		);
		if($user_id != Session::get("user_id"))
		{
			$user_model->revoke_permissions($data_set);
			foreach($data["modules"] as $module_id)
			{
				$access = $user_model->get_module_access($user_id, $module_id);
				if(isset($access["user_id"]))
				{
					$data_set = Array(
						"module_id" => $module_id,
						"user_id" => $user_id,
						"access_type" => 1,
						"edition_user" => 0,
						"edition_time" => $now,
						"status" => 1
					);
					$user_model->update_access($data_set);
				}
				else
				{
					$data_set = Array(
						"module_id" => $module_id,
						"user_id" => $user_id,
						"access_type" => 1,
						"creation_user" => 0,
						"creation_time" => $now,
						"edition_user" => 0,
						"edition_time" => $now
					);
					$user_model->set_access($data_set);
				}
			}
		}

		#Logo
		if(!empty($_FILES["images"]["name"]))
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
			$data["redirect_after"] = $protocol . "://" . str_replace("installer", $data["subdomain"], $_SERVER["SERVER_NAME"]);
		}
		else
		{
			$data["reload_after"] = true;
		}
		#Close installer session
		Session::destroy();
		header('Content-type: application/json');
		echo json_encode($data);
	}

	public function test_authorization()
	{
		$data = $_POST;
		$data["success"] = false;
		if(empty($data["nickname"]) || empty($data["password"]))
		{
			$data["title"] = "Error";
			$data["message"] = _("Enter your installer user and password");
			$data["theme"] = "red";
			echo json_encode($data);
			return;
		}
		$this->loadModel("installer");
		$installer = $this->model->get_access($data["nickname"], $data["password"]);
		if(isset($installer["installer_id"]))
		{
			Session::set("authorization_code", true);
			Session::set("installer_id", $installer["installer_id"]);
			$data["reload"] = true;
		}
		else
		{
			$data["title"] = "Error";
			$data["message"] = _("Bad user or password");
			$data["theme"] = "red";
			$data["no_reset"] = true;
		}
		header('Content-type: application/json');
		echo json_encode($data);
	}

	function get_started() 
	{
		$this->view->data["title"] = _("Start");
		$this->view->standard_error();
		$this->view->data["nav"] = "";
		$this->view->data["content"] = $this->view->render("installation/install_missing_conf", true);
		$this->view->render('main');
	}
}
?>
