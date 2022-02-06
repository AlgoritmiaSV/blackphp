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
		$this->view->data["title"] = 'Inicio';
		$this->view->add("styles", "css", Array(
			'external/css/jquery-ui.min.css',
			'external/css/jAlert.css',
			'styles/preloader.css',
			'styles/main.css',
			'styles/loading.css',
			'styles/menu.css'
		));
		$this->view->add("scripts", "js", Array(
			'external/js/jquery-3.2.1.min.js',
			'external/js/jquery-ui.min.js',
			'external/js/jAlert.min.js',
			'scripts/main.js',
			'scripts/lists.js'
		));
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
		$this->loadModel("entity");
		$company = Session::get("entity");
		$this->view->data["real_date"] = date_utilities::sql_date_to_string(Date("Y-m-d"));
		foreach($company as $key => $value)
		{
			$this->view->data[$key] = $value;
		}
		$this->view->data["branch_name"] = Session::get("branch")["branch_name"];
		$this->view->data["comp_date"] = date_utilities::sql_date_to_string($company["comp_date"]);
		Session::set("comp_date", $company["comp_date"]);
		foreach($this->view->data["modules"] as $key => $module)
		{
			$this->view->data["module"] = $module["module_url"];
			$this->view->data["methods"] = $this->model->get_entity_methods($this->entity_id, $module["module_id"]);
			$this->view->data["modules"][$key]["module_menu"] = $this->view->render("generic_menu", true);
		}
		$this->view->render('home_content');
	}

	function user_agent()
	{
		include "plugins/PhpUserAgent/src/UserAgentParser.php";
		print_r(parse_user_agent());
	}

	public function branch_filter_loader()
	{
		$data = Array();
		/*$this->loadModel("company");
		if($_SERVER["SERVER_NAME"] == $_SERVER["SERVER_ADDR"])
		{
			$data["results"] = $this->model->get_branches_by_company(1);
		}
		else
		{
			$subdomain = explode(".", $_SERVER["SERVER_NAME"])[0];
			$data["results"] = $this->model->get_branches_by_subdomain($subdomain);
		}*/
		echo json_encode($data);
	}

	public function test_login()
	{
		$user = new user();
		$user->test_login();
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
