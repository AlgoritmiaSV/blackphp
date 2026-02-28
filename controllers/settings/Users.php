<?php
trait Users
{
	/**
	 * Usuarios
	 * 
	 * Muestra una lista de usuarios del sistema
	 * 
	 * @return void
	 */
	public function Users()
	{
		$this->check_permissions("read", "users");
		$this->view->data["title"] = _("Users");
		$this->view->standard_list();
		$this->view->data["nav"] = $this->view->render("main/nav", true);
		$this->view->data["print_title"] = _("Users");
		$this->view->data["print_header"] = $this->view->render("main/" . Session::get("options/page_header"), true);
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
		$this->check_permissions("create", "users");
		$this->view->data["title"] = _("New user");
		$this->view->standard_form();
		$this->view->data["nav"] = $this->view->render("main/nav", true);
		$this->view->restrict[] = "edition";
		$this->view->data["content"] = $this->view->render("settings/user_form", true);
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
		$this->check_permissions("update", "users");
		$this->view->data["title"] = _("Edit user");
		$this->view->standard_form();
		$this->view->data["nav"] = $this->view->render("main/nav", true);
		if($user_id == Session::get("user_id"))
		{
			$this->view->restrict[] = "no_self";
		}
		$this->view->restrict[] = "creation";
		$this->view->data["content"] = $this->view->render("settings/user_form", true);
		$this->view->render('main');
	}

	/**
	 * Detalles de usuario
	 * 
	 * Muestra una hoja con los datos del usuario y las últimas sesiones abiertas.
	 * @param int $user_id ID del usuario a consultar
	 * 
	 * @return void
	 */
	public function UserDetails($user_id)
	{
		$this->check_permissions("read", "users");
		$this->view->data["title"] = _("User details");
		$this->view->standard_details();
		$this->view->data["system_short_date"] = Date("d/m/Y");
		$this->view->data["nav"] = $this->view->render("main/nav", true);
		$this->view->data["content_id"] = "user_details";
		$this->view->data["content"] = $this->view->render("content_loader", true);
		$this->view->render('main');
	}

	/**
	 * Cargar tabla de usuarios
	 * 
	 * Devuelve, en formato JSON o en un archivo Excel, la lista de usuarios.
	 * @param string $response El modo de respuesta (JSON o Excel)
	 * 
	 * @return void
	 */
	public function user_table_loader($response = "JSON")
	{
		$this->check_permissions("read", "users");
		$data = Array();
		$users = userDataModel::getAllArray();
		foreach($users as $key => $user)
		{
			$users[$key]["last_login"] = "";
			if(!empty($user["last_login"]))
			{
				$last_login = new DateTime($user["last_login"]);
				$users[$key]["last_login"] = $last_login->format("d/m/Y h:ia");
			}
		}
		$data["content"] = $users;
		if($response == "Excel")
		{
			$data["title"] = _("Users");
			$data["headers"] = Array(_("User"), _("Complete name"), _("Last login"));
			$data["fields"] = Array("nickname", "user_name", "last_login");
			excel::create_from_table($data, "Users_" . Date("YmdHis") . ".xlsx");
		}
		else
		{
			$this->json($data);
		}
	}

	/**
	 * Carga de detalles del usuario
	 * 
	 * Muestra una hoja con los detalles del usuario. Este método puede ser invocado por a través
	 * de UserDetails (embedded) y directamente para ser mostrado en un jAlert (standalone); por
	 * ejemplo, para el usuario con ID 1, se podría visitar:
	 * - Settings/UserDetails/1/ (embedded)
	 * - Settings/user_details_loader/1/standalone/ (standalone)
	 * @param int $user_id ID del usuario
	 * @param string $mode Modo en que se mostrará la vista
	 * 
	 * @return void
	 */
	public function user_details_loader($user_id = "", $mode = "embedded")
	{
		$this->check_permissions("read", "users", $mode);
		if(empty($user_id))
		{
			$user_id = $_POST["id"];
		}
		$user = userDataModel::findBy("user_id", $user_id)->toArray();
		$this->view->data = array_merge($this->view->data, $user);
		$sessions = userSessionsModel::join("browsers", "browser_id")
			->where("user_sessions.user_id", $user_id)
			->orderBy("date_time", "DESC")
			->addCounter("item")
			->get(10);
		foreach($sessions as $key => $session)
		{
			$time = strtotime($session["date_time"]);
			$sessions[$key]["session_date"] = Date("d/m/Y", $time);
			$sessions[$key]["session_time"] = Date("h:i a", $time);
		}
		$this->view->data["sessions"] = $sessions;

		#User photo
		$photo = glob("entities/" . $this->entity_subdomain . "/users/profile_" . $user["user_id"] . ".*");
		if(count($photo) > 0)
		{
			$this->view->data["user_photo"] = $photo[0];
		}
		else
		{
			$this->view->data["user_photo"] = "public/images/user.png";
		}
		$this->userActions($user);
		if($user_id == Session::get("user_id"))
		{
			$this->view->restrict[] = "no_self";
		}
		$this->view->data["print_title"] = _("User details");
		$this->view->data["print_header"] = $this->view->render("main/" . Session::get("options/page_header"), true);
		if($mode == "standalone")
		{
			$this->view->data["title"] = _("User details");
			$this->view->standard_details();
			$this->view->add("styles", "css", Array(
				'styles/standalone.css'
			));
			$this->view->restrict[] = "embedded";
			$this->view->data["content"] = $this->view->render('settings/user_details', true);
			$this->view->render('clean_main');
		}
		else
		{
			$this->view->render("settings/user_details");
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
		$this->check_permissions(empty($_POST["user_id"]) ? "create" : "update", "users");
		if(empty($_POST["user_name"]))
		{
			$this->json([
				"success" => false,
				"title" => _("Error"),
				"message" => _("Bad request"),
				"theme" => "red"
			]);
			return;
		}

		#Validate nickname
		$test = usersModel::where("nickname", $_POST["nickname"])
			->where("user_id", "!=", $_POST["user_id"])->get();
		if(!empty($test->getUserId()))
		{
			$this->json([
				"success" => false,
				"title" => _("Error"),
				"message" => _("The nickname already exists!"),
				"theme" => "red"
			]);
			return;
		}

		$user_id = 0;
		$user = usersModel::find($_POST["user_id"])
			->set([
				"user_name" => $_POST["user_name"],
				"nickname" => $_POST["nickname"]
			]);
		if(!empty($_POST["password"]))
		{
			$validate = $this->ValidatePassword($_POST["password"]);
			if($validate !== true)
			{
				$this->json([
					"success" => false,
					"title" => _("Error"),
					"message" => implode("<br>", $validate),
					"theme" => "red"
				]);
				return;
			}
			$user->setPassword("HASH");
			$user->setPasswordHash(password_hash($_POST["password"], PASSWORD_BCRYPT));
			$user->setPasswordChanged(Date("Y-m-d H:i:s"));
		}
		if(empty($user->getPassword()))
		{
			$user->setPassword("");
			$user->setPasswordHash("");
			$user->setPasswordChanged(Date("Y-m-d H:i:s"));
		}
		if(!empty($_POST["role_id"]))
		{
			$user->setRoleId($_POST["role_id"]);
		}
		$user->save();
		if(!empty($_POST["user_id"]))
		{
			$this->setUserLog("update", "users", $user->getUserId());
		}
		else
		{
			$this->setUserLog("create", "users", $user->getUserId());
		}

		$this->json([
			"success" => true,
			"title" => _("Success"),
			"message" => _("Changes have been saved"),
			"theme" => "green",
			"reload_after" => true
		]);
	}

	/**
	 * Eliminar usuario
	 * 
	 * Elimina un usuario e imprime la respuesta en formato JSON.
	 * 
	 * @return void
	 */
	public function delete_user()
	{
		$this->check_permissions("delete", "users");
		if(empty($_POST["id"]))
		{
			$this->json([
			"deleted" => false,
			"title" => _("Error"),
			"message" => _("Bad request"),
			"theme" => "red"
		]);
			return;
		}
		$user = usersModel::find($_POST["id"]);
		$affected = $user->delete();
		$this->setUserLog("delete", "users", $user->getUserId());
		$this->json([
			"deleted" => $affected > 0,
			"title" => _("Success"),
			"message" => _("Deleted successfully"),
			"theme" => "green"
		]);
	}

	public function CloseSessions($userId)
	{
		$serverName = $_SERVER["SERVER_NAME"];
		$activeSession = $_SESSION;
		$ectiveSessionId = session_id();
		$path = session_save_path();
		$files = glob($path . "/sess*");
		foreach($files as $file)
		{
			session_decode(file_get_contents($file));
			if($_SESSION["server_name"] == $serverName && !empty($_SESSION["user_id"]) && $userId == $_SESSION["user_id"])
			{
				unlink($file);
			}
		}
		$_SESSION = $activeSession;
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
?>
