<?php
trait RoleAndUser
{
	public function RoleAndUser()
	{
		$this->InstallerRequired();
		$this->view->data["title"] = _("Role and user");
		$this->view->standard_form();
		if(Session::get("user_id") == null)
		{
			$this->view->restrict[] = "inside_installation";
		}
		else
		{
			$this->view->restrict[] = "outside_installation";
		}
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
			$role_elements .= $this->view->render("installation/role_elements", true);
		}
		$this->view->data["role_elements"] = $role_elements;
		$this->view->data["content"] = $this->view->render("installation/role_and_user", true);
		$this->view->render('main');
	}

	/**
	 * Guardar rol y usuario
	 * 
	 * Guarda los datos del rol con sus permisos respectivos, y los datos del usuario administrador
	 * 
	 * @return void
	 */
	public function SaveRoleAndUser()
	{
		$response = ["success" => false];
		$now = Date("Y-m-d H:i:s");
		$today = Date("Y-m-d");

		#Check session type
		$entity = entitiesModel::find($this->entity_id);

		# Creación del rol administrador
		$role = rolesModel::find($entity->getAdminRole());
		if(!$role->exists())
		{
			$role->set([
				"entity_id" => $entity->getEntityId(),
				"role_name" => "Administrator"
			])->save();
			$entity->setAdminRole($role->getRoleId());
			$entity->save();
		}
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
			$role_element = roleElementsModel::where("role_id", $role->getRoleId())
				->where("element_id", $element_id)
				->where("status", ">=", 0)
				->get();
			$role_element->set([
				"role_id" => $role->getRoleId(),
				"element_id" => $element_id,
				"permissions" => $permissions,
				"status" => 1
			])->save();
		}
		roleElementsModel::where("role_id", $role->getRoleId())->whereNotIn(array_keys($elements), "element_id")->update(["status" => 0]);
		# $data["elements"] = $elements;

		#Save default user
		$user = usersModel::find($_POST["admin_user"]);

		$user->set(Array(
			"entity_id" => $entity->getEntityId(),
			"user_name" => $_POST["user_name"],
			"nickname" => $_POST["nickname"],
			"password" => empty($_POST["password"]) ? $user->getPassword() : "HASH",
			"password_hash" => empty($_POST["password"]) ? $user->getPasswordHash() : password_hash($_POST["password"], PASSWORD_BCRYPT),
			"password_changed" => Date("Y-m-d H:i:s"),
			"role_id" => $role->getRoleId(),
			"theme_id" => 1
		))->save();
		$entity->setAdminUser($user->getUserId());
		$entity->save();

		# Finalizando y enviando respuesta
		$response["success"] = true;
		$response += [
			"title" => _("Success"),
			"message" => _("Installation completed successfully"),
			"theme" => "green",
			"no_reset" => true
		];
		if($_SERVER["SERVER_NAME"] != $_SERVER["SERVER_ADDR"])
		{
			$protocol = "http";
			if((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443 ){
				$protocol .= "s";
			}
			$response["redirect_after"] = $protocol . "://" . str_replace("installer", $_POST["subdomain"], $_SERVER["SERVER_NAME"]) . "/Installation/Menu/";
		}
		else
		{
			$response["reload_after"] = true;
		}
		$this->json($response);
	}
}
?>
