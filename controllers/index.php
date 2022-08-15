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
		$this->view->data["nav"] = $this->view->render("nav", true);
		$this->view->data["content_id"] = "home_content";
		$this->view->data["content"] = $this->view->render("content_loader", true);
		$this->view->render('main');
	}

	public function keep_alive()
	{
		$data = Array(
			"alive" => true
		);
		echo json_encode($data);
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
		$this->view->data["branch_name"] = Session::get("branch")["branch_name"];
		$this->view->data["entity_date"] = date_utilities::sql_date_to_string($entity["entity_date"]);
		Session::set("entity_date", $entity["entity_date"]);
		foreach($this->view->data["modules"] as $key => $module)
		{
			$this->view->data["module"] = $module["module_url"];
			$this->view->data["methods"] = available_methods_model::where("user_id", Session::get("user_id"))
			->where("module_id", $module["module_id"])
			->orderBy("method_order")->getAllArray();
			$this->view->data["modules"][$key]["module_menu"] = $this->view->render("generic_menu", true);
		}
		$this->view->render('home_content');
	}

	public function branch_filter_loader()
	{
		$data = Array();
		echo json_encode($data);
	}

	public function age_calculation($date)
	{
		$data = Array();
		if(!empty($date))
		{
			$data["age"] = date_utilities::sql_date_to_age($date);
		}
		echo json_encode($data);
	}
}
?>
