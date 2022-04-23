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
		$restrictions = Array();
		$this->loadModel("entity");
		if(Session::get("entity") == null)
		{
			# If SERVER_NAME == IP address (SERVER_ADDR), then get the first entity from database
			if($this->is_ip_address($_SERVER["SERVER_NAME"]))
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
					if(!isset($entity["entity_id"]))
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
			$restrictions = $this->model->get_restrictions($entity["entity_id"]);
			Session::set("restrictions", $restrictions);
		}
		else
		{
			$entity = Session::get("entity");
			$restrictions = Session::get("restrictions");
		}
		$this->entity_id = $entity["entity_id"];
		$this->entity_subdomain = $entity["entity_subdomain"];
		$this->entity_name = $entity["entity_name"];
		$this->view->data["modules"] = Session::get("modules");

		# Each entity has a folder on the entities/ location
		# default is used in local installations
		if(!empty($entity["entity_subdomain"]))
		{
			$this->store_dir = "entities/" . $entity["entity_subdomain"] . "/";
		}
		else
		{
			$this->store_dir = "entities/local/";
		}
		$this->view->data["entity_dir"] = $this->store_dir;
		$this->view->data["entity_logo"] = glob($this->store_dir . "logo.*")[0];

		# Entity vars are always available in the views
		foreach($entity as $key => $item)
		{
			$this->view->data[$key] = $item;
		}

		#Default theme
		if(Session::get("theme_id") == null)
		{
			$theme = $this->model->get_first_theme();
			Session::set("theme_id", $theme["theme_id"]);
			Session::set("theme_url", $theme["theme_url"]);
			$this->view->data["theme_id"] = $theme["theme_id"];
			$this->view->data["theme_url"] = $theme["theme_url"];
		}

		#Restrictions
		foreach($restrictions as $key => $restriction)
		{
			if($restriction == 0)
			{
				$this->view->restrict[] = "entity:" . $key;
			}
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
						$this->view->standard_error();
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
			$this->view->standard_form();
			$this->view->add("styles", "css", Array(
				'styles/login.css'
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

	/**
	 * user_actions
	 * @version 1.0.9 or higher
	 * @author Edwin Fajardo <contacto@edwinfajardo.com>
	 * Date-time: 2021-12-05 16:44
	 */
	public function set_user_log($action_key, $element_key, $element_link = 0, $date_time = "")
	{
		if(empty($date_time))
		{
			$date_time = Date("Y-m-d H:i:s");
		}
		$user_model = $this->loadModel("user", "models/", false);
		$action = $user_model->get_action_id($action_key);
		$element = $user_model->get_element_id($element_key);
		if(!isset($action["action_id"]) || !isset($element["element_id"]))
		{
			return;
		}
		$data_set = Array(
			"user_id" => Session::get("user_id"),
			"element_id" => $element["element_id"],
			"action_id" => $action["action_id"],
			"date_time" => $date_time,
			"element_link" => $element_link
		);
		$user_model->set_log($data_set);
	}

	/**
	 * Is IP Address
	 * Check if SERVER_NAME is IP
	 */
	public function is_ip_address($str)
	{
		$octets = explode(".", $str);
		if(count($octets) != 4)
		{
			return false;
		}
		foreach($octets as $octet)
		{
			if(!is_numeric($octet))
			{
				return false;
			}
			$octet = intval($octet);
			if($octet < 0 || $octet > 255)
			{
				return false;
			}
		}
		return true;
	}
}
?>
