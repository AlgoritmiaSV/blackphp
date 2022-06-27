<?php
/**
 * Model for app_themes
 * 
 * Generated by BlackPHP
 */

class app_themes_model extends Model
{
	/** @var int $theme_id ID de la tabla */
	private $theme_id;

	/** @var string $theme_name Nombre del tema */
	private $theme_name;

	/** @var string $theme_url Nombre de la carpeta pública */
	private $theme_url;

	/**
	 * Constructor de la clase
	 * 
	 * Inicializa las propiedades table_name y primary_key
	 */
	public function __construct()
	{
		$this->table_name = "app_themes";
		$this->primary_key = "theme_id";
	}

	public function getTheme_id()
	{
		return $this->theme_id;
	}

	public function setTheme_id($value)
	{
		$this->theme_id = $value;
	}

	public function getTheme_name()
	{
		return $this->theme_name;
	}

	public function setTheme_name($value)
	{
		$this->theme_name = $value;
	}

	public function getTheme_url()
	{
		return $this->theme_url;
	}

	public function setTheme_url($value)
	{
		$this->theme_url = $value;
	}
}
?>
