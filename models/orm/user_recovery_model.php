<?php
/**
 * Model for user_recovery
 * 
 * Generated by BlackPHP
 */

class user_recovery_model
{
	use ORM;

	/** @var string $_table_name Nombre de la tabla */
	private $_table_name;

	/** @var string $_primary_key Llave primaria */
	private $_primary_key;

	/** @var bool $_timestamps La tabla usa marcas de tiempo para la inserción y edición de datos */
	private $_timestamps;

	/** @var bool $_soft_delete La tabla soporta borrado suave */
	private $_soft_delete;

	/** @var int $urecovery_id ID de la tabla */
	private $urecovery_id;

	/** @var int $user_id ID del usuario */
	private $user_id;

	/** @var string $urecovery_code Código de recuperación */
	private $urecovery_code;

	/** @var string $expiration_time Fecha y hora de vencimiento */
	private $expiration_time;

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

	/**
	 * Constructor de la clase
	 * 
	 * Inicializa las propiedades generales de la tabla
	 */
	public function __construct()
	{
		$this->_table_name = "user_recovery";
		$this->_primary_key = "urecovery_id";
		$this->_timestamps = true;
		$this->_soft_delete = true;
	}

	public function getUrecovery_id()
	{
		return $this->urecovery_id;
	}

	public function setUrecovery_id($value)
	{
		$this->urecovery_id = (int)$value;
	}

	public function getUser_id()
	{
		return $this->user_id;
	}

	public function setUser_id($value)
	{
		$this->user_id = (int)$value;
	}

	public function getUrecovery_code()
	{
		return $this->urecovery_code;
	}

	public function setUrecovery_code($value)
	{
		$this->urecovery_code = (string)$value;
	}

	public function getExpiration_time()
	{
		return $this->expiration_time;
	}

	public function setExpiration_time($value)
	{
		$this->expiration_time = (string)$value;
	}

	public function getCreation_user()
	{
		return $this->creation_user;
	}

	public function setCreation_user($value)
	{
		$this->creation_user = (int)$value;
	}

	public function getCreation_time()
	{
		return $this->creation_time;
	}

	public function setCreation_time($value)
	{
		$this->creation_time = (string)$value;
	}

	public function getEdition_user()
	{
		return $this->edition_user;
	}

	public function setEdition_user($value)
	{
		$this->edition_user = (int)$value;
	}

	public function getEdition_time()
	{
		return $this->edition_time;
	}

	public function setEdition_time($value)
	{
		$this->edition_time = (string)$value;
	}

	public function getStatus()
	{
		return $this->status;
	}

	public function setStatus($value)
	{
		$this->status = (int)$value;
	}
}
?>
