<?php

/**
 * Arranque del sistema
 * 
 * La clase bootstrap sirve para interpretar la URL (REQUEST_URI), y decidir qué controlador se va
 * a cargar, y de qué forma se van a interpretar los parámetros.
 */
class Bootstrap {

	/**
	 * @var string $_url La url solicitada por el cliente
	 */
	private $_url = null;

	/**
	 * @var string $_controller El controlador slicitado en la URL
	*/
	private $_controller = null;
	
	/**
	 * @var string $_controllerPath La ruta donde están ubicados los controladores en el sistema
	 * Se debe incluir siempre la barra / al final
	 */
	private $_controllerPath = 'controllers/';

	/**
	 * @var string $_modelPath La ruta donde se encuentran los modelos
	 * Se debe incluir siempre la barra / al final
	 */
	private $_modelPath = 'models/';

	/**
	 * @var string $_errorFile El nombre del fichero con la clase o las clases que manejarán los errores.
	 */
	private $_errorFile = 'error.php';

	/**
	 * @var string $_defaultFile El nombre del archivo a cargar por defecto en caso de que no se llame
	 * explícitamente a un controlador.
	 */
	private $_defaultFile = 'index.php';
	
	/**
	 * Iniciar
	 * 
	 * Inicia el proceso de arranque, llamando a los métodos correspondientes para cargar el controlador
	 * y llamar al método correspondiente.
	 * 
	 * @return boolean
	 */
	public function init()
	{
		$this->_getUrl();
		if (empty($this->_url[0])) {
			$this->_loadDefaultController();
			return false;
		}
		$this->_loadExistingController();
		$this->_callControllerMethod();
	}
	
	/**
	 * (Opcional) Introducir una ruta personalizada para los controladores
	 * 
	 * @param string $path Ubicación de los controladores
	 * 
	 * @return void
	 */
	public function setControllerPath($path)
	{
		$this->_controllerPath = trim($path, '/') . '/';
	}
	
	/**
	 * (Optional) Introduci una ruta personalizada para los modelos
	 * 
	 * @param string $path Ubicación de los modelos
	 * 
	 * @return void
	 */
	public function setModelPath($path)
	{
		$this->_modelPath = trim($path, '/') . '/';
	}
	
	/**
	 * (Optional) Ruta personalizada para el archivo de error
	 * 
	 * @param string $path Nombre del archivo controlador personalizado
	 * 
	 * @return void
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
		$url = isset($_GET['url']) ? $_GET['url'] : "";
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
