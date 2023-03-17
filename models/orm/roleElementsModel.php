<?php
/**
 * Model for role_elements
 * 
 * Generated by BlackPHP
 */

class roleElementsModel
{
	use ORM;

	/** @var int $role_element_id Llave primaria */
	private $role_element_id;

	/** @var int $role_id ID del rol */
	private $role_id;

	/** @var int $element_id ID del elemento */
	private $element_id;

	/** @var int $permissions Permisos (Leer, crear, editar, eliminar) */
	private $permissions;

	/** @var int $creation_user - */
	private $creation_user;

	/** @var int $creation_time - */
	private $creation_time;

	/** @var int $edition_user - */
	private $edition_user;

	/** @var int $edition_time - */
	private $edition_time;

	/** @var int $status - */
	private $status;


	/** @var string $_table_name Nombre de la tabla */
	private static $_table_name = "role_elements";

	/** @var string $_table_type Tipo de tabla */
	private static $_table_type = "BASE TABLE";

	/** @var string $_primary_key Llave primaria */
	private static $_primary_key = "role_element_id";

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
			$this->permissions = 8;
			$this->status = 1;
		}
	}

	public function getRoleElementId()
	{
		return $this->role_element_id;
	}

	public function setRoleElementId($value)
	{
		$this->role_element_id = $value === null ? null : (int)$value;
	}

	public function getRoleId()
	{
		return $this->role_id;
	}

	public function setRoleId($value)
	{
		$this->role_id = $value === null ? null : (int)$value;
	}

	public function getElementId()
	{
		return $this->element_id;
	}

	public function setElementId($value)
	{
		$this->element_id = $value === null ? null : (int)$value;
	}

	public function getPermissions()
	{
		return $this->permissions;
	}

	public function setPermissions($value)
	{
		$this->permissions = $value === null ? null : (int)$value;
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
		$this->creation_time = $value === null ? null : (int)$value;
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
		$this->edition_time = $value === null ? null : (int)$value;
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
