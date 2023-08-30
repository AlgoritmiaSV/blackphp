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
	protected $view;
	protected $system_name;
	function __construct()
	{
		#1 Creación de la vista
		$this->view = new View();
		$this->view->data["base_href"] = "/";
		$system = Session::get("system");
		if(empty($system))
		{
			if(file_exists("app_info.json"))
			{
				$system = json_decode(file_get_contents("app_info.json") ,true);
			}
			else
			{
				$system = Array("system_name" => "BlackPHP");
			}
			Session::set("system", $system);
		}
		$this->view->data["system_name"] = $system["system_name"];
		if(isset($system["copyright"]))
		{
			foreach($system["copyright"] as $key => $value)
			{
				$this->view->data["copyright_" . $key] = $value;
			}
		}
		$this->system_name = $system["system_name"];

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
		$charset = Session::get("charset") ?? "UTF-8";
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
		elseif(Session::get("installer_id") == null)
		{
			$this->view->restrict = array("user");
		}
		else
		{
			$this->view->data["user_photo"] = "public/images/user.png";
		}

		# Sistema en mantenimiento
		if(defined('SYSTEM_STATUS') && SYSTEM_STATUS == 'MAINTENANCE')
		{
			$this->maintenance();
		}
		
		#6 Entidad
		$entity = Array();
		$options = Array();
		if(Session::get("entity") == null)
		{
			# If SERVER_NAME == IP address (SERVER_ADDR), then get the first entity from database
			if($this->isIpAddress($_SERVER["SERVER_NAME"]))
			{
				# Primera entidad de la tabla
				$entity = entitiesModel::first()->toArray();
			}
			else
			{
				# Subdominio de la entidad
				$server_name = explode(".", $_SERVER["SERVER_NAME"]);
				$subdomain = $server_name[0];
				if($subdomain != "installer")
				{
					$entity = entitiesModel::findBy("entity_subdomain", $subdomain)->toArray();
					if(!isset($entity["entity_id"]))
					{
						$protocol = "http";
						if( (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443 ){
							$protocol .= "s";
						}
						$installer_url = $protocol . "://installer";
						for($i = 1; $i < count($server_name); $i++)
						{
							$installer_url .= ("." . $server_name[$i]);
						}
						header("Location: " . $installer_url . "/Installation/NewEntity/" . $subdomain . "/");
						return;
					}
				}
			}

			# Moneda
			if(!empty($entity["country_iso"]))
			{
				$currency = appCurrenciesModel::join("app_countries", "currency_iso")->where("country_iso", $entity["country_iso"])->get();
				$entity["currency_symbol"] = $currency["symbol"];
			}
			else
			{
				$entity["currency_symbol"] = "$";
			}

			# Municipio y departamento
			if(!empty($entity["city_id"]))
			{
				$city = appCitiesModel::select("city_name, department_name")->join("app_departments", "department_id")->where("city_id", $entity["city_id"])->get();
				$entity = array_merge($entity, $city);
			}

			Session::set("entity", $entity);
			
			$option_list = entityOptionsModel::select("option_key", "option_value")->join("app_options", "option_id")->getAll();
			$options = Array();
			foreach($option_list as $item)
			{
				$options[$item["option_key"]] = $item["option_value"];
			}
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
		$logo = glob($this->store_dir . "logo.*")[0];
		if(empty($logo))
		{
			$logo = "public/images/default_image.png";
		}
		$this->view->data["entity_logo"] = $logo . "?t=" . filemtime($logo);

		# Entity vars are always available in the views
		foreach($entity as $key => $item)
		{
			$this->view->data[$key] = $item;
		}

		#Restricciones
		foreach($options as $key => $value)
		{
			if($value == 0)
			{
				$this->view->restrict[] = "entity:" . $key;
			}
		}

		#7 Tema por defecto
		if(Session::get("theme_id") == null)
		{
			$theme = appThemesModel::first();
			Session::set("theme_id", $theme->getThemeId());
			Session::set("theme_url", $theme->getThemeUrl());
			$this->view->data["theme_id"] = $theme->getThemeId();
			$this->view->data["theme_url"] = $theme->getThemeUrl();
		}
	}

	/**
	 * Cargar el modelo
	 * 
	 * Método para cargar modelos de forma manual. En este momento, este método no se está utilizando,
	 * y se eliminará en versiones posteriores.
	 * 
	 * @param string $name Nombre del modelo
	 * @param string $path Ruta donde se encuentra el modelo, por defecto models/
	 * @param boolean $default_model Si es verdadero, asigna el objeto a $this->model, sino, retorna el objeto
	 * 
	 * @return object|void Un objeto de tipo Model, o void
	 */
	protected function loadModel($name, $modelPath = 'models/', $default_model = true)
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
	 * Sistema en mantenimiento
	 * 
	 * Muestra un aviso de sistema en mantenimiento, en caso de que la constante
	 * SYSTEM_STATUS esté configurada como MAINTENANCE.
	 */
	protected function maintenance($type = 'html')
	{
		if($type == 'json')
		{
			$this->json(Array(
				"success" => false,
				"error" => true,
				"message" => _("System under maintenance"),
				"title" => "Error",
				"theme" => "red"
			));
		}
		elseif($type == 'internal')
		{
			$this->view->render("main/maintenance");
		}
		else
		{
			$this->view->data["title"] = _("System under maintenance");
			$this->view->standard_error();
			$this->view->data["nav"] = "";
			$this->view->data["content"] = $this->view->render("main/maintenance", true);
			$this->view->render('clean_main');
		}
		exit();
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
	protected function session_required($type = 'html', $module = "")
	{
		if(Session::get("user_id") != null)
		{
			if(!empty($module))
			{
				$module = appModulesModel::findBy("module_url", $module);
				$perms = userModulesModel::where("module_id", $module->getModuleId())->where("user_id", Session::get("user_id"))->get();
				if(empty($perms->getUmoduleId()))
				{
					if($type == 'json')
					{
						$this->json(Array(
							"success" => false,
							"error" => true,
							"message" => _("You do not have permissions to perform this operation"),
							"title" => "Error",
							"theme" => "red"
						));
					}
					else
					{
						$this->view->data["title"] = _("Not authorized");
						$this->view->standard_error();
						$this->view->data["nav"] = $this->view->render("main/nav", true);
						$this->view->data["content"] = $this->view->render("main/forbidden", true);
						$this->view->render('main');
					}
					exit();
				}
			}
			return;
		}
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
			$this->view->add("styles", "css", Array(
				'styles/login.css'
				));
			$this->view->data["nav"] = "";
			$this->view->data["about"] = sprintf(_("About %s"), $this->system_name);
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
	protected function getRealIP()
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
	protected function userActions($element)
	{
		if(is_object($element))
		{
			$element = $element->toArray();
		}
		if($element["creation_user"] != 0)
		{
			$creator = usersModel::find($element["creation_user"]);
			$this->view->data["cr_user_name"] = $creator->getUserName();
			$this->view->data["cr_time"] = date_utilities::sql_date_to_string($element["creation_time"], true);
		}
		else
		{
			$this->view->restrict[] = "created";
		}
		if($element["edition_user"] != 0 && $element["edition_time"] != $element["creation_time"])
		{
			$editor = usersModel::find($element["edition_user"]);
			$this->view->data["ed_user_name"] = $editor->getUserName();
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
	protected function setUserLog($action_key, $element_key, $element_link = null, $date_time = "")
	{
		if(empty($date_time))
		{
			$date_time = Date("Y-m-d H:i:s");
		}
		$element = appElementsModel::findBy("element_key", $element_key);
		if(!$element->exists())
		{
			return;
		}
		$actions = [
			"create" => 4,
			"update" => 2,
			"delete" => 1
		];
		if(!isset($actions[$action_key]))
		{
			return;
		}
		$user_log = new userLogsModel();
		$user_log->set(Array(
			"user_id" => Session::get("user_id"),
			"element_id" => $element->getElementId(),
			"action_id" => $actions[$action_key],
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
	protected function isIpAddress($str)
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

	/**
	 * Imprimir un array en formato JSON
	 * 
	 * Convierte todos los valores nulos a cadenas vacías, y luego, imprime el resultado en formato
	 * JSON
	 * @param array $data Arreglo de datos a imprimir
	 * 
	 * @return void
	 */
	protected function json($data)
	{
		array_walk_recursive($data, function(&$item)
		{
			$item = $item === null ? "" : $item;
		});
		header('Content-type: application/json');
		echo json_encode($data);
	}

	/**
	 * Comprobar permisos
	 * 
	 * Comprueba si un usuario tiene permisos para el método solicitado
	 */
	protected function check_permissions($action, $element, $response = "default")
	{
		if($response == "default")
		{
			$response = $_SERVER["REQUEST_METHOD"] == "GET" ? "html" : "json";
		}
		$actions = ["read" => 8, "create" => 4, "update" => 2, "delete" => 1];
		$permissions = Session::get("permissions");
		if($permissions == null)
		{
			if($response == 'json')
			{
				$this->json(Array(
					"success" => false,
					"error" => true,
					"message" => _("You are not logged in"),
					"title" => "Error",
					"theme" => "red"
				));
			}
			elseif($response == 'embedded')
			{
				$this->view->render("main/error");
			}
			else
			{
				$this->view->data["title"] = _("Log in");
				$this->view->standard_form();
				$this->view->add("styles", "css", Array(
					'styles/login.css'
					));
				$this->view->data["nav"] = "";
				$this->view->data["about"] = sprintf(_("About %s"), $this->system_name);
				$this->view->data["content"] = $this->view->render("login", true);
				$this->view->render('main');
			}
			exit();
		}
		elseif(!isset($permissions[$element]) || ($actions[$action] & $permissions[$element]) == 0)
		{
			if($response == "json")
			{
				$this->json(Array(
					"success" => false,
					"error" => true,
					"message" => _("You do not have permissions to perform this operation"),
					"title" => "Error",
					"theme" => "red"
				));
			}
			elseif($response == "embedded")
			{
				$this->view->render("main/forbidden");
			}
			else
			{
				$this->view->data["title"] = _("Not authorized");
				$this->view->standard_error();
				$this->view->data["nav"] = $this->view->render("main/nav", true);
				$this->view->data["content"] = $this->view->render("main/forbidden", true);
				if($response == "standalone")
				{
					$this->view->render('clean_main');
				}
				else
				{
					$this->view->render('main');
				}
			}
			exit();
		}
	}

	protected function Building($title = "")
	{
		$this->session_required("html", $this->module);
		$this->view->data["title"] = empty($title) ? _("In construction") : $title;
		$this->view->standard_list();
		$this->view->data["nav"] = $this->view->render("main/nav", true);
		$this->view->data["print_header"] = $this->view->render("print_header", true);
		$this->view->data["content"] = $this->view->render("main/building", true);
		$this->view->render("main");
	}
}
?>
