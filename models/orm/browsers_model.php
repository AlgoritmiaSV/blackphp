<?php
/**
 * Model for browsers
 * 
 * Generated by BlackPHP
 */

class browsers_model
{
	use ORM;

	/** @var int $browser_id ID de la tabla */
	private $browser_id;

	/** @var string $user_agent Cadena completa User Agent enviada por el navegador */
	private $user_agent;

	/** @var string $browser_name Nombre del navegador */
	private $browser_name;

	/** @var string $browser_version Versión del navegador */
	private $browser_version;

	/** @var string $platform Sistema operativo */
	private $platform;

	/** @var int $creation_user Primer usuario que lo registra */
	private $creation_user;

	/** @var string $creation_time Hora y fecha de registro */
	private $creation_time;


	/** @var string $_table_name Nombre de la tabla */
	private static $_table_name = "browsers";

	/** @var string $_primary_key Llave primaria */
	private static $_primary_key = "browser_id";

	/** @var bool $_timestamps La tabla usa marcas de tiempo para la inserción y edición de datos */
	private static $_timestamps = false;

	/** @var bool $_soft_delete La tabla soporta borrado suave */
	private static $_soft_delete = false;

	/**
	 * Constructor de la clase
	 * 
	 * Se inicializan las propiedades con los valores de los campos default
	 * de la base de datos
	 **/
	public function __construct()
	{
	}

	public function getBrowser_id()
	{
		return $this->browser_id;
	}

	public function setBrowser_id($value)
	{
		$this->browser_id = $value === null ? null : (int)$value;
	}

	public function getUser_agent()
	{
		return $this->user_agent;
	}

	public function setUser_agent($value)
	{
		$this->user_agent = $value === null ? null : (string)$value;
	}

	public function getBrowser_name()
	{
		return $this->browser_name;
	}

	public function setBrowser_name($value)
	{
		$this->browser_name = $value === null ? null : (string)$value;
	}

	public function getBrowser_version()
	{
		return $this->browser_version;
	}

	public function setBrowser_version($value)
	{
		$this->browser_version = $value === null ? null : (string)$value;
	}

	public function getPlatform()
	{
		return $this->platform;
	}

	public function setPlatform($value)
	{
		$this->platform = $value === null ? null : (string)$value;
	}

	public function getCreation_user()
	{
		return $this->creation_user;
	}

	public function setCreation_user($value)
	{
		$this->creation_user = $value === null ? null : (int)$value;
	}

	public function getCreation_time()
	{
		return $this->creation_time;
	}

	public function setCreation_time($value)
	{
		$this->creation_time = $value === null ? null : (string)$value;
	}

	public function user_sessions()
	{
		user_sessions::flush();
		return user_sessions::where("browser_id", $this->browser_id);
	}
}
?>
