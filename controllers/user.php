<?php

#	User controller
#	By: Edwin Fajardo
#	Date-time: 2020-6-12 23:55
use donatj\UserAgent\UserAgentParser;

class User extends Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->module = get_class($this);
	}

	public function index()
	{
	}

	public function test_login()
	{
		$data = Array("session" => false);
		if(empty($_POST["nickname"]))
		{
			$data["title"] = "Error";
			$data["message"] = _("Bad request");
			$data["theme"] = "red";
			echo json_encode($data);
			return;
		}
		$this->loadModel("user");
		$user = $this->model->get_access($_POST["nickname"], $_POST["password"], $this->entity_id);
		if(isset($user["nickname"]))
		{
			$data["reload"] = true;

			foreach ($user as $key => $value) {
				Session::set($key, $value);
			}
			if(!empty($user["locale"]))
			{
				Session::set("lang", explode("_", $user["locale"])[0]);
			}
			$now = Date("Y-m-d H:i:s");
			# Get user agent
			$user_agent = $_SERVER['HTTP_USER_AGENT'];
			# Get IP Address
			$ipv4 = $this->getRealIP();
			# Check if exists
			$browser = $this->model->get_browser($user_agent);
			$browser_id = 0;
			if(isset($browser["browser_id"]))
			{
				$browser_id = $browser["browser_id"];
			}
			else
			{
				#Set new browser
				//include "plugins/PhpUserAgent/src/UserAgentParser.php";
				//$browser_data = parse_user_agent();
				$parser = new UserAgentParser();
				$ua = $parser->parse($user_agent);
				$data_set = Array(
					"user_agent" => $user_agent,
					"browser_name" => $ua->browser(),
					"browser_version" => $ua->browserVersion(),
					"platform" => $ua->platform(),
					"creation_user" => $user["user_id"],
					"creation_time" => $now
				);
				$new_browser = $this->model->set_browser($data_set);
				$browser_id = $new_browser["id"];
			}
			#Set session
			$data_set = Array(
				"user_id" => $user["user_id"],
				"ip_address" => $ipv4,
				"browser_id" => $browser_id,
				"date_time" => $now
			);
			$this->model->set_user_session($data_set);
			#Set modules
			Session::set("modules", $this->model->get_user_entity_modules($this->entity_id, $user["user_id"]));
		}
		else
		{
			$data["title"] = "Error";
			$data["message"] = _("Bad user or password");
			$data["theme"] = "red";
		}
		echo json_encode($data);
	}

	public function logout()
	{
		$data = Array("session" => false);
		Session::destroy();
		echo json_encode($data);
	}

	public function get_session()
	{
		echo '<pre>' . print_r($_SESSION, true) . '</pre>';
	}
}
?>
