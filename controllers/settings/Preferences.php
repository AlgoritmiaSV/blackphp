<?php
trait Preferences
{
	/**
	 * Preferencias del sistema
	 * 
	 * Muestra un formulario para cambiar datos opcionales en el sistema
	 * 
	 * @return void
	 */
	public function Preferences()
	{
		$this->check_permissions("read", "preferences");
		$this->view->data["title"] = _("Preferences");
		$this->view->standard_form();
		$this->view->data["nav"] = $this->view->render("main/nav", true);
		$this->view->data["config_modules"] = Array();
		foreach($this->view->data["modules"] as $key => $module)
		{
			$switches = entityOptionsModel::join("app_options", "option_id")->where("module_id", $module["module_id"])->where("option_type", 1)->getAllArray();
			$fields = entityOptionsModel::join("app_options", "option_id")->where("module_id", $module["module_id"])->where("option_type", 2)->getAllArray();
			$selectors = entityOptionsModel::join("app_options", "option_id")->where("module_id", $module["module_id"])->where("option_type", 3)->getAllArray();
			$numbers = entityOptionsModel::join("app_options", "option_id")->where("module_id", $module["module_id"])->where("option_type", 4)->getAllArray();
			if(count($switches) + count($fields) + count($selectors) + count($numbers) > 0)
			{
				$this->view->data["switches"] = $switches;
				$this->view->data["fields"] = $fields;
				$this->view->data["selectors"] = $selectors;
				$this->view->data["numbers"] = $numbers;
				$this->view->data["config_modules"][] = Array(
					"module_name" => $module["module_name"],
					"preferences" => $this->view->render("settings/preference_item", true)
				);
			}
		}
		$this->view->data["content"] = $this->view->render("settings/preferences", true);
		$this->view->render('main');
	}

	/**
	 * Guardar preferencias
	 * 
	 * Guarda las preferencias del usuario con los datros recibidos del formulario de preferencias.
	 * 
	 * @return void
	 */
	public function save_preferences()
	{
		$this->check_permissions("update", "preferences");
		$data = $_POST;
		$data["success"] = false;
		$app_options = appOptionsModel::select("option_id")->where("option_type", 1)->getAll();
		entityOptionsModel::whereIn(array_column($app_options, "option_id"), "option_id")->update(Array("option_value" => 0));
		foreach($_POST as $key => $value)
		{
			$option = appOptionsModel::where("option_key", $key)->get();
			$entity_option = $option->entityOptions()->get();
			$entity_option->setOptionValue($value);
			$entity_option->save();
		}
		$this->setUserLog("update", "preferences");

		$option_list = entityOptionsModel::select("option_key", "option_value")->join("app_options", "option_id")->getAll();
		$options = Array();
		foreach($option_list as $item)
		{
			$options[$item["option_key"]] = $item["option_value"];
		}
		Session::set("options", $options);

		$data["success"] = true;
		$data["title"] = _("Success");
		$data["message"] = _("Changes have been saved");
		$data["theme"] = "green";
		$data["no_reset"] = true;
		$this->json($data);
	}
}
?>
