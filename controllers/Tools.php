<?php

foreach(glob("controllers/tools/*") as $file)
{
	include $file;
}

/**
 * Herramientas
 * 
 * Conjunto de herramientas administrativas del sistema:
 * - Papelera
 * - Respaldos
 * - Registro de actividades
 * 
 * Incorporado el 2023-01-04 10:54
 * @author Edwin Fajardo <contacto@edwinfajardo.com>
 * @link https://www.edwinfajardo.com
*/

class Tools extends Controller
{
	use Trash, ActivityLog;

	/**
	 * Constructor de la clase
	 * 
	 * Inicializa la propiedad module con el nombre de la clase
	 */
	public function __construct()
	{
		parent::__construct();
		$this->module = get_class($this);
		$this->view->data["module"] = $this->module;
	}

	################################ VISTAS
	/**
	 * Vista principal
	 * 
	 * Muestra un menú con las principales actividades que se pueden realizar en el módulo.
	 * 
	 * @return void
	 */
	public function index()
	{
		$this->session_required("html", $this->module);
		$this->view->standard_menu();
		$this->view->data["nav"] = $this->view->render("main/nav", true);
		$module = appModulesModel::findBy("module_url", $this->module);
		$this->view->data["title"] = _($module->getModuleName());
		$this->view->data["methods"] = availableMethodsModel::where("user_id", Session::get("user_id"))
		->where("module_id", $module->getModuleId())
		->orderBy("method_order")->getAllArray();
		$this->view->data["content"] = $this->view->render("generic_menu", true);
		$this->view->render("main");
	}

	################################ LISTAS Y FORMULARIOS

	public function load_form_data()
	{
		$this->json(Array());
	}
}
?>
