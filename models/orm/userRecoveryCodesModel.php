<?php
/**
 * Model for user_recovery_codes
 * 
 * Generated by BlackPHP
 */

class userRecoveryCodesModel
{
	use ORM;

	/** @var int $recovery_code_id Llave primaria */
	private $recovery_code_id;

	/** @var int $user_id ID del usuario */
	private $user_id;

	/** @var string $recovery_code Código de recuperación */
	private $recovery_code;

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


	/** @var string $_table_name Nombre de la tabla */
	private static $_table_name = "user_recovery_codes";

	/** @var string $_table_type Tipo de tabla */
	private static $_table_type = "BASE TABLE";

	/** @var string $_primary_key Llave primaria */
	private static $_primary_key = "recovery_code_id";

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

	public function getRecoveryCodeId()
	{
		return $this->recovery_code_id;
	}

	public function setRecoveryCodeId($value)
	{
		$this->recovery_code_id = $value === null ? null : (int)$value;
	}

	public function getUserId()
	{
		return $this->user_id;
	}

	public function setUserId($value)
	{
		$this->user_id = $value === null ? null : (int)$value;
	}

	public function getRecoveryCode()
	{
		return $this->recovery_code;
	}

	public function setRecoveryCode($value)
	{
		self::validateStringSize($value, 32);
		$this->recovery_code = $value === null ? null : (string)$value;
	}

	public function getExpirationTime()
	{
		return $this->expiration_time;
	}

	public function setExpirationTime($value)
	{
		$this->expiration_time = $value === null ? null : (string)$value;
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