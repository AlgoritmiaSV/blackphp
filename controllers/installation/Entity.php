<?php
trait Entity
{
	/**
	 * Nueva entidad
	 * 
	 * Alias de index()
	 * 
	 * @param string $subdomain El subdominio con el que se registrará la entidad
	 * 
	 * @return void
	 */
	public function NewEntity($subdomain)
	{
		$this->InstallerRequired();
		$this->view->data["subdomain"] = $subdomain;

		# Validación de nombre de subdominio
		if (!empty($subdomain) && !preg_match('/^[a-z][a-z0-9]{0,30}$/', $subdomain)) {
			$this->view->data["title"] = _("Installation");
			$this->view->standard_error();
			$this->view->data["nav"] = "";
			$this->view->data["content"] = $this->view->render("installation/invalid_subdomain", true);
			$this->view->render('main');
			exit();
		}

		$this->view->data["title"] = _("Installation");
		$this->view->standard_form();
		if (Session::get("user_id") == null) {
			$this->view->restrict[] = "inside_installation";
		} else {
			$this->view->restrict[] = "outside_installation";
		}
		$this->view->data["content"] = $this->view->render("installation/entity_data", true);
		$this->view->render('main');
	}

	/**
	 * Guardar datos de la entidad.
	 * 
	 * Crea o actualiza una entidad, crea o actualiza un usuario administrador, guarda el logotipo
	 * enviado desde el formulario, y asigna los permisos a los métodos y módulos correspondientes
	 * tanto a la entidad como al usuario administrador.
	 * Finalmente, redirige hacia el subdominio creado.
	 * 
	 * @return void
	 */
	public function SaveEntity()
	{
		$request = http::getRequestData();
		$response = ["success" => false];
		$time = Date("Y-m-d H:i:s");
		$today = Date("Y-m-d");

		if (empty($request["entity_name"])) {
			ApiResponse::error(
				code: "REQUIRED_FIELD",
				title: _("Error"),
				message: _("Bad request")
			);
		}

		# Validando tipo de sesión
		if (Session::get("entity/entity_id") == null) {
			# Si es nueva instalación, se valida el dominio
			$reserved_subdomains = ["www", "installer", "local"];

			if ($_SERVER["SERVER_NAME"] != $_SERVER["SERVER_ADDR"]) {
				if (empty($_POST["subdomain"])) {
					ApiResponse::error(
						code: "VALIDATION_ERROR",
						title: _("Error"),
						message: _("No subdomain chosen")
					);
				}
			}
			$entity = entitiesModel::where("entity_subdomain", $_POST["subdomain"])
				->get()
				->toArray();
			if (isset($entity["entity_id"]) || in_array($_POST["subdomain"], $reserved_subdomains)) {
				ApiResponse::error(
					code: "VALIDATION_ERROR",
					title: _("Error"),
					message: sprintf(_("The subdomain %s is not available"), $_POST["subdomain"])
				);
			}
		}

		$entity = entitiesModel::find(Session::get("entity/entity_id"));
		$subdomain = empty($_POST["subdomain"]) ? $entity->getEntitySubdomain() : $_POST["subdomain"];
		if (empty($entity->getEntityId())) {
			$entity->set([
				"entity_subdomain" => $subdomain,
				"entity_begin" => $today,
				"creation_installer" => Session::get("installer_id"),
				"creation_time" => $time,
				"edition_installer" => Session::get("installer_id"),
				"installer_edition_time" => $time
			]);
		}
		$entity->set([
			"entity_name" => $request["entity_name"],
			"app_name" => empty($request["app_name"]) ? ucfirst($subdomain) : $request["app_name"],
			"entity_slogan" => $request["entity_slogan"],
			"edition_user" => Session::get("user_id") == null ? 0 : Session::get("user_id"),
			"user_edition_time" => $time
		]);
		$entity->save();

		# Creación de subdirectorios
		$dir = "entities/" . $subdomain . "/";
		if ($_SERVER["SERVER_NAME"] == $_SERVER["SERVER_ADDR"]) {
			$dir = "entities/local/";
		}
		if (!is_dir($dir)) {
			mkdir($dir, 0755, true);
		}

		#Logo
		if (!empty($_FILES["logo"]["name"])) {
			$extension = strtolower(pathinfo($_FILES["logo"]["name"], PATHINFO_EXTENSION));
			$file = $dir . "logo." . $extension;
			$generic_file = glob($dir . "logo.*");
			if (is_dir($dir)) {
				foreach ($generic_file as $previous) {
					unlink($previous);
				}
			}
			move_uploaded_file($_FILES["logo"]["tmp_name"], $file);
		}

		#Finish and response
		$reload = false;
		$redirect = null;
		$response += [
			"theme" => "green",
			"no_reset" => true
		];
		if ($_SERVER["SERVER_NAME"] != $_SERVER["SERVER_ADDR"]) {
			$protocol = "http";
			if ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) {
				$protocol .= "s";
			}
			$redirect = $protocol . "://" . str_replace("installer", $_POST["subdomain"], $_SERVER["SERVER_NAME"]) . "/Installation/RoleAndUser/";
			Session::destroy();
		} else {
			$reload = true;
		}
		ApiResponse::success(
			code: "UPDATED",
			title: _("Success"),
			message: _("Installation completed successfully"),
			actions: [
				"reset" => false,
				"reload" => $reload,
				"redirect" => $redirect
			]
		);
	}
}
