<?php
/**
 * Model for app_options
 * 
 * Generated by BlackPHP
 */

class app_options_model
{
	use ORM;

	/** @var int $option_id Llave primaria */
	private $option_id;

	/** @var int $option_type Tipo de variable: 1: Booleana; 2: Valor */
	private $option_type;

	/** @var string $option_key Clave de la opción */
	private $option_key;

	/** @var string $option_description Descripción de la opción */
	private $option_description;

	/** @var int $module_id Módulo en el que se realiza la configuración */
	private $module_id;

	/** @var string $default_value Valor por defecto de la opción */
	private $default_value;


	/** @var string $_table_name Nombre de la tabla */
	private static $_table_name = "app_options";

	/** @var string $_table_type Tipo de tabla */
	private static $_table_type = "BASE TABLE";

	/** @var string $_primary_key Llave primaria */
	private static $_primary_key = "option_id";

	/** @var bool $_timestamps La tabla usa marcas de tiempo para la inserción y edición de datos */
	private static $_timestamps = false;

	/** @var bool $_soft_delete La tabla soporta borrado suave */
	private static $_soft_delete = false;

	/** @var int|null $_deleted_status Valor a asignar en caso de borrado suave. */
	private static $_deleted_status = 0;

	/**
	 * Constructor de la clase
	 * 
	 * Se inicializan las propiedades de la clase.
	 * @param bool $default Determina si se utilizan, o no, los valores por defecto
	 * definidos en la base de datos.
	 **/
	public function __construct($default = true)
	{
		if($default)
		{
			$this->option_type = 1;
		}
	}

	public function getOption_id()
	{
		return $this->option_id;
	}

	public function setOption_id($value)
	{
		$this->option_id = $value === null ? null : (int)$value;
	}

	public function getOption_type()
	{
		return $this->option_type;
	}

	public function setOption_type($value)
	{
		$this->option_type = $value === null ? null : (int)$value;
	}

	public function getOption_key()
	{
		return $this->option_key;
	}

	public function setOption_key($value)
	{
		$this->option_key = $value === null ? null : (string)$value;
	}

	public function getOption_description()
	{
		return $this->option_description;
	}

	public function setOption_description($value)
	{
		$this->option_description = $value === null ? null : (string)$value;
	}

	public function getModule_id()
	{
		return $this->module_id;
	}

	public function setModule_id($value)
	{
		$this->module_id = $value === null ? null : (int)$value;
	}

	public function getDefault_value()
	{
		return $this->default_value;
	}

	public function setDefault_value($value)
	{
		$this->default_value = $value === null ? null : (string)$value;
	}

	public function entity_options()
	{
		entity_options_model::flush();
		return entity_options_model::where("option_id", $this->option_id);
	}
}
?>
