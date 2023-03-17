<?php
/**
 * Model for entities
 * 
 * Generated by BlackPHP
 */

class entitiesModel
{
	use ORM;

	/** @var int $entity_id ID de la tabla */
	private $entity_id;

	/** @var string $entity_name Nombre de la empresa */
	private $entity_name;

	/** @var string $entity_slogan Eslogan de la empresa */
	private $entity_slogan;

	/** @var int $admin_user Usuario principal (Superadministrador) */
	private $admin_user;

	/** @var string $entity_date Fecha actual de operaciones (En caso que difiera del sistema) */
	private $entity_date;

	/** @var string $entity_begin Fecha de inicio de las operaciones */
	private $entity_begin;

	/** @var string $entity_subdomain Subdominio (Para funcionamiento en línea) */
	private $entity_subdomain;

	/** @var string $app_name Nombre de la App para instalación como PWA */
	private $app_name;

	/** @var string $default_locale Idioma por defecto de la entidad */
	private $default_locale;

	/** @var int $creation_installer ID del usuario que instaló el sistema */
	private $creation_installer;

	/** @var string $creation_time - */
	private $creation_time;

	/** @var int $edition_installer - */
	private $edition_installer;

	/** @var string $installer_edition_time - */
	private $installer_edition_time;

	/** @var int $edition_user - */
	private $edition_user;

	/** @var string $user_edition_time - */
	private $user_edition_time;

	/** @var int $status - */
	private $status;


	/** @var string $_table_name Nombre de la tabla */
	private static $_table_name = "entities";

	/** @var string $_table_type Tipo de tabla */
	private static $_table_type = "BASE TABLE";

	/** @var string $_primary_key Llave primaria */
	private static $_primary_key = "entity_id";

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
			$this->app_name = 'BlackPHP';
			$this->status = 1;
		}
	}

	public function getEntityId()
	{
		return $this->entity_id;
	}

	public function setEntityId($value)
	{
		$this->entity_id = $value === null ? null : (int)$value;
	}

	public function getEntityName()
	{
		return $this->entity_name;
	}

	public function setEntityName($value)
	{
		$this->entity_name = $value === null ? null : (string)$value;
	}

	public function getEntitySlogan()
	{
		return $this->entity_slogan;
	}

	public function setEntitySlogan($value)
	{
		$this->entity_slogan = $value === null ? null : (string)$value;
	}

	public function getAdminUser()
	{
		return $this->admin_user;
	}

	public function setAdminUser($value)
	{
		$this->admin_user = $value === null ? null : (int)$value;
	}

	public function getEntityDate()
	{
		return $this->entity_date;
	}

	public function setEntityDate($value)
	{
		$this->entity_date = $value === null ? null : (string)$value;
	}

	public function getEntityBegin()
	{
		return $this->entity_begin;
	}

	public function setEntityBegin($value)
	{
		$this->entity_begin = $value === null ? null : (string)$value;
	}

	public function getEntitySubdomain()
	{
		return $this->entity_subdomain;
	}

	public function setEntitySubdomain($value)
	{
		$this->entity_subdomain = $value === null ? null : (string)$value;
	}

	public function getAppName()
	{
		return $this->app_name;
	}

	public function setAppName($value)
	{
		$this->app_name = $value === null ? null : (string)$value;
	}

	public function getDefaultLocale()
	{
		return $this->default_locale;
	}

	public function setDefaultLocale($value)
	{
		$this->default_locale = $value === null ? null : (string)$value;
	}

	public function getCreationInstaller()
	{
		return $this->creation_installer;
	}

	public function setCreationInstaller($value)
	{
		$this->creation_installer = $value === null ? null : (int)$value;
	}

	public function getCreationTime()
	{
		return $this->creation_time;
	}

	public function setCreationTime($value)
	{
		$this->creation_time = $value === null ? null : (string)$value;
	}

	public function getEditionInstaller()
	{
		return $this->edition_installer;
	}

	public function setEditionInstaller($value)
	{
		$this->edition_installer = $value === null ? null : (int)$value;
	}

	public function getInstallerEditionTime()
	{
		return $this->installer_edition_time;
	}

	public function setInstallerEditionTime($value)
	{
		$this->installer_edition_time = $value === null ? null : (string)$value;
	}

	public function getEditionUser()
	{
		return $this->edition_user;
	}

	public function setEditionUser($value)
	{
		$this->edition_user = $value === null ? null : (int)$value;
	}

	public function getUserEditionTime()
	{
		return $this->user_edition_time;
	}

	public function setUserEditionTime($value)
	{
		$this->user_edition_time = $value === null ? null : (string)$value;
	}

	public function getStatus()
	{
		return $this->status;
	}

	public function setStatus($value)
	{
		$this->status = $value === null ? null : (int)$value;
	}

	public function entityMethods()
	{
		entityMethodsModel::flush();
		return entityMethodsModel::where("entity_id", $this->entity_id);
	}

	public function entityModules()
	{
		entityModulesModel::flush();
		return entityModulesModel::where("entity_id", $this->entity_id);
	}

	public function entityOptions()
	{
		entityOptionsModel::flush();
		return entityOptionsModel::where("entity_id", $this->entity_id);
	}

	public function roles()
	{
		rolesModel::flush();
		return rolesModel::where("entity_id", $this->entity_id);
	}

	public function users()
	{
		usersModel::flush();
		return usersModel::where("entity_id", $this->entity_id);
	}
}
?>
