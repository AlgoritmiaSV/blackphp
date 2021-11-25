<?php

class Bootstrap {

	private $_url = null;
	private $_controller = null;
	
	private $_controllerPath = 'controllers/'; // Always include trailing slash
	private $_modelPath = 'models/'; // Always include trailing slash
	private $_errorFile = 'error.php';
	private $_defaultFile = 'index.php';
	
	/**
	 * Starts the Bootstrap
	 * 
	 * @return boolean
	 */
	public function init()
	{
		// Sets the protected $_url
		$this->_getUrl();

		// Load the default controller if no URL is set
		// eg: Visit http://localhost it loads Default Controller
		if (empty($this->_url[0])) {
			$this->_loadDefaultController();
			return false;
		}

		$this->_loadExistingController();
		$this->_callControllerMethod();
	}
	
	/**
	 * (Optional) Set a custom path to controllers
	 * @param string $path
	 */
	public function setControllerPath($path)
	{
		$this->_controllerPath = trim($path, '/') . '/';
	}
	
	/**
	 * (Optional) Set a custom path to models
	 * @param string $path
	 */
	public function setModelPath($path)
	{
		$this->_modelPath = trim($path, '/') . '/';
	}
	
	/**
	 * (Optional) Set a custom path to the error file
	 * @param string $path Use the file name of your controller, eg: error.php
	 */
	public function setErrorFile($path)
	{
		$this->_errorFile = trim($path, '/');
	}
	
	/**
	 * (Optional) Set a custom path to the error file
	 * @param string $path Use the file name of your controller, eg: index.php
	 */
	public function setDefaultFile($path)
	{
		$this->_defaultFile = trim($path, '/');
	}
	
	/**
	* Fetches the $_GET from 'url'
	*/
	private function _getUrl()
	{
		$url = isset($_GET['url']) ? $_GET['url'] : null;
		$url = rtrim($url, '/');
		$url = filter_var($url, FILTER_SANITIZE_URL);
		$this->_url = explode('/', $url);
	}
	
	/**
	* This loads if there is no GET parameter passed
	*/
	private function _loadDefaultController()
	{
		require $this->_controllerPath . $this->_defaultFile;
		$this->_controller = new Index();
		$this->_controller->index();
	}
	
	/**
	* Load an existing controller if there IS a GET parameter passed
	*
	* @return boolean|string
	*/
	private function _loadExistingController()
	{
		$file = $this->_controllerPath . $this->_url[0] . '.php';
		
		if (file_exists($file)) {
			require $file;
			$this->_controller = new $this->_url[0];
			$this->_controller->loadModel($this->_url[0], $this->_modelPath);
		} else {
			$this->_error();
			return false;
		}
	}
	
	/**
	* If a method is passed in the GET url paremter
	* 
	*  http://localhost/controller/method/ Default index
	*  http://localhost/controller/method/id
	*  http://localhost/controller/method/(param)/(value)/(param)/(value)...
	*/
	private function _callControllerMethod()
	{
		$length = count($this->_url);
		
		// Make sure the method we are calling exists
		if ($length > 1) {
			if (!method_exists($this->_controller, $this->_url[1])) {
				$this->_error();
			}
		}
		
		// Determine what to load
		#	Modified by: Edwin Fajardo 2017-09-23 22:30
		$parameters = Array();
		for($i = 2; $i < count($this->_url); $i++)
		{
			$parameters[] = $this->_url[$i];
		}
		if(count($this->_url) < 2)
		{
			$this->_url[1] = "index";
		}
		call_user_func_array(Array($this->_controller, $this->_url[1]), $parameters);
	}
	
	/**
	 * Display an error page if nothing exists
	 * 
	 * @return boolean
	 */
	private function _error()
	{
		require_once $this->_controllerPath . $this->_errorFile;
		$this->_controller = new MainError();
		$this->_controller->index();
		exit;
	}
}
