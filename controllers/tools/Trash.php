<?php
trait Trash
{
	/**
	 * Papelera
	 * 
	 * Muestra una lista de los elementos eliminados del sistema, que
	 * se pueden filtrar por tipo de elemento y rango de fechas.
	 * 
	 * @return void
	 */
	public function Trash()
	{
		$this->check_permissions("read", "trash");
		$this->view->standard_list();
		$this->view->set([
			"title" => _("Trash"),
			"print_title" => _("Trash")
		]);
		$this->view->data["nav"] = $this->view->render("main/nav", true);
		$this->view->data["print_header"] = $this->view->render("print_header", true);
		$args = func_get_args();
		$options = Array();
		for($i = 1; $i < func_num_args(); $i = $i + 2)
		{
			$options[$args[$i - 1]] = $args[$i];
		}
		if(empty($options["type"]))
		{
			$this->view->data["content"] = $this->view->render("tools/trash_select", true);
		}
		else
		{
			if(!empty($options["from"]))
			{
				$this->view->data["from"] = implode("/", array_reverse(explode("-", $options["from"])));
			}
			if(!empty($options["to"]))
			{
				$this->view->data["to"] = implode("/", array_reverse(explode("-", $options["to"])));
			}
			$this->view->data["content"] = $this->view->render("tools/trash_list", true);
		}
		$this->view->render('main');
	}

	/**
	 * Cargar tabla de elementos eliminados
	 * 
	 * Devuelve, en formato JSON o en un archivo Excel, la lista de usuarios.
	 * @param string $response El modo de respuesta (JSON o Excel)
	 * 
	 * @return void
	 */
	public function trash_table_loader($response = "JSON")
	{
		$this->check_permissions("read", "trash");
		$title = "";
		$items = Array();
		$type = $_POST["options"]["type"];
		$from = empty($_POST["options"]["from"]) ? "" : $_POST["options"]["from"];
		$to = empty($_POST["options"]["to"]) ? "" : $_POST["options"]["to"];
		$element = appElementsModel::findBy("element_key", $type);
		$element_name = _($element->getElementName());
		$title = sprintf(_("Deleted %s"), $element_name);
		$table_name = $element->getTableName();
		if(strlen(DB_PREFIX) > 0)
		{
			$table_name = substr($table_name, strlen(DB_PREFIX));
		}
		$model = lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $table_name)))) . "Model";
		$query = new $model;
		$items = $query->list_deleted($from, $to);
		foreach($items as &$item)
		{
			$item["creation_time"] = Date("d/m/Y h:ia", strtotime($item["creation_time"]));
			$item["edition_time"] = Date("d/m/Y h:ia", strtotime($item["edition_time"]));
		}
		switch($type)
		{
			case "users":
				foreach($items as &$item)
				{
					$item["description"] = $item["nickname"] . ": " . $item["user_name"];
				}
				break;
			default:
				break;
		}
		unset($item);

		$data["content"] = $items;
		if($response == "Excel")
		{
			$data["title"] = $title;
			$data["headers"] = Array(_("Element ID"), _("Description"), _("Created by"), _("Created at"), _("Deleted by"), _("Deleted at"));
			$data["fields"] = Array("element_id", "description", "creator_name", "creation_time", "editor_name", "edition_time");
			excel::create_from_table($data, "Trash_" . Date("YmdHis") . ".xlsx");
		}
		else
		{
			$this->json($data);
		}
	}

	/**
	 * Filtro de papelera
	 * 
	 * Imprime, en formato JSON, una lista de los elementos que se pueden seleccionar desde la papelera.
	 * 
	 * @return void
	*/
	public function trash_filter_loader()
	{
		$this->check_permissions("read", "trash");
		$elements = appElementsModel::where("is_deletable", 1)->list("element_key", "element_name");
		foreach($elements as &$element)
		{
			$element["text"] = _($element["text"]);
		}
		unset($element);
		usort($elements, function($a, $b) {
			return strcmp($a["text"], $b["text"]);
		});
		$this->json(Array(
			"results" => $elements
		));
	}
}
?>
