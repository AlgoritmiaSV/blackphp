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
		$this->check_permissions("update", "entityData");
		$data = Array("success" => false);
		if(!empty($_POST["entity_name"]))
		{
			$time = Date("Y-m-d H:i:s");
			$entity = entitiesModel::find($this->entity_id);
			$entity->set(Array(
				"entity_name" => $_POST["entity_name"],
				"entity_slogan" => $_POST["entity_slogan"],
				"edition_user" => Session::get("user_id"),
				"user_edition_time" => $time
			))->save();
			Session::set("entity", $entity->toArray());
			$data["success"] = true;
			$data["title"] = _("Success");
			$data["message"] = _("Changes have been saved");
			$data["theme"] = "green";
			$data["no_reset"] = true;

			#Save image
			if(!empty($_FILES["logo"]["name"]))
			{
				$extension = strtolower(pathinfo($_FILES["logo"]["name"], PATHINFO_EXTENSION));
				if($_SERVER["SERVER_NAME"] == $_SERVER["SERVER_ADDR"])
				{
					$dir = "entities/local/";
				}
				else
				{
					$dir = "entities/" . $this->entity_subdomain . "/";
				}
				$file = $dir . "logo." . $extension;
				$generic_file = glob($dir . "logo.*");
				if(!is_dir($dir))
				{
					mkdir($dir, 0755, true);
				}
				else
				{
					foreach($generic_file as $previous)
					{
						unlink($previous);
					}
				}
				move_uploaded_file($_FILES["logo"]["tmp_name"], $file);
			}
			$this->setUserLog("update", "entityData");
		}
		$this->json($data);
	}
}
?>
