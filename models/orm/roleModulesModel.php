<?php
/**
 * Model for role_modules
 * 
 * Generated by BlackPHP
 */

class roleModulesModel
{
	use ORM;

	/** @var int $role_module_id Llave primaria */
	private $role_module_id;

	/** @var int $role_id - */
	private $role_id;

	/** @var int $module_id - */
	private $module_id;

	/** @var int $creation_user - */
	private $creation_user;

	/** @var string $creation_time - */
	private $creation_time;

	/** @var int $edition_user - */
	private $edition_user;

	/** @var string $edition_time - */
	private $edition_time;

	/** @var int $status - */
	private $status;


	/** @var string $_table_name Nombre de la tabla */
	private static $_table_name = "role_modules";

	/** @var string $_table_type Tipo de tabla */
	private static $_table_type = "BASE TABLE";

	/** @var string $_primary_key Llave primaria */
	private static $_primary_key = "role_module_id";

	/** @var bool $_timestamps La tabla usa marcas de tiempo para la inserción y edición de datos */
	private static $_timestamps = true;

	/** @var bool $_soft_delete La tabla soporta borrado suave */
	private static $_soft_delete = true;

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
			$this->status = 1;
		}
	}

	public function getRoleModuleId()
	{
		return $this->role_module_id;
	}

	public function setRoleModuleId($value)
	{
		$this->role_module_id = $value === null ? null : (int)$value;
	}

	public function getRoleId()
	{
		return $this->role_id;
	}

	public function setRoleId($value)
	{
		$this->role_id = $value === null ? null : (int)$value;
	}

	public function getModuleId()
	{
		return $this->module_id;
	}

	public function setModuleId($value)
	{
		$this->module_id = $value === null ? null : (int)$value;
	}

	public function getCreationUser()
	{
		return $this->creation_user;
	}

	public function setCreationUser($value)
	{
		$this->creation_user = $value === null ? null : (int)$value;
	}

	public function getCreationTime()
	{
		return $this->creation_time;
	}

	public function setCreationTime($value)
	{
		$this->creation_time = $value === null ? null : (string)$value;
	}

	public function getEditionUser()
	{
		return $this->edition_user;
	}

	public function setEditionUser($value)
	{
		$this->edition_user = $value === null ? null : (int)$value;
	}

	public function getEditionTime()
	{
		return $this->edition_time;
	}

	public function setEditionTime($value)
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
