<?php
/**
 * Controller
 * 
 * Este fichero contiene la clase Controller de la que derivan todos los controladores.
 */

/**
 * Clase Controller
 * 
 * Clase principal de la que derivan todos los controladores.
 */
class Controller
{
	/**
	 * Constructor de la clase Controller
	 * 
	 * En el presente constructor se realizan los siguientes procedimientos:
	 * 1) Se crea una vista (Objeto de la clase View).
	 * 2) Se establece una zona horaria por defecto.
	 * 3) Se establece un idioma regional por defecto, o se usa el establecido en la sesión
	 * 4) Se define error_reporting a E_ERROR | E_PARSE; esto para evitar las advertencias de PHP
	 * cuando se tiene en producción sobre Windows.
	 * 5) Se verifica la sesión del usuario, y se envían los datos a la vista.
	 * 6) Se elige la entidad, y se establece el directorio, el logo y las restricciones de dicha entidad.
	 * 7) Se establece un tema por defecto
	 */
	function __construct()
	{
		#1 Creación de la vista
		$this->view = new View();
		$this->view->data["base_href"] = "/";

		#2 Zona horaria por defecto
		date_default_timezone_set('America/El_Salvador');

		#3 Idioma regional (Por defecto = en_US)
		$locale = "en_US";
		$lang = "en";
		if(empty(Session::get("locale")))
		{
			if(isset($_SERVER["HTTP_ACCEPT_LANGUAGE"]))
			{
				$browser_language = substr($_SERVER["HTTP_ACCEPT_LANGUAGE"], 0, 2);
				if($browser_language == "es")
				{
					$locale = "es_ES";
					$lang = "es";
				}
				Session::set("locale", $locale);
				Session::set("lang", $lang);
			}
		}
		else
		{
			$locale = Session::get("locale");
			$lang = Session::get("lang");
		}
		$charset = empty(Session::get("charset")) ? "UTF-8" : Session::get("charset");
		$this->view->data["lang"] = $lang;

		if (defined('LC_MESSAGES'))
		{
			putenv("LANGUAGE=$locale.$charset");
			setlocale(LC_MESSAGES, $locale . "." . $charset); // Linux
			bindtextdomain("messages", "locale/");
		}
		else
		{
			putenv("LC_ALL={$locale}"); // windows
			bindtextdomain("messages", ".\locale");
		}
		bind_textdomain_codeset("messages", 'UTF-8');
		textdomain("messages");

		#4 Error reporting
		error_reporting(E_ERROR | E_PARSE);

		#5 Verificación de usuario
		if(Session::get("user_id") != null)
		{
			$this->view->data["user_name"] = Session::get("user_name");
			$this->view->data["nickname"] = Session::get("nickname");
			$this->view->data["user_photo"] = "public/images/user.png";
		}
		else
		{
			$this->view->restrict = array("user");
		}

		#6 Entidad
		$entity = Array();
		$options = Array();
		if(Session::get("entity") == null)
		{
			# If SERVER_NAME == IP address (SERVER_ADDR), then get the first entity from database
			if($this->is_ip_address($_SERVER["SERVER_NAME"]))
			{
				# Primera entidad de la tabla
				$entity = entities_model::first()->toArray();
			}
			else
			{
				# Subdominio de la entidad
				$server_name = explode(".", $_SERVER["SERVER_NAME"]);
				$subdomain = $server_name[0];
				if($subdomain != "installer")
				{
					$entity = entities_model::where("entity_subdomain", $subdomain)->get()->toArray();
					if(!isset($entity["entity_id"]))
					{
						$protocol = "http";
						if( (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443 ){
							$protocol .= "s";
						}
						header("Location: " . $protocol . "://installer." . $server_name[1] . "." . $server_name[2] . "/Installation/NewEntity/" . $subdomain . "/");
						return;
					}
				}
			}
			Session::set("entity", $entity);
			$options = entity_options_model::select("option_key", "option_value")->join("app_options", "option_id")->where("entity_id", $entity["entity_id"])->where("option_type", 1)->getAll();
			Session::set("options", $options);
		}
		else
		{
			$entity = Session::get("entity");
			$options = Session::get("options");
		}
		$this->entity_id = $entity["entity_id"];
		$this->entity_subdomain = $entity["entity_subdomain"];
		$this->entity_name = $entity["entity_name"];
		$this->view->data["modules"] = Session::get("modules");

		#Directorio y logo
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

		#Restricciones
		foreach($options as $option)
		{
			if($option["option_value"] == 0)
			{
				$this->view->restrict[] = "entity:" . $option["option_key"];
			}
		}

		#7 Tema por defecto
		if(Session::get("theme_id") == null)
		{
			$theme = app_themes_model::first();
			Session::set("theme_id", $theme->getTheme_id());
			Session::set("theme_url", $theme->getTheme_url());
			$this->view->data["theme_id"] = $theme->getTheme_id();
			$this->view->data["theme_url"] = $theme->getTheme_url();
		}
	}

	/**
	 * Cargar el modelo
	 * 
	 * @param string $name Nombre del modelo
	 * @param string $path Ruta donde se encuentra el modelo, por defecto models/
	 * @param boolean $default_model Si es verdadero, asigna el objeto a $this->model, sino, retorna el objeto
	 * 
	 * @return object|void Un objeto de tipo Model, o void
	 */
	public function loadModel($name, $modelPath = 'models/', $default_model = true)
	{
		$path = $modelPath . $name.'_model.php';
		if (file_exists($path))
		{
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
	 * Validación de sesión
	 * 
	 * Verifica si hay una sesión abierta para el usuario de forma general, o para el módulo
	 * especificado.
	 * 
	 * @param string $type Tipo de respuesta esperada (html, internal o JSON)
	 * @param string $module el módulo al que el usuario intenta acceder
	 */
	public function session_required($type = 'html', $module = "")
	{
		if(Session::get("user_id") != null)
		{
			if(!empty($module))
			{
				$module =app_modules_model::where("module_url", $module)->get();
				$perms = user_modules_model::where("module_id", $module->getModule_id())->where("user_id", Session::get("user_id"))->get();
				if(empty($perms->getUmodule_id()))
				{
					if($type == 'json')
					{
						$data = Array(
							"success" => false,
							"error" => true,
							"message" => _("You do not have permissions to perform this operation"),
							"title" => "Error",
							"theme" => "red"
						);
						echo json_encode($data);
					}
					else
					{
						$this->view->data["title"] = _("Not authorized");
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
				"message" => _("You are not logged in"),
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
			$this->view->data["title"] = _("Log in");
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

	/**
	 * Obtener IP
	 * 
	 * Ontiene la dirección IP del cliente desde la primera variable $_SERVER disponible
	 * 
	 * @return string IP del cliente
	 */
	public function getRealIP()
	{
		if (!empty($_SERVER['HTTP_CLIENT_IP']))
			return $_SERVER['HTTP_CLIENT_IP'];
		if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
			return $_SERVER['HTTP_X_FORWARDED_FOR'];
		return $_SERVER['REMOTE_ADDR'];
	}

	/**
	 * Acciones de usuario
	 * 
	 * Agrega información acerca de acciones de usuario para un objeto en particular,
	 * tales como el usuario y fecha de creación y el usuario y fecha de última edición
	 * Date-time: 2021-11-18 09:32
	 * 
	 * @param array $element Un array asociativo que contiene los campos
	 * creation_user, creation_time, edition_user y edition_time
	 * 
	 * @return void Realiza los cambios directamente en la vista
	 */
	public function user_actions($element)
	{
		if($element["creation_user"] != 0)
		{
			$creator = users_model::find($element["creation_user"]);
			$this->view->data["cr_user_name"] = $creator->getUser_name();
			$this->view->data["cr_time"] = date_utilities::sql_date_to_string($element["creation_time"], true);
		}
		else
		{
			$this->view->restrict[] = "created";
		}
		if($element["edition_user"] != 0 && $element["edition_time"] != $element["creation_time"])
		{
			$editor = users_model::find($element["edition_user"]);
			$this->view->data["ed_user_name"] = $editor->getUser_name();
			$this->view->data["ed_time"] = date_utilities::sql_date_to_string($element["edition_time"], true);
		}
		else
		{
			$this->view->restrict[] = "edited";
		}
	}

	/**
	 * Registro de actividades del usuario
	 * 
	 * Registra, en la base de datos, cada una de las actividades del usuario previamente definidas,
	 * a fin de poder consultar en el futuro el historial de actividades.
	 * Incorporado el: 2021-12-05 16:44
	 * 
	 * @param string $action_key Clave de actividad realizada
	 * @param string $element_key Clave del elemento sobre el cual se realiza la actividad
	 * @param int $element_link Identificador del elemento
	 * @param string $date_time Fecha y hora en formato ISO
	 * 
	 * @return void No se devuelven valores
	 */
	public function set_user_log($action_key, $element_key, $element_link = 0, $date_time = "")
	{
		if(empty($date_time))
		{
			$date_time = Date("Y-m-d H:i:s");
		}
		$action = app_actions_model::where("action_key", $action_key)->get();
		$element = app_elements_model::where("element_key", $element_key)->get();
		if($action->getAction_id() == null || $element->getElement_id() == null)
		{
			return;
		}
		$user_log = new user_logs_model();
		$user_log->set(Array(
			"user_id" => Session::get("user_id"),
			"element_id" => $element->getElement_id(),
			"action_id" => $action->getAction_id(),
			"date_time" => $date_time,
			"element_link" => $element_link
		))->save();
	}

	/**
	 * Is IP Address
	 * Verifica si SERVER_NAME es una dirección IP
	 * 
	 * @param string $str SERVER_NAME pasado por parámetro
	 * 
	 * @return boolean Verdadero si es una IP, falso en caso contrario.
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
