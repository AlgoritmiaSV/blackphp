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
			$themes = appThemesModel::list();
			foreach($themes as $key => $theme)
			{
				$themes[$key]["text"] = _($theme["text"]);
			}
			$data["themes"] = $themes;
			$data["locales"] = Array(
				Array("id" => "en_US", "text" => _("English")),
				Array("id" => "es_ES", "text" => _("Spanish"))
			);
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
		$user = usersModel::where("nickname", $_POST["nickname"])->where("password", md5($_POST["password"]))->get()->toArray();
		if(isset($user["nickname"]))
		{
			$data["reload"] = true;

			foreach ($user as $key => $value) {
				Session::set($key, $value);
			}
			if(!empty($user["locale"]))
			{
				Session::set("lang", explode("_", $user["locale"])[0]);
			}
			if(!empty($user["theme_id"]))
			{
				$theme = appThemesModel::find($user["theme_id"]);
				Session::set("theme_id", $theme->getThemeId());
				Session::set("theme_url", $theme->getThemeUrl());
			}
			$now = Date("Y-m-d H:i:s");
			# Get user agent
			$user_agent = $_SERVER['HTTP_USER_AGENT'];
			# Get IP Address
			$ipv4 = $this->getRealIP();
			# Check if exists
			$browser = browsersModel::where("user_agent", $user_agent)->get();
			if(!$browser->exists())
			{
				#Set new browser
				$parser = new UserAgentParser();
				$ua = $parser->parse($user_agent);
				$browser->set(Array(
					"user_agent" => $user_agent,
					"browser_name" => $ua->browser(),
					"browser_version" => $ua->browserVersion(),
					"platform" => $ua->platform(),
					"creation_user" => $user["user_id"],
					"creation_time" => $now
				))->save();
			}

			#Set session
			$session = new userSessionsModel();
			$session->set(Array(
				"user_id" => $user["user_id"],
				"ip_address" => $ipv4,
				"browser_id" => $browser->getBrowserId(),
				"date_time" => $now
			))->save();

			#Set modules
			Session::set("modules", availableModulesModel::where("user_id", $user["user_id"])->orderBy("module_order")->getAllArray());
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
		$this->view->data["nav"] = $this->view->render("nav", true);
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
		if(md5($data["current_password"]) != $user->getPassword())
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
		$user->setPassword(md5($data["new_password"]));
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
