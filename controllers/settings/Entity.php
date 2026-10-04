<?php
trait Entity
{
	/**
	 * Datos de la entidad
	 * 
	 * Muestra un formulario para modificar información general de la entidad, como el nombre,
	 * dirección, logotipo y formas de contacto.
	 * 
	 * @return void
	 */
	public function Entity()
	{
		$this->check_permissions("read", "entityData");
		$this->view->data["title"] = _("Entity data");
		$this->view->standard_form();
		$this->view->data["nav"] = $this->view->render("main/nav", true);
		$this->view->data["content"] = $this->view->render("settings/entity", true);
		$this->view->render('main');
	}

	/**
	 * Guardar entidad
	 * 
	 * Guarda cambios realizados en los datos de la entidad.
	 * 
	 * @return void
	 */
	public function save_entity()
	{
		$request = http::getRequestData();
		$this->check_permissions("update", "entityData");

		if (empty($request["entity_name"])) {
			ApiResponse::error(
				code: "REQUIRED_FIELD",
				title: _("Error"),
				message: _("Bad request")
			);
		}

		# Guardar la entidad
		$time = Date("Y-m-d H:i:s");
		$entity = entitiesModel::get();
		$entity->set(array(
			"entity_name" => $request["entity_name"],
			"entity_slogan" => $request["entity_slogan"],
			"edition_user" => Session::get("user_id"),
			"user_edition_time" => $time
		))->save();
		Session::set("entity", $entity->toArray());

		# Guardar imagen
		if (!empty($_FILES["logo"]["name"])) {
			$extension = strtolower(pathinfo($_FILES["logo"]["name"], PATHINFO_EXTENSION));
			if ($_SERVER["SERVER_NAME"] == $_SERVER["SERVER_ADDR"]) {
				$dir = "entities/local/";
			} else {
				$dir = "entities/" . Session::get("entity/entity_subdomain") . "/";
			}
			$file = $dir . "logo." . $extension;
			$generic_file = glob($dir . "logo.*");
			if (!is_dir($dir)) {
				mkdir($dir, 0755, true);
			} else {
				foreach ($generic_file as $previous) {
					unlink($previous);
				}
			}
			move_uploaded_file($_FILES["logo"]["tmp_name"], $file);
		}
		$this->setUserLog("update", "entityData");

		ApiResponse::success(
			code: "UPDATED",
			title: _("Success"),
			message: _("Changes have been saved"),
			actions: ["reset" => false]
		);
	}
}
