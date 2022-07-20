<?php
/**
 * Model for app_modules
 * 
 * Generated by BlackPHP
 */

class app_modules_model
{
	use ORM;

	/** @var int $module_id ID de la tabla */
	private $module_id;

	/** @var string $module_name Nombre del módulo */
	private $module_name;

	/** @var string $module_url URL del módulo */
	private $module_url;

	/** @var string $module_key Tecla de acceso rápido */
	private $module_key;

	/** @var string $module_description Descripción del módulo */
	private $module_description;

	/** @var int $default_order Orden por defecto */
	private $default_order;

	/** @var int $status Estado 0:inactivo, 1:activo */
	private $status;


	/** @var string $_table_name Nombre de la tabla */
	private static $_table_name = "app_modules";

	/** @var string $_primary_key Llave primaria */
	private static $_primary_key = "module_id";

	/** @var bool $_timestamps La tabla usa marcas de tiempo para la inserción y edición de datos */
	private static $_timestamps = false;

	/** @var bool $_soft_delete La tabla soporta borrado suave */
	private static $_soft_delete = true;

	/**
	 * Constructor de la clase
	 * 
	 * Se inicializan las propiedades con los valores de los campos default
	 * de la base de datos
	 **/
	public function __construct()
	{
		$this->status = 1;
	}

	public function getModule_id()
	{
		return $this->module_id;
	}

	public function setModule_id($value)
	{
		$this->module_id = $value === null ? null : (int)$value;
	}

	public function getModule_name()
	{
		return $this->module_name;
	}

	public function setModule_name($value)
	{
		$this->module_name = $value === null ? null : (string)$value;
	}

	public function getModule_url()
	{
		return $this->module_url;
	}

	public function setModule_url($value)
	{
		$this->module_url = $value === null ? null : (string)$value;
	}

	public function getModule_key()
	{
		return $this->module_key;
	}

	public function setModule_key($value)
	{
		$this->module_key = $value === null ? null : (string)$value;
	}

	public function getModule_description()
	{
		return $this->module_description;
	}

	public function setModule_description($value)
	{
		$this->module_description = $value === null ? null : (string)$value;
	}

	public function getDefault_order()
	{
		return $this->default_order;
	}

	public function setDefault_order($value)
	{
		$this->default_order = $value === null ? null : (int)$value;
	}

	public function getStatus()
	{
		return $this->status;
	}

	public function setStatus($value)
	{
		$this->status = $value === null ? null : (int)$value;
	}

	public function app_elements()
	{
		app_elements::flush();
		return app_elements::where("module_id", $this->module_id);
	}

	public function app_methods()
	{
		app_methods::flush();
		return app_methods::where("module_id", $this->module_id);
	}

	public function entity_modules()
	{
		entity_modules::flush();
		return entity_modules::where("module_id", $this->module_id);
	}

	public function user_modules()
	{
		user_modules::flush();
		return user_modules::where("module_id", $this->module_id);
	}
}
?>
