<?php
trait ORM
{
	/** @var PDO $_db Base de datos */
	private static $_db = null;

	/** @var string $_select Columnas que se seleccionarán en una tabla */
	private static $_select = Array();

	/** @var array $_join Uniones (JOIN) dentro de la consulta */
	private static $_join = Array();

	/** @var array $_where Conjunto de condiciones que se aplicarán a la consulta */
	private static $_where = Array();

	/** @var array $_order_by Criterios de ordenamiento de los resultados */
	private static $_order_by = Array();

	/**
	 * Limpieza de flujo
	 * 
	 * Limpia todas las propiedades estáticas de la clase, para que se pueda realizar otra
	 * consulta.
	 */
	public static function flush()
	{
		self::$_select = Array();
		self::$_join = Array();
		self::$_where = Array();
		self::$_order_by = Array();
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
		return self::get("FIRST");
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
		$table_name = self::$_table_name;
		$primary_key = self::$_primary_key;
		$class = get_called_class();
		$sql = "SELECT * FROM $table_name WHERE $primary_key = :id LIMIT 1";
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
		if(self::$_timestamps)
		{
			$user_id = empty(Session::get("user_id")) ? 0 : Session::get("user_id");
			if(empty($this->{self::$_primary_key}))
			{
				$this->setCreation_user($user_id);
				$this->setCreation_time($now);
			}
			$this->setEdition_user($user_id);
			$this->setEdition_time($now);
		}
		$data = get_object_vars($this);
		/*$unset = Array("_table_name", "_primary_key", "_timestamps", "_soft_delete");
		foreach($unset as $key)
		{
			unset($data[$key]);
		}*/
		$sth = null;
		$table_name = self::$_table_name;
		$primary_key = self::$_primary_key;
		if(empty($primary_key))
		{
			$fieldNames = implode(',', array_keys($data));
			$fieldValues = ':' . implode(', :', array_keys($data));
			$sth = self::$_db->prepare("INSERT INTO $table_name ($fieldNames) VALUES ($fieldValues)");
		}
		else
		{
			$fieldDetails = "";
			foreach($data as $key => $value)
			{
				$fieldDetails .= "$key=:$key,";
			}
			$fieldDetails = rtrim($fieldDetails, ',');
			$sth = self::$_db->prepare("UPDATE $table_name SET $fieldDetails WHERE $primary_key = :$primary_key");
		}
		foreach ($data as $key => $value)
		{
			$sth->bindValue(":$key", $value);
		}
		$sth->execute();
		self::flush();
		if(empty($this->{$primary_key}))
		{
			$this->{$primary_key} = self::$_db->lastInsertId();
		}
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

	public static function select()
	{
		self::$_select = array_merge(self::$_select, func_get_args());
		return new static();
	}

	public static function join()
	{
		self::$_join[] = func_get_args();
		return new static();
	}

	public static function where()
	{
		self::$_where[] = func_get_args();
		return new static();
	}

	public static function orderBy()
	{
		self::$_order_by[] = func_get_args();
		return new static();
	}

	public static function get($results = "FIRST", $objects = true)
	{
		$table_name = self::$_table_name;
		# Select
		$select = "*";
		if(count(self::$_select) > 0)
		{
			$select = implode(",", self::$_select);
			$objects = false;
		}

		# Join
		$join = "";
		if(count(self::$_join) > 0)
		{
			foreach(self::$_join as $value)
			{
				if(is_array($value))
				{
					if(count($value) == 3)
					{
						$join .= "LEFT JOIN $value[0] ON $table_name.$value[1] = $value[0].$value[2] ";
					}
				}
			}
		}

		# Where
		$wheres = Array();
		$data = Array();
		foreach(self::$_where as $value)
		{
			if(is_array($value))
			{
				$var = str_replace(".", "_", $value[0]);
				if(count($value) == 3)
				{
					$data[$var] = $value[2];
					$wheres[] = $value[0] . " " . $value[1] . " :" . $var;
				}
				if(count($value) == 2)
				{
					$data[$var] = $value[1];
					$wheres[] = $value[0] . " = :" . $var;
				}
			}
			if(is_string($value))
			{
				$wheres[] = $value;
			}
		}
		$where = implode(" AND ", $wheres);
		if(empty($where))
		{
			$where = 1;
		}

		# Order By
		$order_by = "";
		if(count(self::$_order_by) > 0)
		{
			$order_by .= "ORDER BY ";
			$orders = Array();
			foreach(self::$_order_by as $value)
			{
				if(is_array($value))
				{
					$order_item = $value[0];
					if(count($value) == 2)
					{
						$order_item .= " " . $value[1];
					}
					$orders[] = $order_item;
				}
			}
			$order_by .= implode(", ", $orders);
		}

		self::init();
		$sql = "SELECT $select FROM $table_name $join WHERE $where $order_by";
		$sth = self::$_db->prepare($sql);
		foreach ($data as $key => $value) {
			$sth->bindValue(":$key", $value);
		}
		$sth->execute();
		self::flush();
		$class = get_called_class();
		if($results == "FIRST")
		{
			if($objects)
			{
				$object = $sth->fetchObject($class);
				if($object === false)
				{
					return new $class;
				}
				else
				{
					return $object;
				}
			}
			else
			{
				return $sth->fetch(PDO::FETCH_ASSOC);
			}
		}
		else
		{
			if($objects)
			{
				return $sth->fetchAll(PDO::FETCH_CLASS, $class);
			}
			else
			{
				return $sth->fetchAll(PDO::FETCH_ASSOC);
			}
		}
	}

	public static function getAll()
	{
		return self::get("ALL");
	}

	public static function getAllArray()
	{
		return self::get("ALL", false);
	}

	public function toArray()
	{
		$data = get_object_vars($this);
		return $data;
	}

	public function set($array)
	{
		$data = get_object_vars($this);
		foreach($array as $key => $value)
		{
			if(array_key_exists($key, $data))
			{
				$this->{"set" . ucfirst($key)}($value);
			}
		}
		return $this;
	}
}
?>
