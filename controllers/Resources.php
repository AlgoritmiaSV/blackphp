<?php
/**
 * Recursos
 * 
 * Conjunto de métodos utilitarios para ser enviados al cliente.
 * 
 * Incorporado el 2022-08-27 22:24
 */
class Resources extends Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->module = get_class($this);
	}

	public function index()
	{
		$this->view->data["title"] = _("Not authorized");
		$this->view->standard_error();
		$this->view->data["nav"] = $this->view->render("main/nav", true);
		$this->view->data["content"] = $this->view->render("main/forbidden", true);
		$this->view->render('main');
	}

	public function keep_alive()
	{
		$this->json(Array(
			"alive" => true
		));
	}

	public function age_calculation($date)
	{
		$data = Array("age" => 0);
		if(!empty($date))
		{
			$data["age"] = date_utilities::sql_date_to_age($date);
		}
		$this->json($data);
	}

	/**
	 * DataTables
	 * 
	 * Traducción de palabras utilizadas en DataTables; respuesta en formato JSON.
	 * 
	 * @return void
	 */
	public function datatables_language()
	{
		$data = Array(
			"decimal" => "",
			"emptyTable" => _("No data available in table"),
			"info" => _("Showing _START_ to _END_ of _TOTAL_ entries"),
			"infoEmpty" => _("Showing 0 to 0 of 0 entries"),
			"infoFiltered" => _("filtered from _MAX_ total entries"),
			"infoPostFix" => "",
			"thousands" => ",",
			"lengthMenu" => _("Show _MENU_ entries"),
			"loadingRecords" => _("Loading") . "...",
			"processing" => "",
			"search" => _("Search") . ":",
			"zeroRecords" => _("No matching records found"),
			"paginate" => Array(
				"first" => _("First"),
				"last" => _("Last"),
				"next" => _("Next"),
				"previous" => _("Previous")
			),
			"aria" => Array(
				"sortAscending" => ": " . _("activate to sort column ascending"),
				"sortDescending" => ": " . _("activate to sort column descending")
			)
		);
		$this->json($data);
	}

	public function manifest()
	{
		$manifest = json_decode(file_get_contents("public/manifest.json"), true);
		$entity = Session::get("entity");
		if(!empty($entity["app_name"]))
		{
			$manifest["name"] = $entity["app_name"];
			$manifest["short_name"] = $entity["app_name"];
		}
		$this->json($manifest);
	}
}
?>
