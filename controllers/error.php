<?php

#	Error controller
#	By: Edwin Fajardo
#	Date-time: 2017-09-12 14:00

class MainError extends Controller
{

	function __construct()
	{
		parent::__construct();
		$this->module = get_class($this);
	}
	
	function index() 
	{
		$this->view->data["title"] = 'Error';
		$this->view->add("styles", "css", Array(
			'external/css/jquery-ui.min.css',
			'external/css/jAlert.css',
			'external/css/zeroes.css',
			'styles/preloader.css',
			'styles/main.css'
			));
		$this->view->add("scripts", "js", Array(
			'external/js/jquery-3.2.1.min.js',
			'external/js/jquery-ui.min.js',
			'external/js/jAlert.min.js',
			'external/js/three.r92.min.js',
			'external/js/vanta.waves.min.js',
			'scripts/main.js'
			));
		$this->view->data["nav"] = $this->view->render("nav", true);
		$this->view->data["content"] = $this->view->render("error", true);
		$this->view->render('main');
	}
}
?>
