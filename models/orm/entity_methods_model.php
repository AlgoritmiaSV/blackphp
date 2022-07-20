<?php
/**
 * Model for entity_methods
 * 
 * Generated by BlackPHP
 */

class entity_methods_model
{
	use ORM;

	/** @var int $emethod_id ID de la tabla */
	private $emethod_id;

	/** @var int $entity_id ID de la empresa */
	private $entity_id;

	/** @var int $method_id ID del método */
	private $method_id;

	/** @var int $method_order Orden en el que aoparecerá el método en el menú */
	private $method_order;

	/** @var string $creation_time - */
	private $creation_time;

	/** @var string $edition_time - */
	private $edition_time;

	/** @var int $status - */
	private $status;


	/** @var string $_table_name Nombre de la tabla */
	private static $_table_name = "entity_methods";

	/** @var string $_primary_key Llave primaria */
	private static $_primary_key = "emethod_id";

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
		$this->method_order = 1;
		$this->status = 1;
	}

	public function getEmethod_id()
	{
		return $this->emethod_id;
	}

	public function setEmethod_id($value)
	{
		$this->emethod_id = $value === null ? null : (int)$value;
	}

	public function getEntity_id()
	{
		return $this->entity_id;
	}

	public function setEntity_id($value)
	{
		$this->entity_id = $value === null ? null : (int)$value;
	}

	public function getMethod_id()
	{
		return $this->method_id;
	}

	public function setMethod_id($value)
	{
		$this->method_id = $value === null ? null : (int)$value;
	}

	public function getMethod_order()
	{
		return $this->method_order;
	}

	public function setMethod_order($value)
	{
		$this->method_order = $value === null ? null : (int)$value;
	}

	public function getCreation_time()
	{
		return $this->creation_time;
	}

	public function setCreation_time($value)
	{
		$this->creation_time = $value === null ? null : (string)$value;
	}

	public function getEdition_time()
	{
		return $this->edition_time;
	}

	public function setEdition_time($value)
	{
		$this->edition_time = $value === null ? null : (string)$value;
	}

	public function getStatus()
	{
		return $this->status;
	}

	public function setStatus($value)
	{
		$this->status = $value === null ? null : (int)$value;
	}
}
?>
