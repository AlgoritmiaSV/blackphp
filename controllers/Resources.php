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
		http::json([
			"alive" => Session::get("user_id") != null
		]);
	}

	public function age_calculation($date)
	{
		$data = array("age" => 0);
		if (!empty($date)) {
			$data["age"] = Dates::age($date);
		}
		http::json($data);
	}

	/**
	 * DataTables
	 * 
	 * Traducción de palabras utilizadas en DataTables; respuesta en formato JSON.
	 * 
	 * @return void
	 */
	/*
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
		http::json($data);
	}
	*/

	public function manifest()
	{
		$manifest = json_decode(file_get_contents("public/manifest.json"), true);
		$entity = Session::get("entity");
		if (!empty($entity["app_name"])) {
			$manifest["name"] = $entity["app_name"];
			$manifest["short_name"] = $entity["app_name"];
		}
		http::json($manifest);
	}

	function DownloadFile()
	{
		$filePath = $_GET["path"];
		if (!file_exists($filePath)) {
			header("HTTP/1.0 404 Not Found");
			exit("File not found.");
		}

		// Get file details
		$fileName = basename($filePath);
		$fileSize = filesize($filePath);

		// Set headers to force download
		header("Content-Description: File Transfer");
		header("Content-Type: application/octet-stream");
		header("Content-Disposition: attachment; filename=\"" . $fileName . "\"");
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: " . $fileSize);

		// Clean output buffer
		ob_clean();
		flush();

		// Read the file
		readfile($filePath);
		exit;
	}
}
?>