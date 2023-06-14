<?php

#	Index/Session controller
#	By: Edwin Fajardo
#	Date-time: 2017-09-12 14:00

class Index extends Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->module = get_class($this);
	}

	public function index()
	{
		$this->session_required();
		$this->view->data["title"] = _("Home");
		$this->view->standard_menu();
		$this->view->data["nav"] = $this->view->render("main/nav", true);
		$this->view->data["content_id"] = "home_content";
		$this->view->data["content"] = $this->view->render("content_loader", true);
		$this->view->render('main');
	}

	public function home_content_loader()
	{
		$entity = Session::get("entity");
		$this->view->restrict[] = "standalone";
		$this->view->data["real_date"] = date_utilities::sql_date_to_string(Date("Y-m-d"));
		foreach($entity as $key => $value)
		{
			$this->view->data[$key] = $value;
		}
		$this->view->data["entity_date"] = date_utilities::sql_date_to_string($entity["entity_date"]);
		Session::set("entity_date", $entity["entity_date"]);
		foreach($this->view->data["modules"] as $key => $module)
		{
			$this->view->data["module"] = $module["module_url"];
			$this->view->data["methods"] = availableMethodsModel::where("user_id", Session::get("user_id"))
			->where("module_id", $module["module_id"])
			->orderBy("method_order")->getAllArray();
			$this->view->data["modules"][$key]["module_menu"] = $this->view->render("generic_menu", true);
		}
		$this->view->render('home_content');
	}

	public function branch_filter_loader()
	{
		$data = Array();
		$this->json($data);
	}

	/**
	 * Carga de contactos para soporte técnico
	 * 
	 * Imprime, en formato HTML, los íconos para acceso a soporte técnico previamente
	 * configurados en app_info.json.
	 * 
	 * @return void
	 */
	public function technical_support_loader($mode = "embedded")
	{
		if(file_exists("app_info.json"))
		{
			$info = json_decode(file_get_contents("app_info.json"), true);
			$this->view->data["contacts"] = $info["technical_support"];
		}
		if($mode == "standalone")
		{
			$this->view->data["title"] = _("Technical support");
			$this->view->standard_menu();
			$this->view->add("styles", "css", Array(
				'styles/standalone.css'
			));
			$this->view->restrict[] = "embedded";
			$this->view->data["content"] = $this->view->render('main/technical_support', true);
			$this->view->render('clean_main');
		}
		else
		{
			$this->view->render('main/technical_support');
		}
	}
}
?>
