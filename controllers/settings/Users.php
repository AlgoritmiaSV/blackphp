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
		$this->session_required("html", $this->module);
		$this->view->data["title"] = _("Users");
		$this->view->standard_list();
		$this->view->data["nav"] = $this->view->render("main/nav", true);
		$this->view->data["print_title"] = _("Users");
		$this->view->data["print_header"] = $this->view->render("print_header", true);
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
		$this->session_required("html", $this->module);
		$this->view->data["title"] = _("New user");
		$this->view->standard_form();
		$this->view->data["nav"] = $this->view->render("main/nav", true);
		$this->view->restrict[] = "edition";
		$modules = availableModulesModel::where("user_id", Session::get("user_id"))->orderBy("module_order")->getAllArray();
		$this->view->data["modules"] = "";
		foreach($modules as $module)
		{
			foreach($module as $key => $item)
			{
				$this->view->data[$key] = $item;
			}
			$this->view->data["methods"] = availableMethodsModel::where("user_id", Session::get("user_id"))
			->where("module_id", $module["module_id"])
			->orderBy("method_order")->getAllArray();
			$this->view->data["modules"] .= $this->view->render("modules", true);
		}
		$this->view->data["content"] = $this->view->render("settings/user_edit", true);
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
		$this->session_required("html", $this->module);
		$this->view->data["title"] = _("Edit user");
		$this->view->standard_form();
		$this->view->data["nav"] = $this->view->render("main/nav", true);
		if($user_id == Session::get("user_id"))
		{
			$this->view->restrict[] = "no_self";
		}
		$this->view->restrict[] = "creation";
		$modules = availableModulesModel::where("user_id", Session::get("user_id"))->orderBy("module_order")->getAllArray();
		$this->view->data["modules"] = "";
		foreach($modules as $module)
		{
			foreach($module as $key => $item)
			{
				$this->view->data[$key] = $item;
			}
			$this->view->data["methods"] = availableMethodsModel::where("user_id", Session::get("user_id"))
			->where("module_id", $module["module_id"])
			->orderBy("method_order")->getAllArray();
			$this->view->data["modules"] .= $this->view->render("modules", true);
		}
		$this->view->data["content"] = $this->view->render("settings/user_edit", true);
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
		$this->session_required("html", $this->module);
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
	public function users_table_loader($response = "JSON")
	{
		$this->session_required("json");
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
		$this->session_required("internal");
		if(empty($user_id))
		{
			$user_id = $_POST["id"];
		}
		$user = usersModel::find($user_id)->toArray();
		$this->view->data = array_merge($this->view->data, $user);
		$sessions = userSessionsModel::join("browsers", "browser_id")->where("user_sessions.user_id", $user_id)->orderBy("date_time", "DESC")->addCounter("item")->get(10);
		foreach($sessions as $key => $session)
		{
			$time = strtotime($session["date_time"]);
			$sessions[$key]["session_date"] = Date("d/m/Y", $time);
			$sessions[$key]["session_time"] = Date("h:i a", $time);
		}
		$this->view->data["sessions"] = $sessions;

		$modules = availableModulesModel::where("user_id", $user_id)->getAllArray();
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
		$this->view->data["print_header"] = $this->view->render("print_header", true);
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
		$this->session_required("json");
		$data = Array("success" => false);
		if(empty($_POST["user_name"]))
		{
			$this->json($data);
			return;
		}

		#Validate nickname
		$test = usersModel::where("nickname", $_POST["nickname"])
			->where("user_id", "!=", $_POST["user_id"])->get();
		if(!empty($test->getUserId()))
		{
			$data["title"] = "Error";
			$data["message"] = _("The nickname already exists!");
			$data["theme"] = "red";
			$this->json($data);
			return;
		}

		$user_id = 0;
		$user = usersModel::find($_POST["user_id"])
			->set(Array(
				"user_name" => $_POST["user_name"],
				"nickname" => $_POST["nickname"]
			));
		if(!empty($_POST["password"]))
		{
			$user->setPassword(md5($_POST["password"]));
		}
		if(empty($user->getPassword()))
		{
			$user->setPassword("");
		}
		$user->save();

		$user_id = $user->getUserId();
		if($user_id != Session::get("user_id"))
		{
			#Module access
			userModulesModel::where("user_id", $user_id)->whereNotIn($_POST["modules"], "module_id")->update(Array("status" => 0));
			foreach($_POST["modules"] as $module_id)
			{
				userModulesModel::where("user_id", $user_id)
					->where("module_id", $module_id)
					->where("status", ">=", 0)
					->get()->set(Array(
						"module_id" => $module_id,
						"user_id" => $user_id,
						"access_type" => 1,
						"status" => 1
					))->save();
			}

			#Method access
			userMethodsModel::where("user_id", $user_id)->whereNotIn($_POST["methods"], "method_id")->update(Array("status" => 0));
			foreach($_POST["methods"] as $method_id)
			{
				userMethodsModel::where("user_id", $user_id)
					->where("method_id", $method_id)
					->where("status", ">=", 0)
					->get()->set(Array(
						"method_id" => $method_id,
						"user_id" => $user_id,
						"access_type" => 1,
						"status" => 1
					))->save();
			}
		}
		$data["success"] = true;
		$data["title"] = _("Success");
		$data["message"] = _("Changes have been saved");
		$data["theme"] = "green";
		$data["reload_after"] = true;
		$this->json($data);
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
		$this->session_required("json");
		$data = Array("deleted" => false);
		if(empty($_POST["id"]))
		{
			$this->json($data);
			return;
		}
		$user = usersModel::find($_POST["id"]);
		$affected = $user->delete();
		$data["deleted"] = $affected > 0;
		$this->json($data);
	}
}
?>