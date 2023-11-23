<?php
trait ActivityLog
{
	/**
	 * Registro de actividades
	 * 
	 * Muestra una lista de las actividades realizadas por los usuarios en un rango de fechas.
	 * 
	 * @return void
	 */
	public function ActivityLog()
	{
		$this->check_permissions("read", "logs");
		$args = func_get_args();
		$options = Array();
		for($i = 1; $i < func_num_args(); $i = $i + 2)
		{
			$options[$args[$i - 1]] = $args[$i];
		}
		if(empty($options["from"]) || empty($options["to"]))
		{
			$date = Date("Y-m-d");
			header("Location: /" . $this->module . "/" . __FUNCTION__ . "/from/$date/to/$date/");
			return;
		}
		$this->view->standard_list();
		$this->view->set([
			"title" => _("Activity log"),
			"print_title" => _("Activity log")
		]);
		$this->view->data["nav"] = $this->view->render("main/nav", true);
		$this->view->data["print_header"] = $this->view->render("print_header", true);
		$this->view->data["from"] = implode("/", array_reverse(explode("-", $options["from"])));
		$this->view->data["to"] = implode("/", array_reverse(explode("-", $options["to"])));
		$this->view->data["content"] = $this->view->render("tools/activity_log", true);
		$this->view->render('main');
	}

	/**
	 * Cargar tabla de registro de actividades
	 * 
	 * Devuelve, en formato JSON o en un archivo Excel, la lista de actividades realizadas por
	 * los usuaios.
	 * @param string $response El modo de respuesta (JSON o Excel)
	 * 
	 * @return void
	 */
	public function log_table_loader($response = "JSON")
	{
		$this->check_permissions("read", "logs");
		$title = "";
		$items = Array();
		$type = $_POST["options"]["type"];
		$from = $_POST["options"]["from"] ?? Date("Y-m-d");
		$from .= " 00:00:00";
		$to = $_POST["options"]["to"] ?? Date("Y-m-d");
		$to .= " 23:59:59";
		$items = userLogsModel::select(userLogsModel::fields("*"), usersModel::fields("user_name"), appElementsModel::fields("singular_name", "element_gender", "unique_element"))
			->join("users", "user_id")
			->join("app_elements", "element_id")
			->where("date_time", ">=", $from)
			->where("date_time", "<=", $to)
			->orderBy("date_time", "DESC")
			->getAll();
		$actions = [
			1 => _("deleted"),
			2 => _("updated"),
			4 => _("created")
		];
		foreach($items as &$item)
		{
			$article = "";
			if($item["unique_element"] == 0)
			{
				$first_character = substr($item["singular_name"], 0, 1);
				if(in_array($first_character, ["a", "e", "i", "o", "u"]))
				{
					if($item["element_gender"] == 'F')
					{
						$article = _("femalean");
					}
					else
					{
						$article = _("malean");
					}
				}
				else
				{
					if($item["element_gender"] == 'F')
					{
						$article = _("femalea");
					}
					else
					{
						$article = _("malea");
					}
				}
				$article .= " ";
			}
			$item["date_time"] = Date("d/m/Y h:ia", strtotime($item["date_time"]));
			$item["description"] = $item["user_name"] . " " . $actions[$item["action_id"]] . " " . $article . _($item["singular_name"]) . ".";
		}
		unset($item);

		$data["content"] = $items;
		if($response == "Excel")
		{
			$data["title"] = $title;
			$data["headers"] = Array(_("Date and time"), _("Activity description"));
			$data["fields"] = Array("date_time", "description");
			excel::create_from_table($data, "Activity_log_" . Date("YmdHis") . ".xlsx");
		}
		else
		{
			$this->json($data);
		}
	}
}
?>
