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
	 * Redirige a Mi Cuenta.
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
		$result = [];
		if (Session::get("user_id") == null && $_POST["method"] != "SetNewPassword") {
			$result = $this->LoadLoginForm();
		} else {
			switch ($_POST["method"]) {
				case "MyAccount":
					$result = $this->LoadMyAccountForm();
					break;
				case "SetNewPassword":
					$result = $this->LoadNewPasswordForm();
					break;
			}
		}
		http::json($result);
	}

	private function LoadLoginForm()
	{
		return [];
	}

	private function LoadMyAccountForm()
	{
		$result = [
			"themes" => appThemesModel::list(),
			"locales" => appLocalesModel::list("locale_code", "locale_name")
		];

		foreach ($result["themes"] as &$theme) {
			$theme["text"] = _($theme["text"]);
		}
		unset($theme);

		foreach ($result["locales"] as &$locale) {
			$locale["text"] = _($locale["text"]);
		}
		unset($locale);

		$user = usersModel::find(Session::get("user_id"));
		$result["update"] = [
			"theme_id" => Session::get("theme_id"),
			"locale" => Session::get("locale"),
			"user_name" => $user->getUserName(),
			"nickname" => $user->getNickname()
		];
		return $result;
	}

	private function LoadNewPasswordForm()
	{
		$user = usersModel::find(Session::get("password_user_id"));
		$result = [
			"update" => [
				"user_id" => $user->getUserId(),
				"nickname" => $user->getNickName(),
				"user_name" => $user->getUserName()
			]
		];
		return $result;
	}

	/**
	 * Verificación de inicio de sesión
	 * 
	 * Verifica que el usuario y contraseña enviados por POST estén correctos para la entidad
	 * a la que se solicita el acceso, e imprime la respuesta en formato JSON.
	 * 
	 * @return void
	 */
	public function TestLogin()
	{
		$request = http::getRequestData();
		if (empty($request["nickname"])) {
			ApiResponse::error(
				code: "REQUIRED_FIELD",
				title: _("Error"),
				message: _("Bad request")
			);
		}

		$user = usersModel::findBy("nickname", $request["nickname"]);
		if (!$user->exists()) {
			ApiResponse::error(
				code: "AUTH_INVALID_CREDENTIALS",
				title: _("Error"),
				message: _("Bad user or password")
			);
		}

		# Verificar número de intentos fallidos en los últimos cinco minutos
		$date_time = Date("Y-m-d H:i:s", time() - 300);
		$attemps = loginAttempsModel::where("user_id", $user->getUserId())
			->where("date_time", ">=", $date_time)
			->count();
		if ($attemps >= 3) {
			ApiResponse::error(
				code: "AUTH_LOGIN_RATE_LIMIT",
				title: _("Error"),
				message: _("Too many failed attempts, try again in five minutes")
			);
		}

		if (empty($user->getPasswordHash()) && md5($request["password"]) == $user->getPassword()) {
			$user->set([
				"password_hash" => password_hash($request["password"], PASSWORD_BCRYPT),
				"password" => "HASH"
			])->save();
		}

		# Obtener la dirección IP y el navegador
		$now = Date("Y-m-d H:i:s");
		$user_agent = $_SERVER['HTTP_USER_AGENT'];
		$ipv4 = $this->getRealIP();
		$browser = browsersModel::where("user_agent", $user_agent)
			->get();
		if (!$browser->exists()) {
			$parser = new UserAgentParser();
			$ua = $parser->parse($user_agent);
			if (!empty($ua->platform)) {
				$browser->set([
					"user_agent" => $user_agent,
					"browser_name" => $ua->browser(),
					"browser_version" => $ua->browserVersion(),
					"platform" => $ua->platform(),
					"creation_user" => $user->getUserId(),
					"creation_time" => $now
				])->save();
			}
		}

		if (password_verify($request["password"], $user->getPasswordHash())) {
			# Verificar si la contraseña ha sido cambiada en los últimos noventa días
			$passwordChanged = new DateTime($user->getPasswordChanged());
			$threshold = new DateTime();
			$threshold->sub(new DateInterval('P90D'));
			if ($passwordChanged < $threshold) {
				Session::set("password_user_id", $user->getUserId());
				ApiResponse::success(actions:[
					"redirect"=> "/User/SetNewPassword/"
				]);
				return;
			}

			# Cargar los datos del usuario a la sesión actual
			foreach ($user->toArray() as $key => $value) {
				Session::set($key, $value);
			}

			# Guardar en la sesión el identificador del equipo del usuario
			Session::set("blackphp_device_code", $request["blackphp_device_code"]);

			# Cargar el idioma del usuario
			if (!empty($user->getLocale())) {
				Session::set("lang", explode("_", $user->getLocale())[0]);
			}

			# Cargar el tema del usuario
			if (!empty($user->getThemeId())) {
				$theme = appThemesModel::find($user->getThemeId());
				Session::set("theme_id", $theme->getThemeId());
				Session::set("theme_url", $theme->getThemeUrl());
			}

			# Guardar un registro del inicio de sesión
			$session = new userSessionsModel();
			$session->set([
				"user_id" => $user->getUserId(),
				"ip_address" => $ipv4,
				"device_code" => $request["blackphp_device_code"],
				"browser_id" => $browser->getBrowserId(),
				"date_time" => $now
			])->save();

			# Cargar los módulos a la sesión actual
			Session::set(
				"modules",
				availableModulesModel::where("role_id", $user->getRoleId())
					->orderBy("module_order")
					->getAllArray()
			);

			# Cargar los permisos del usuario
			$permissions = [];
			$elements = roleElementsModel::where("role_id", $user->getRoleId())
				->join("app_elements", "element_id")
				->getAll();
			foreach ($elements as $element) {
				$permissions[$element["element_key"]] = $element["permissions"];
			}
			Session::set("permissions", $permissions);

			ApiResponse::success(actions: ["reload" => true]);
		} else {
			$login_attemp = new loginAttempsModel();
			$login_attemp->set([
				"user_id" => $user->getUserId(),
				"date_time" => $now,
				"browser_id" => $browser->getBrowserId(),
				"ip_address" => $ipv4
			])->save();
			ApiResponse::error(
				code: "AUTH_INVALID_CREDENTIALS",
				title: _("Error"),
				message: _("Bad user or password")
			);
		}
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
		Session::destroy();
		ApiResponse::success();
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
		$this->view->add("scripts", "js", [
			"public/scripts/profile.js"
		]);
		$this->view->add("styles", "css", [
			"public/styles/profile.css"
		]);

		$profileImage = "entities/" . Session::get("entity/entity_subdomain") . "/users/" . Session::get("user_id") . "-profile.jpg";
		if (!file_exists($profileImage)) {
			$profileImage = "public/images/user.png";
		}
		$this->view->data["profile_image"] = $profileImage;

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
	public function SaveMyAccount()
	{
		$this->session_required("json");

		$user = usersModel::find(Session::get("user_id"));
		if ($_POST["theme_id"] != Session::get("theme_id")) {
			$user->setThemeId($_POST["theme_id"]);
			$theme = appThemesModel::find($_POST["theme_id"]);
			Session::set("theme_id", $theme->getThemeId());
			Session::set("theme_url", $theme->getThemeUrl());
		}
		if ($_POST["locale"] != Session::get("locale")) {
			$user->setLocale($_POST["locale"]);
			Session::set("locale", $_POST["locale"]);
			Session::set("lang", explode("_", $_POST["locale"])[0]);
		}
		if ($_POST["user_name"] != Session::get("user_name")) {
			$user->setUserName($_POST["user_name"]);
			Session::set("user_name", $_POST["user_name"]);
		}
		$user->save();

		$dir = "entities/" . Session::get("entity/entity_subdomain") . "/";
		if ($_SERVER["SERVER_NAME"] == $_SERVER["SERVER_ADDR"]) {
			$dir = "entities/local/";
		}
		$dir .= "users/";
		if (!is_dir($dir)) {
			mkdir($dir, 0755, true);
		}

		if (!empty($_FILES["profile"]["name"])) {
			$file = $dir . Session::get("user_id") . "-profile.jpg";
			if (file_exists($file)) {
				unlink($file);
			}
			move_uploaded_file($_FILES["profile"]["tmp_name"], $file);
		}

		ApiResponse::success(
			code: "UPDATED",
			title: _("Success"),
			message: _("Changes have been saved"),
			actions: [
				"reload" => true
			]
		);
	}

	/**
	 * Cambiar contraseña
	 * 
	 * Cambia la contraseña del usuario
	 * 
	 * @return void
	 */
	public function ChangePassword()
	{
		$this->session_required("json");
		$user = usersModel::find(Session::get("user_id"));
		if (md5($_POST["current_password"]) != $user->getPassword() && !password_verify($_POST["current_password"], $user->getPasswordHash())) {
			ApiResponse::error(
				code: "AUTH_INVALID_CREDENTIALS",
				title: _("Error"),
				message: _("Incorrect password")
			);
		}
		if ($_POST["new_password"] != $_POST["confirm_password"]) {
			ApiResponse::error(
				code: "VALIDATION_ERROR",
				title: _("Error"),
				message: _("Passwords do not match")
			);
		}

		$validate = $this->ValidatePassword($_POST["new_password"]);
		if ($validate !== true) {
			ApiResponse::error(
				code: "VALIDATION_ERROR",
				title: _("Error"),
				message: implode("<br>", $validate)
			);
		}

		$user->setPassword("HASH");
		$user->setPasswordHash(password_hash($_POST["new_password"], PASSWORD_BCRYPT));
		$user->save();

		ApiResponse::success(
			code: "UPDATED",
			title: _("Success"),
			message: _("Changes have been saved"),
			actions: ["reload" => true]
		);
	}

	public function SetNewPassword()
	{
		if (Session::get("password_user_id") == null) {
			header("Location: /");
			return;
		}
		$this->view->data["title"] = _("Change password");
		$this->view->standard_form();
		$this->view->data["nav"] = "";
		$this->view->data["content"] = $this->view->render("user/change_password", true);
		$this->view->render('main');
	}

	public function SaveNewPassword()
	{
		$result = [];

		# El usuario no existe
		$user = usersModel::find($_POST["user_id"]);
		if (!$user->exists()) {
			ApiResponse::error(
				code: "REQUIRED_FIELD",
				title: _("Error"),
				message: _("Bad request")
			);
		}

		# La contraseña actual es incorrecta
		if (md5($_POST["current_password"]) != $user->getPassword() && !password_verify($_POST["current_password"], $user->getPasswordHash())) {
			ApiResponse::error(
				code: "AUTH_INVALID_CREDENTIALS",
				title: _("Error"),
				message: _("Incorrect password")
			);
		}

		# Las contraseñas no coinciden
		if ($_POST["new_password"] != $_POST["confirm_password"]) {
			ApiResponse::error(
				code: "VALIDATION_ERROR",
				title: _("Error"),
				message: _("Passwords do not match")
			);
		}

		# No puede ser la misma contraseña anterior

		# Validar contraseña
		$validate = $this->ValidatePassword($_POST["new_password"]);
		if ($validate !== true) {
			ApiResponse::error(
				code: "VALIDATION_ERROR",
				title: _("Error"),
				message: implode("<br>", $validate)
			);
			return;
		}

		$user->set([
			"password" => "HASH",
			"password_hash" => password_hash($_POST["new_password"], PASSWORD_BCRYPT),
			"password_changed" => Date("Y-m-d H:i:s")
		]);
		$user->save();
		Session::unset("password_user_id");

		ApiResponse::success(
			code: "UPDATED",
			title: _("Success"),
			message: _("Changes have been saved"),
			actions: ["redirect" => "/"]
		);
	}

	private function ValidatePassword($password)
	{
		$errors = [];
		# Debe contener al menos seis caracteres e incluir al menos:
		# - Seis caracteres de longitud
		# - Una letra minúscula
		# - Una letra mayúscula
		# - Un número
		# - Un caracter no alfanumérico

		// Minimum length check
		if (strlen($password) < 6) {
			$errors[] = _("Password must be at least 6 characters long");
		}

		// Pattern checks
		if (!preg_match('/[a-z]/', $password)) {
			$errors[] = _("Password must include at least one lowercase letter");
		}
		if (!preg_match('/[A-Z]/', $password)) {
			$errors[] = _("Password must include at least one uppercase letter");
		}
		if (!preg_match('/[0-9]/', $password)) {
			$errors[] = _("Password must include at least one digit");
		}
		if (!preg_match('/[^a-zA-Z0-9]/', $password)) {
			$errors[] = _("Password must include at least one special character");
		}

		// If no errors, return true
		if (empty($errors)) {
			return true;
		}

		// Otherwise return the list of errors
		return $errors;
	}
}
