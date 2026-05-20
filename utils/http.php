<?php
#	http: Http request params
#	By: Edwin Fajardo
#	Date-Time: 2016-03-04 23:02:00

class http
{
	public static function get_param($name, $method = "get")
	{
		switch($method)
		{
			case "get":
				if(isset($_GET[$name])) return (string)$_GET[$name];
				break;
			case "post":
				if(isset($_POST[$name])) return (string)$_POST[$name];
				break;
			case "request":
				if(isset($_REQUEST[$name])) return (string)$_REQUEST[$name];
				break;
			case "server":
				if(isset($_SERVER[$name])) return (string)$_SERVER[$name];
				break;
			case "session":
				if(isset($_SESSION[$name])) return (string)$_SESSION[$name];
				break;
		}
		return null;
	}
	
	public static function set_param($name, $value, $method = "session")
	{
		switch($method)
		{
			case "session":
				$_SESSION[$name] = $value;
				break;
		}
	}

	public static function unset_param($name, $method = "session")
	{
		switch($method)
		{
			case "session":
				unset($_SESSION[$name]);
				break;
			case "post":
				unset($_POST[$name]);
				break;
		}
	}

	public static function get_post_params($required)
	{
		$results = Array();
		if(count($_POST) > 0)
		{
			foreach($required as $item)
			{
				if(!isset($_POST[$item]))
				{
					return null;
				}
				$results[$item] = $_POST[$item];
			}
		}
		return $results;
	}

	public static function getRequestData()
	{
		// Start with normal form-data
		$data = $_POST;

		// Read raw body
		$rawBody = file_get_contents("php://input");
		if ($rawBody) {
			$jsonData = json_decode($rawBody, true);
			if (is_array($jsonData)) {
				// Merge JSON into $_POST-style array
				$data = array_merge($data, $jsonData);
			}
		}

		return $data;
	}

	/**
	 * Imprimir un array en formato JSON
	 * 
	 * Convierte todos los valores nulos a cadenas vacías, y luego, imprime el resultado en formato
	 * JSON
	 * @param array $data Arreglo de datos a imprimir
	 * 
	 * @return void
	 */
	public static function json($data)
	{
		array_walk_recursive($data, function(&$item)
		{
			$item = $item === null ? "" : $item;
		});
		header('Content-type: application/json');
		echo json_encode($data);
	}
}
?>
