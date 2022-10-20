<?php
/**
 * Model for app_installers
 * 
 * Generated by BlackPHP
 */

class app_installers_model
{
	use ORM;

	/** @var int $installer_id ID de la tabla */
	private $installer_id;

	/** @var string $installer_nickname Usuario */
	private $installer_nickname;

	/** @var string $installer_password Resumen de contraseña */
	private $installer_password;

	/** @var string $installer_name Nombre del instalador */
	private $installer_name;

	/** @var string $installer_phone Teléfono */
	private $installer_phone;

	/** @var string $installer_email Correo electrónico */
	private $installer_email;

	/** @var string $creation_time Hora y fecha de creación */
	private $creation_time;

	/** @var int $status Eliminado, inactivo, activo */
	private $status;


	/** @var string $_table_name Nombre de la tabla */
	private static $_table_name = "app_installers";

	/** @var string $_table_type Tipo de tabla */
	private static $_table_type = "BASE TABLE";

	/** @var string $_primary_key Llave primaria */
	private static $_primary_key = "installer_id";

	/** @var bool $_timestamps La tabla usa marcas de tiempo para la inserción y edición de datos */
	private static $_timestamps = false;

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

	public function getInstaller_id()
	{
		return $this->installer_id;
	}

	public function setInstaller_id($value)
	{
		$this->installer_id = $value === null ? null : (int)$value;
	}

	public function getInstaller_nickname()
	{
		return $this->installer_nickname;
	}

	public function setInstaller_nickname($value)
	{
		$this->installer_nickname = $value === null ? null : (string)$value;
	}

	public function getInstaller_password()
	{
		return $this->installer_password;
	}

	public function setInstaller_password($value)
	{
		$this->installer_password = $value === null ? null : (string)$value;
	}

	public function getInstaller_name()
	{
		return $this->installer_name;
	}

	public function setInstaller_name($value)
	{
		$this->installer_name = $value === null ? null : (string)$value;
	}

	public function getInstaller_phone()
	{
		return $this->installer_phone;
	}

	public function setInstaller_phone($value)
	{
		$this->installer_phone = $value === null ? null : (string)$value;
	}

	public function getInstaller_email()
	{
		return $this->installer_email;
	}

	public function setInstaller_email($value)
	{
		$this->installer_email = $value === null ? null : (string)$value;
	}

	public function getCreation_time()
	{
		return $this->creation_time;
	}

	public function setCreation_time($value)
	{
		$this->creation_time = $value === null ? null : (string)$value;
	}

	public function getStatus()
	{
		return $this->status;
	}

	public function setStatus($value)
	{
		$this->status = $value === null ? null : (int)$value;
	}

	public function entities()
	{
		entities_model::flush();
		return entities_model::where("creation_installer", $this->installer_id);
	}
}
?>
