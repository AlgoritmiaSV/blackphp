<?php
trait Menu
{
	public function Menu()
	{
		$this->InstallerRequired();
		$this->view->data["title"] = _("Menu");
		$this->view->standard_form();
		$modules = appModulesModel::orderBy("default_order")->getAllArray();
		$this->view->data["modules"] = "";
		foreach($modules as $module)
		{
			foreach($module as $key => $item)
			{
				$this->view->data[$key] = $item;
			}
			$this->view->data["methods"] = appMethodsModel::where("module_id", $module["module_id"])->orderBy("default_order")->getAllArray();
			$this->view->data["modules"] .= $this->view->render("modules", true);
		}
		if(Session::get("user_id") == null)
		{
			$this->view->restrict[] = "inside_installation";
		}
		else
		{
			$this->view->restrict[] = "outside_installation";
		}
		$this->view->data["content"] = $this->view->render("installation/menu", true);
		$this->view->render('main');
	}

	/**
	 * Guardar Menú
	 * 
	 * Guarda la inforación de los ítems del menú que estarán disponibles para el usuario administrador.
	 * 
	 * @return void
	 */
	public function SaveMenu()
	{
		$result = ["success" => false];
		$now = Date("Y-m-d H:i:s");
		$today = Date("Y-m-d");

		#Check session type
		$entity_id = $this->entity_id;
		if($entity_id == null)
		{
			header("Location: /" . $this->module . "/");
		}

		$entity = entitiesModel::find($this->entity_id);

		# Configurar los módulos de la entidad
		$i = 0;
		entityModulesModel::where("entity_id", $entity->getEntityId())->whereNotIn($_POST["modules"], "module_id")->update(["status" => 0]);
		foreach($_POST["modules"] as $module_id)
		{
			$i++;
			$module = entityModulesModel::where("module_id", $module_id)->where("entity_id", $entity->getEntityId())->where("status", ">=", 0)->get();
			if(empty($module->getEntityModuleId()))
			{
				$module->set([
					"entity_id" => $entity->getEntityId(),
					"module_id" => $module_id,
					"creation_time" => $now,
				]);
			}
			$module->set([
				"module_order" => $i,
				"edition_time" => $now,
				"status" => 1
			])->save();
		}

		# Configuración de métodos de la entidad
		entityMethodsModel::where("entity_id", $entity->getEntityId())->whereNotIn($_POST["methods"], "method_id")->update(["status" => 0]);
		$i = 0;
		foreach($_POST["methods"] as $method_id)
		{
			$i++;
			$method = entityMethodsModel::where("method_id", $method_id)->where("entity_id", $entity->getEntityId())->where("status", ">=", 0)->get();
			if(empty($method->getEntityMethodId()))
			{
				$method->set([
					"entity_id" => $entity->getEntityId(),
					"method_id" => $method_id,
					"creation_time" => $now,
				]);
			}
			$method->set([
				"method_order" => $i,
				"edition_time" => $now,
				"status" => 1
			])->save();
		}

		$user = usersModel::find($entity->getAdminUser());

		# Configuración de íconos del rol de administrador
		if($user->getUserId() != Session::get("user_id"))
		{
			# Módulos del rol administrador
			roleModulesModel::where("role_id", $entity->getAdminRole())->whereNotIn($_POST["modules"], "module_id")->update(["status" => 0]);
			foreach($_POST["modules"] as $module_id)
			{
				$module = roleModulesModel::where("module_id", $module_id)->where("role_id", $entity->getAdminRole())->where("status", ">=", 0)->get();
				$module->set([
					"module_id" => $module_id,
					"role_id" => $entity->getAdminRole(),
					"status" => 1
				])->save();
			}

			# Métodos del rol administrador
			roleMethodsModel::where("role_id", $entity->getAdminRole())->whereNotIn($_POST["methods"], "method_id")->update(["status" => 0]);
			foreach($_POST["methods"] as $method_id)
			{
				$method = roleMethodsModel::where("method_id", $method_id)->where("role_id", $entity->getAdminRole())->where("status", ">=", 0)->get();
				$method->set([
					"role_id" => $entity->getAdminRole(),
					"method_id" => $method_id,
					"status" => 1
				])->save();
			}
		}

		# Preparación de respuesta
		$result = [
			"success" => true,
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
			$result["redirect_after"] = $protocol . "://" . str_replace("installer", $result["subdomain"], $_SERVER["SERVER_NAME"]);
		}
		else
		{
			$result["reload_after"] = true;
		}

		# Cerrar sesión del instalador
		Session::destroy();

		$this->json($result);
	}
}
?>
