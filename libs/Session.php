<?php
/**
 * Manejo de la sesión
 * 
 * Esta clase proporciona funcionalidad sobre en manejo de la sesión.
 */
class Session
{
	/**
	 * Inicio de sesión
	 * 
	 * @return void
	 */
	public static function init()
	{
		@session_start();
	}
	
	/**
	 * Insertar valor
	 * 
	 * Inserta un valor de cualquier tipo dentro de una clave de la variable $_SESSION
	 * 
	 * @param string $key La clave de acceso al valor
	 * @param string $value El valor a insertar
	 * 
	 * @return void
	 */
	public static function set($key, $value)
	{
		$_SESSION[$key] = $value;
	}

	public static function unset($key)
	{
		unset($_SESSION[$key]);
	}
	
	/**
	 * Obtener valor
	 * 
	 * Devuelve un valor almacenado en la sesión, o null si no existe.
	 * 
	 * @param string $key La clave del valor solicitado, que puede estar expresada de manera simple o en multinivel separado
	 * por pleca. Por ejemplo:
	 * Session::get("user") equivale a $_SESSION["user"]
	 * Session::get("user/user_id") equivale a $_SESSION["user"]["user_id"]
	 * 
	 * @return mixed|null El valor solicitado, o null, si no existe.
	 */
	public static function get($key)
	{
		if(strpos($key, "/") !== false)
		{
			$indexes = explode("/", $key);
			$result = $_SESSION;
			foreach($indexes as $index)
			{
				if(!isset($result[$index]))
				{
					return null;
				}
				$result = $result[$index];
			}
			return $result;
		}
		if (isset($_SESSION[$key]))
		{
			return $_SESSION[$key];
		}
		return null;
	}

	/**
	 * Cerrar la sesión
	 */
	public static function destroy()
	{
		@session_destroy();
	}
}
?>
