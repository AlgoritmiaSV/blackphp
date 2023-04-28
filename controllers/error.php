<?php

/**
 * Controlador de error
 * 
 * La clase MainError consta de un sólo método, y se crea una instancia de la clase cada vez que
 * el módulo o el método solicitado por el usuario no exista.
 * 
 * Incorporado el Date-time: 2017-09-12 14:00
 */

class MainError extends Controller
{
	/**
	 * Constructor de la clase MainError
	 * 
	 * Inicializa el valor de la propiedad module con el nombre de la clase.
	 * 
	 * @return object
	 */
	function __construct()
	{
		parent::__construct();
		$this->module = get_class($this);
	}
	
	/**
	 * Vista principal
	 * 
	 * Muestra un error en caso de que no exista el módulo o el método que se está solicitando.
	 * 
	 * @return void
	 */
	function index() 
	{
		$this->view->data["title"] = 'Error';
		$this->view->standard_error();
		$this->view->data["nav"] = $this->view->render("main/nav", true);
		$this->view->data["content"] = $this->view->render("main/error", true);
		$this->view->render('main');
	}
}
?>
