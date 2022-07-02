<?php
trait ORM
{
	/** @var PDO $_db Base de datos */
	private static $_db = null;

	/** @var string $_select Columnas que se seleccionarán en una tabla */
	private static $_select = "";

	/** @var array $_where Conjunto de condiciones que se aplicarán a la consulta */
	private static $_where = Array();

	/**
	 * Limpieza de flujo
	 * 
	 * Limpia todas las propiedades estáticas de la clase, para que se pueda realizar otra
	 * consulta.
	 */
	public static function flush()
	{
		self::$_select = "";
		self::$_where = Array();
	}

	/** 
	 * Inicializar
	 * 
	 * Inicia la conexión en la base de datos, y la mantiene en la propiedad estática $db
	 */
	public static function init()
	{
		if(self::$_db == null)
		{
			self::$_db = new Database(DB_TYPE, DB_HOST, DB_PORT,DB_NAME, DB_USER, DB_PASS);
		}
	}

	/** Primer resultado
	 * 
	 * Realiza un aconsulta y retorna un resultado
	 * 
	 * @return object Un objeto conteniendo los datos consultados
	 */
	public static function first()
	{
		self::init();
		$class = get_called_class();
		$instance = new $class;
		$sql = "SELECT * FROM $instance->_table_name WHERE 1 LIMIT 1";
		$sth = self::$_db->prepare($sql);
		$sth->execute();
		self::flush();
		return $sth->fetchObject($class);
	}

	/**
	 * Encontrar por su llave primaria
	 * 
	 * Obtiene un registro de la tabla indicada por su llave primaria.
	 * 
	 * @return object Objeto de la clase que llamó al método
	 */
	public static function find($id)
	{
		self::init();
		$class = get_called_class();
		$instance = new $class;
		$sql = "SELECT * FROM $instance->_table_name WHERE $instance->_primary_key = :id LIMIT 1";
		$sth = self::$_db->prepare($sql);
		$sth->bindValue(":id", $id);
		$sth->execute();
		self::flush();
		return $sth->fetchObject($class);
	}

	/**
	 * Guardar
	 * 
	 * Inserta o actualiza un registro en la base de datos. Si existe un valos en la propiedad
	 * indicada como llave primaria, el registro se actualiza; sino, se crea uno nuevo.
	 * 
	 * @return void
	 */
	public function save()
	{
		self::init();
		$now = Date("Y-m-d H:i:s");
		if($this->_timestamps)
		{
			if(empty($this->{$this->_primary_key}))
			{
				$this->setCreation_user(Session::get("user_id"));
				$this->setCreation_time($now);
			}
			$this->setEdition_user(Session::get("user_id"));
			$this->setEdition_time($now);
		}
		$data = get_object_vars($this);
		$unset = Array("_table_name", "_primary_key", "_timestamps", "_soft_delete");
		foreach($unset as $key)
		{
			unset($data[$key]);
		}
		$sth = null;
		if(empty($this->{$this->_primary_key}))
		{
			$fieldNames = implode(',', array_keys($data));
			$fieldValues = ':' . implode(', :', array_keys($data));
			$sth = self::$_db->prepare("INSERT INTO $this->_table_name ($fieldNames) VALUES ($fieldValues)");
		}
		else
		{
			$fieldDetails = "";
			foreach($data as $key => $value)
			{
				$fieldDetails .= "$key=:$key,";
			}
			$fieldDetails = rtrim($fieldDetails, ',');
			$sth = self::$_db->prepare("UPDATE $this->_table_name SET $fieldDetails WHERE $this->_primary_key = :$this->_primary_key");
		}
		foreach ($data as $key => $value)
		{
			$sth->bindValue(":$key", $value);
		}
		$sth->execute();
		self::flush();
		return $sth->rowCount();
	}

	/**
	 * Borrar
	 * 
	 * Elimina el elemento. Si la propiedad $soft_delete es verdadera, entonces actualiza el estado
	 * $status = 0
	 * 
	 * @return bool Verdadero si el objeto ha sido eliminado
	 */
	public function delete()
	{
		if($this->soft_delete)
		{
			$this->setEdition_user(Session::get("user_id"));
			$this->setEdition_time(Date("Y-m-d H:i:s"));
			$this->setStatus(0);
			$affected = $this->save();
		}
		return $affected != 0;
	}
}
?>
