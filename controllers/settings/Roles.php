<?php
trait Roles
{
	/**
	 * Roles
	 * 
	 * Muestra una lista de roles del sistema. Los roles sirven para gestionar los permisos de los usuarios.
	 * 
	 * @return void
	 */
	public function Roles()
	{
		$this->session_required("html", $this->module);
		$this->view->data["title"] = _("Roles");
		$this->view->standard_list();
		$this->view->data["nav"] = $this->view->render("main/nav", true);
		$this->view->data["print_title"] = _("Roles");
		$this->view->data["print_header"] = $this->view->render("print_header", true);
		$this->view->data["content"] = $this->view->render("settings/role_list", true);
		$this->view->render('main');
	}

	/**
	 * Nuevo rol
	 * 
	 * Muestra un formulario que permite registrar nuevos roles en el sistema, y asignarles
	 * permisos a diferentes módulos. Un usuario autorizado para registrar roles, sólo puede
	 * otorgar permisos que le han sido otorgados.
	 * 
	 * @return void
	 */
	public function NewRole()
	{
		$this->session_required("html", $this->module);
		$this->view->data["title"] = _("New role");
		$this->view->standard_form();
		$this->view->data["nav"] = $this->view->render("main/nav", true);
		$this->view->restrict[] = "edition";
		$elements = roleElementsModel::where("role_id", Session::get("role_id"))->getAllArray();
		$this->view->data["elements"] = "";
		foreach($elements as $element)
		{
			foreach($element as $key => $item)
			{
				$this->view->data[$key] = $item;
			}
			$this->view->data["elements"] .= $this->view->render("settings/elements", true);
		}
		$this->view->data["content"] = $this->view->render("settings/user_edit", true);
		$this->view->render('main');
	}

	/**
	 * Editar rol
	 * 
	 * Permite editar los datos y los permisos para un rol específico.
	 * 
	 * @return void
	 */
	public function EditRole($role_id)
	{
		$this->session_required("html", $this->module);
		$this->view->data["title"] = _("Edit role");
		$this->view->standard_form();
		$this->view->data["nav"] = $this->view->render("main/nav", true);
		if($role_id == Session::get("role_id"))
		{
			$this->view->restrict[] = "no_self";
		}
		$this->view->restrict[] = "creation";
		$elements = roleElements::where("role_id", Session::get("role_id"))->getAllArray();
		$this->view->data["elements"] = "";
		foreach($elements as $element)
		{
			foreach($element as $key => $item)
			{
				$this->view->data[$key] = $item;
			}
			$this->view->data["elements"] .= $this->view->render("settings/elements", true);
		}
		$this->view->data["content"] = $this->view->render("settings/role_edit", true);
		$this->view->render('main');
	}

	/**
	 * Detalles del rol
	 * 
	 * Muestra una hoja con los datos del rol y sus respectivos permisos.
	 * @param int $role_id ID del usuario a consultar
	 * 
	 * @return void
	 */
	public function RoleDetails($role_id)
	{
		$this->session_required("html", $this->module);
		$this->view->data["title"] = _("Role details");
		$this->view->standard_details();
		$this->view->data["nav"] = $this->view->render("main/nav", true);
		$this->view->data["content_id"] = "role_details";
		$this->view->data["content"] = $this->view->render("content_loader", true);
		$this->view->render('main');
	}

	/**
	 * Cargar tabla de roles
	 * 
	 * Devuelve, en formato JSON o en un archivo Excel, la lista de roles.
	 * @param string $response El modo de respuesta (JSON o Excel)
	 * 
	 * @return void
	 */
	public function role_table_loader($response = "JSON")
	{
		$this->session_required("json");
		$data = Array();
		$roles = rolesModel::getAllArray();
		foreach($roles as &$role)
		{
			$role["users"] = usersModel::where("role_id", $role["role_id"])->count();
		}
		unset($role);
		$data["content"] = $roles;
		if($response == "Excel")
		{
			$data["title"] = _("Roles");
			$data["headers"] = Array(_("Role name"), _("Users"));
			$data["fields"] = Array("role_name", "users");
			excel::create_from_table($data, "Roles_" . Date("YmdHis") . ".xlsx");
		}
		else
		{
			$this->json($data);
		}
	}
}
?>
