<?php
use donatj\UserAgent\UserAgentParser;

/**
 * Controlador de usuarios
 * 
 * Gestiona el acceso del usuario al sistema, y las configuraciones que el usuario puede hacer
 * a su cuenta.
 * 
 * Incorporado el 2020-6-12 23:55
 * @author Edwin Fajardo <contacto@edwinfajardo.com>
 */
class User extends Controller
{
	/**
	 * Constructor de la clase.
	 * 
	 * Inicializa la variable module con el nombre de la clase.
	 */
	public function __construct()
	{
		parent::__construct();
		$this->module = get_class($this);
	}

	/**
	 * Vista principal
	 * 
	 * La vista principal para este módulo no ha sido desarrollada.
	 * 
	 * @todo Desarrollar contenido propio de la vista principal del usuario.
	 * 
	 * @return void
	 */
	public function index()
	{
		header("Location: /" . $this->module . "/MyAccount/");
	}

	/**
	 * Carga de datos de formulario
	 * 
	 * Imprime en formato JSON los datos iniciales de los formularios.
	 * 
	 * @return void
	 */
	public function load_form_data()
	{
		$data = Array();
		if($_POST["method"] == "MyAccount")
		{
			$data["themes"] = appThemesModel::list();
			foreach($data["themes"] as &$theme)
			{
				$theme["text"] = _($theme["text"]);
			}
			unset($theme);
			$data["locales"] = appLocalesModel::list("locale_code", "locale_name");
			foreach($data["locales"] as &$locale)
			{
				$locale["text"] = _($locale["text"]);
			}
			unset($locale);
			$user = usersModel::find(Session::get("user_id"));
			$data["update"] = Array(
				"theme_id" => Session::get("theme_id"),
				"locale" => Session::get("locale"),
				"user_name" => $user->getUserName(),
				"nickname" => $user->getNickname()
			);
		}
		$this->json($data);
	}

	/**
	 * Verificación de inicio de sesión
	 * 
	 * Verifica que el usuario y contraseña enviados por POST estén correctos para la entidad
	 * a la que se solicita el acceso, e imprime la respuesta en formato JSON.
	 * 
	 * @return void
	 */
	public function test_login()
	{
		$data = Array("session" => false);
		if(empty($_POST["nickname"]))
		{
			$data["title"] = "Error";
			$data["message"] = _("Bad request");
			$data["theme"] = "red";
			$this->json($data);
			return;
		}
		$user = usersModel::findBy("nickname", $_POST["nickname"]);
		if($user->exists() && empty($user->getPasswordHash()) && md5($_POST["password"]) == $user->getPassword())
		{
			$user->set([
				"password_hash" => password_hash($_POST["password"], PASSWORD_BCRYPT),
				"password" => "HASH"
			])->save();
		}
		if($user->exists() && password_verify($_POST["password"], $user->getPasswordHash()))
		{
			$data["reload"] = true;

			# Cargar los datos del usuario a la sesión actual
			foreach ($user->toArray() as $key => $value) {
				Session::set($key, $value);
			}

			# Cargar el idioma del usuario
			if(!empty($user->getLocale()))
			{
				Session::set("lang", explode("_", $user->getLocale())[0]);
			}

			# Cargar el tema del usuario
			if(!empty($user->getThemeId()))
			{
				$theme = appThemesModel::find($user->getThemeId());
				Session::set("theme_id", $theme->getThemeId());
				Session::set("theme_url", $theme->getThemeUrl());
			}

			# Obtener la dirección IP y el navegador
			$now = Date("Y-m-d H:i:s");
			$user_agent = $_SERVER['HTTP_USER_AGENT'];
			$ipv4 = $this->getRealIP();
			$browser = browsersModel::where("user_agent", $user_agent)->get();
			if(!$browser->exists())
			{
				$parser = new UserAgentParser();
				$ua = $parser->parse($user_agent);
				$browser->set([
					"user_agent" => $user_agent,
					"browser_name" => $ua->browser(),
					"browser_version" => $ua->browserVersion(),
					"platform" => $ua->platform(),
					"creation_user" => $user->getUserId(),
					"creation_time" => $now
				])->save();
			}

			# Guardar un registro del inicio de sesión
			$session = new userSessionsModel();
			$session->set([
				"user_id" => $user->getUserId(),
				"ip_address" => $ipv4,
				"browser_id" => $browser->getBrowserId(),
				"date_time" => $now
			])->save();

			# Cargar los módulos a la sesión actual
			Session::set("modules", availableModulesModel::where("user_id", $user->getUserId())->orderBy("module_order")->getAllArray());

			# Cargar los permisos del usuario
			$permissions = Array();
			$elements = roleElementsModel::where("role_id", $user->getRoleId())->join("app_elements", "element_id")->getAll();
			foreach($elements as $element)
			{
				$permissions[$element["element_key"]] = $element["permissions"];
			}
			Session::set("permissions", $permissions);
		}
		else
		{
			$data["title"] = "Error";
			$data["message"] = _("Bad user or password");
			$data["theme"] = "red";
		}
		$this->json($data);
	}

	/**
	 * Cerrar sesión
	 * 
	 * Cierra la sesión del usuario y limpia todas las variables de sesión. Finalmente
	 * imprime la respuesta en formato JSON.
	 * 
	 * @return void
	 */
	public function logout()
	{
		$data = Array("session" => false);
		Session::destroy();
		$this->json($data);
	}

	/**
	 * Mi cuenta
	 * 
	 * Muestra un formulario con preferencias para la cuenta del usuario.
	 * 
	 * @return void
	 */
	public function MyAccount()
	{
		$this->session_required();
		$this->view->data["title"] = _("My account");
		$this->view->standard_form();
		$this->view->data["nav"] = $this->view->render("main/nav", true);
		$this->view->data["content"] = $this->view->render("user/my_account", true);
		$this->view->render('main');
	}

	/**
	 * Guardar mi cuenta
	 * 
	 * Guarda los datos de la configuración del usuario desde el formulario Mi Cuenta.
	 * 
	 * @return void
	 */
	public function save_my_account()
	{
		$this->session_required("json");
		$data = $_POST;
		$data["success"] = false;
		$user = usersModel::find(Session::get("user_id"));
		if($data["theme_id"] != Session::get("theme_id"))
		{
			$user->setThemeId($data["theme_id"]);
			$theme = appThemesModel::find($data["theme_id"]);
			Session::set("theme_id", $theme->getThemeId());
			Session::set("theme_url", $theme->getThemeUrl());
		}
		if($data["locale"] != Session::get("locale"))
		{
			$user->setLocale($data["locale"]);
			Session::set("locale", $data["locale"]);
			Session::set("lang", explode("_", $data["locale"])[0]);
		}
		if($data["user_name"] != Session::get("user_name"))
		{
			$user->setUserName($data["user_name"]);
			Session::set("user_name", $data["user_name"]);
		}
		$user->save();
		$data["reload_after"] = true;
		$data["success"] = true;
		$data["title"] = _("Success");
		$data["message"] = _("Changes have been saved");
		$data["theme"] = "green";
		$this->json($data);
	}

	/**
	 * Cambiar contraseña
	 * 
	 * Cambia la contraseña del usuario
	 * 
	 * @return void
	 */
	public function change_password()
	{
		$this->session_required("json");
		$data = $_POST;
		$data["success"] = false;
		$user = usersModel::find(Session::get("user_id"));
		if(md5($_POST["current_password"]) != $user->getPassword() && !password_verify($_POST["current_password"], $user->getPasswordHash()))
		{
			$data["title"] = "Error";
			$data["message"] = _("Incorrect password");
			$data["theme"] = "red";
			$this->json($data);
			return;
		}
		if($data["new_password"] != $data["confirm_password"])
		{
			$data["title"] = "Error";
			$data["message"] = _("Passwords do not match");
			$data["theme"] = "red";
			$this->json($data);
			return;
		}
		$user->setPassword("HASH");
		$user->setPasswordHash(password_hash($_POST["new_password"], PASSWORD_BCRYPT));
		$user->save();
		$data["reload_after"] = true;
		$data["success"] = true;
		$data["title"] = _("Success");
		$data["message"] = _("Changes have been saved");
		$data["theme"] = "green";
		$this->json($data);
	}
}
?>
