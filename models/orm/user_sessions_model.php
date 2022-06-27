<?php
/**
 * Model for user_sessions
 * 
 * Generated by BlackPHP
 */

class user_sessions_model extends Model
{
	/** @var int $usession_id ID de la tabla */
	private $usession_id;

	/** @var int $user_id ID del usuario */
	private $user_id;

	/** @var int $branch_id Sucursal en la que inició sesión */
	private $branch_id;

	/** @var string $ip_address Dirección IP desde donde se conecta */
	private $ip_address;

	/** @var int $browser_id Navegador que usa */
	private $browser_id;

	/** @var string $date_time Fecha y hora */
	private $date_time;

	/**
	 * Constructor de la clase
	 * 
	 * Inicializa las propiedades table_name y primary_key
	 */
	public function __construct()
	{
		$this->table_name = "user_sessions";
		$this->primary_key = "usession_id";
	}

	public function getUsession_id()
	{
		return $this->usession_id;
	}

	public function setUsession_id($value)
	{
		$this->usession_id = $value;
	}

	public function getUser_id()
	{
		return $this->user_id;
	}

	public function setUser_id($value)
	{
		$this->user_id = $value;
	}

	public function getBranch_id()
	{
		return $this->branch_id;
	}

	public function setBranch_id($value)
	{
		$this->branch_id = $value;
	}

	public function getIp_address()
	{
		return $this->ip_address;
	}

	public function setIp_address($value)
	{
		$this->ip_address = $value;
	}

	public function getBrowser_id()
	{
		return $this->browser_id;
	}

	public function setBrowser_id($value)
	{
		$this->browser_id = $value;
	}

	public function getDate_time()
	{
		return $this->date_time;
	}

	public function setDate_time($value)
	{
		$this->date_time = $value;
	}
}
?>
