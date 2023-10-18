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
		$this->check_permissions("read", "roles");
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
		$this->check_permissions("create", "roles");
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
				$element["element_name"] = _($element["element_name"]);
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
			$this->view->data["module_id"] = $module->getModuleId();
			$this->view->data["module_name"] = _($module->getModuleName());
			$this->view->data["elements"] = $elements;
			$role_elements .= $this->view->render("settings/role_elements", true);
		}
		$this->view->data["role_elements"] = $role_elements;

		$this->view->data["content"] = $this->view->render("settings/role_form", true);
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
		$this->check_permissions("update", "roles");
		$this->view->data["title"] = _("Edit role");
		$this->view->standard_form();
		$this->view->data["nav"] = $this->view->render("main/nav", true);
		$this->view->restrict[] = "creation";

		$role_elements = "";
		$modules = appModulesModel::getAll();
		foreach($modules as $module)
		{
			$elements = appElementsModel::where("module_id", $module->getModuleId())->getAllArray();
			foreach($elements as &$element)
			{
				$element["element_name"] = _($element["element_name"]);
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
			$this->view->data["module_id"] = $module->getModuleId();
			$this->view->data["module_name"] = _($module->getModuleName());
			$this->view->data["elements"] = $elements;
			$role_elements .= $this->view->render("settings/role_elements", true);
		}
		$this->view->data["role_elements"] = $role_elements;

		$this->view->data["content"] = $this->view->render("settings/role_form", true);
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
		$this->check_permissions("read", "roles");
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
		$this->check_permissions("read", "roles");
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

	/**
	 * Carga de detalles del rol
	 * 
	 * Muestra una hoja con los detalles del rol. Este método puede ser invocado por a través
	 * de RoleDetails (embedded) y directamente para ser mostrado en un jAlert (standalone); por
	 * ejemplo, para el rol con ID 1, se podría visitar:
	 * - Settings/RoleDetails/1/ (embedded)
	 * - Settings/role_details_loader/1/standalone/ (standalone)
	 * @param int $role_id ID del usuario
	 * @param string $mode Modo en que se mostrará la vista
	 * 
	 * @return void
	 */
	public function role_details_loader($role_id = "", $mode = "embedded")
	{
		$this->check_permissions("read", "roles", $mode);
		if(empty($role_id))
		{
			$role_id = $_POST["id"];
		}
		$role = rolesModel::find($role_id)->toArray();
		$this->view->data = array_merge($this->view->data, $role);

		$this->view->data["users"] = implode(", ", array_column(usersModel::where("role_id", $role_id)->getAllArray(), "user_name"));

		if($role["role_id"] == Session::get("role_id"))
		{
			$this->view->restrict[] = "self";
		}
		
		# Permissions
		$appModules = appModulesModel::whereIn(array_column(entityModulesModel::getAllArray(), "module_id"))->getAll();
		$modules = "";
		foreach($appModules as $module)
		{
			$this->view->data["module_name"] = _($module->getModuleName());
			$permissions = appElementsModel::join("role_elements", "element_id")->where("module_id", $module->getModuleId())->where("role_id", $role_id)->getAll();
			foreach($permissions as &$permission)
			{
				$permission["element_name"] = _($permission["element_name"]);
				$permission["read"] = ($permission["permissions"] & 8) == 0 ? "not_permitted" : "checked";
				$permission["create"] = ($permission["permissions"] & 4) == 0 ? "not_permitted" : "checked";
				$permission["update"] = ($permission["permissions"] & 2) == 0 ? "not_permitted" : "checked";
				$permission["delete"] = ($permission["permissions"] & 1) == 0 ? "not_permitted" : "checked";
			}
			unset($permission);
			$this->view->data["permissions"] = $permissions;
			$modules .= $this->view->render("settings/role_details_modules", true);
		}
		$this->view->data["modules"] = $modules;

		$this->userActions($role);
		$this->view->data["print_title"] = _("Role details");
		$this->view->data["print_header"] = $this->view->render("print_header", true);
		if($mode == "standalone")
		{
			$this->view->data["title"] = _("Role details");
			$this->view->standard_details();
			$this->view->add("styles", "css", Array(
				'styles/standalone.css'
			));
			$this->view->restrict[] = "embedded";
			$this->view->data["content"] = $this->view->render('settings/role_details', true);
			$this->view->render('clean_main');
		}
		else
		{
			$this->view->render("settings/role_details");
		}
	}

	public function save_role()
	{
		$this->check_permissions(empty($_POST["role_id"]) ? "create" : "update", "roles");
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

	/**
	 * Eliminar rol
	 * 
	 * Elimina un rol e imprime la respuesta en formato JSON.
	 * 
	 * @return void
	 */
	public function delete_role()
	{
		$this->check_permissions("delete", "roles");
		$data = Array("deleted" => false);
		if(empty($_POST["id"]))
		{
			$this->json($data);
			return;
		}
		$role = rolesModel::find($_POST["id"]);
		$users = usersModel::where("role_id", $role->getRoleId())->count();
		if($users > 0)
		{
			$this->json([
				"deleted" => false,
				"title" => _("Error"),
				"message" => _("There are active users in this role"),
				"theme" => "red"
			]);
			return;
		}
		$affected = $role->delete();
		$data["deleted"] = $affected > 0;
		$this->setUserLog("delete", "roles", $role->getRoleId());
		$this->json($data);
	}
}
?>
