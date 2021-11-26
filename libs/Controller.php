<?php
class Controller
{
	function __construct()
	{
		# Initialize the view
		$this->view = new View();
		$this->view->data["base_href"] = "/";

		# Default time zone of servers is Lithuania/Europe, UTC+00; America/El_Salvador is UTC-06
		date_default_timezone_set('America/El_Salvador');

		# In Windows, this line remove the warnings from the client side
		error_reporting(E_ERROR | E_PARSE);

		# Check if an user session is open
		if(Session::get("user_id") != null)
		{
			$this->view->data["user_name"] = Session::get("user_name");
			$this->view->data["nickname"] = Session::get("nickname");
			$this->view->data["user_photo"] = "public/images/user.png";
		}
		else
		{
			# If there is not open session, hide the user button
			$this->view->restrict = array("user");
		}

		# Entity is the enterprise, bussines or organization that use the system
		# The online version works with subdomain names as entity
		$entity = Array();
		if(Session::get("entity") == null)
		{
			$this->loadModel("entity");

			# If SERVER_NAME == IP address (SERVER_ADDR), then get the first entity from database
			if($_SERVER["SERVER_NAME"] == $_SERVER["SERVER_ADDR"])
			{
				# Get the first entity
				$entity = $this->model->get_entity();
			}
			else
			{
				# Get the entity by subdomain
				$server_name = explode(".", $_SERVER["SERVER_NAME"]);
				$subdomain = $server_name[0];
				if($subdomain != "installer")
				{
					$entity = $this->model->get_entity_by_subdomain($subdomain);
					if(!isset($entity["sys_name"]))
					{
						$protocol = "http";
						if( (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443 ){
							$protocol .= "s";
						}
						header("Location: " . $protocol . "://installer." . $server_name[1] . "." . $server_name[2] . "/Instalacion/NuevaEntidad/" . $subdomain . "/");
						return;
					}
				}
			}
			Session::set("entity", $entity);
		}
		else
		{
			$entity = Session::get("entity");
		}
		$this->view->data["modules"] = Session::get("modules");

		# Each entity has a folder on the entities/ location
		# default is used in local installations
		if(!empty($entity["entity_subdomain"]))
		{
			$this->view->data["entity_dir"] = "entities/" . $entity["entity_subdomain"] . "/";
		}
		else
		{
			$this->view->data["comp_dir"] = "entities/local/";
		}
		$this->view->data["entity_logo"] = glob($this->view->data["entity_dir"] . "logo.*")[0];

		# Entity vars are always available in the views
		foreach($entity as $key => $item)
		{
			$this->view->data[$key] = $item;
		}

		#Default theme
		if(Session::get("theme") == null)
		{
			$theme = $this->model->get_first_theme();
			Session::set("theme_id", $theme["theme_id"]);
			Session::set("theme_url", $theme["theme_url"]);
			$this->view->data["theme_id"] = $theme["theme_id"];
			$this->view->data["theme_url"] = $theme["theme_url"];
		}

		#Restrictions
		if($company["barcode"] == 0)
		{
			$this->view->restrict[] = "company:barcode";
		}
	}

	/**
	 * @param string $name Name of the model
	 * @param string $path Location of the models
	 * @param boolean default_model If true, asign the object on $this->model, else return the object
	 */
	public function loadModel($name, $modelPath = 'models/', $default_model = true)
	{
		$path = $modelPath . $name.'_model.php';
		if (file_exists($path)) {
			require_once $path;
			$modelName = $name . '_Model';
			if($default_model)
			{
				$this->model = new $modelName();
			}
			else
			{
				return new $modelName();
			}
		}
	}

	/**
	* Edited By: Edwin Fajardo
	* Session requirements foreach method
	* @param string $type Type of response
	*/
	public function session_required($type = 'html', $module = "")
	{
		if(Session::get("user_id") != null)
		{
			if(!empty($module))
			{
				$this->loadModel("user");
				$perms = $this->model->get_permissions(Session::get("user_id"), $module);
				if(!isset($perms["access_type"]))
				{
					if($type == 'json')
					{
						$data = Array(
							"success" => false,
							"error" => true,
							"message" => "No tiene permisos para realizar esta operación",
							"title" => "Error",
							"theme" => "red"
						);
						echo json_encode($data);
					}
					else
					{
						$this->view->data["title"] = 'Entrar';
						$this->view->add("styles", "css", Array(
							'external/css/jAlert.css',
							'styles/preloader.css',
							'styles/main.css',
							'styles/loading.css'
							));
						$this->view->add("scripts", "js", Array(
							'external/js/jquery-3.2.1.min.js',
							'external/js/jAlert.min.js',
							'scripts/main.js'
							));
							$this->view->data["nav"] = $this->view->render("nav", true);
							$this->view->data["content"] = $this->view->render("forbidden", true);
						$this->view->render('main');
					}
					exit();
				}
			}
			return;
		}
		if($type == 'json')
		{
			$data = Array(
				"success" => false,
				"error" => true,
				"message" => "No ha iniciado sesión",
				"title" => "Error",
				"theme" => "red"
			);
			echo json_encode($data);
		}
		elseif($type == 'internal')
		{
			$this->view->render('error');
		}
		else
		{
			$this->view->data["title"] = 'Entrar';
			$this->view->add("styles", "css", Array(
				'external/css/jquery-ui.min.css',
				'external/css/jAlert.css',
				'external/css/select2.css',
				'styles/main.css',
				'styles/loading.css',
				'styles/forms.css'
				));
			$this->view->add("scripts", "js", Array(
				'external/js/jquery-3.2.1.min.js',
				'external/js/jquery-ui.min.js',
				'external/js/jAlert.min.js',
				'external/js/select2.min.js',
				'scripts/main.js',
				'scripts/forms.js',
				'scripts/lists.js'
				));
			$this->view->data["nav"] = "";
			$this->view->data["content"] = $this->view->render("login", true);
			$this->view->render('main');
		}
		exit();
	}

	public function getRealIP()
	{
		if (!empty($_SERVER['HTTP_CLIENT_IP']))
			return $_SERVER['HTTP_CLIENT_IP'];
		if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
			return $_SERVER['HTTP_X_FORWARDED_FOR'];
		return $_SERVER['REMOTE_ADDR'];
	}

	/**
	 * user_actions
	 * @version 1.0.9 or higher
	 * @author Edwin Fajardo <contacto@edwinfajardo.com>
	 * Date-time: 2021-11-18 09:32
	 */
	public function user_actions($element)
	{
		$user_model = $this->loadModel("user", "models/", false);
		if($element["creation_user"] != 0)
		{
			$creator = $user_model->get_user($element["creation_user"]);
			$this->view->data["cr_user_name"] = $creator["user_name"];
			$this->view->data["cr_time"] = date_utilities::sql_date_to_string($element["creation_time"], true);
		}
		else
		{
			$this->view->restrict[] = "created";
		}
		if($element["edition_user"] != 0 && $element["edition_time"] != $element["creation_time"])
		{
			$editor = $user_model->get_user($element["edition_user"]);
			$this->view->data["ed_user_name"] = $editor["user_name"];
			$this->view->data["ed_time"] = date_utilities::sql_date_to_string($element["edition_time"], true);
		}
		else
		{
			$this->view->restrict[] = "edited";
		}
	}
}
?>
