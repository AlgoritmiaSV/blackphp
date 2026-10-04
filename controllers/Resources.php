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

	public function KeepAlive()
	{
		ApiResponse::success(data: [
			"alive" => Session::get("user_id") != null
		]);
	}

	public function AgeCalculation($date)
	{
		$age = 0;
		if (!empty($date)) {
			$age = Dates::age($date);
		}
		ApiResponse::success(data: [
			"age" => $age
		]);
	}

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
