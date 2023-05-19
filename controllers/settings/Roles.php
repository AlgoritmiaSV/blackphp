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

		$role_elements = "";
		$modules = appModulesModel::getAll();
		foreach($modules as $module)
		{
			$elements = appElementsModel::where("module_id", $module->getModuleId())->getAllArray();
			foreach($elements as &$element)
			{
				if($element["is_creatable"] == 0)
				{
					$element["creatable"] = "disabled";
				}
				if($element["is_updatable"] == 0)
				{
					$element["updatable"] = "disabled";
				}
				if($element["is_deletable"] == 0)
				{
					$element["deletable"] = "disabled";
				}
			}
			unset($element);
			$this->view->data["module_name"] = $module->getModuleName();
			$this->view->data["elements"] = $elements;
			$role_elements .= $this->view->render("settings/role_elements", true);
		}
		$this->view->data["role_elements"] = $role_elements;

		$this->view->data["content"] = $this->view->render("settings/role_edit", true);
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

	public function save_role()
	{
		$data = $_POST;
		$data["success"] = false;

		$role = rolesModel::find($_POST["role_id"]);
		$role->set([
			"role_name" => $_POST["role_name"]
		])->save();
		$elements = Array();
		foreach($_POST["read"] as $element)
		{
			$elements[$element] = 8;
		}
		foreach($_POST["create"] as $element)
		{
			$elements[$element] = intval($elements[$element]) + 4;
		}
		foreach($_POST["update"] as $element)
		{
			$elements[$element] = intval($elements[$element]) + 2;
		}
		foreach($_POST["delete"] as $element)
		{
			$elements[$element] = intval($elements[$element]) + 1;
		}
		foreach($elements as $element_id => $permissions)
		{
			$role_element = roleElementsModel::where("role_id", $role->getRoleId())->where("element_id", $element_id)->get();
			$role_element->set([
				"role_id" => $role->getRoleId(),
				"element_id" => $element_id,
				"permissions" => $permissions,
				"status" => 1
			])->save();
		}
		roleElementsModel::where("role_id", $role->getRoleId())->whereNotIn(array_keys($elements), "element_id")->update(["status" => 0]);

		#Finish and response
		$data["success"] = true;
		$data["title"] = _("Success");
		$data["message"] = _("Changes have been saved");
		$data["theme"] = "green";
		if(!empty($_POST["role_id"]))
		{
			$data["no_reset"] = true;
			$this->setUserLog("update", "roles", $role->getRoleId());
		}
		else
		{
			$data["reload_after"] = true;
			$this->setUserLog("create", "roles", $role->getRoleId());
		}
		$this->json($data);
	}
}
?>
