<?php
/**
 * Object Resource Management
 * 
 * En el presente trait se establecen los princiales métodos para permitir la interación
 * con la base de datos por parte de cada uno de los modelos.
 * 
 * @author Edwin Fajardo <contacto@edwinfajardo.com>
 * @link https://www.edwinfajardo.com
 */

trait ORM
{
	/** @var PDO $_db Base de datos */
	private static $_db = null;

	/** @var string $_select Columnas que se seleccionarán en una tabla */
	private static $_select = Array();

	/** @var string $_extra_select Columnas adicionales que se seleccionarán en una tabla */
	private static $_extra_select = "";

	/** @var string $_modifier Modificador de la consulta (Ejemplo: DISTINCT) */
	private static $_modifier = "";

	/** @var array $_join Uniones (JOIN) dentro de la consulta */
	private static $_join = Array();

	/** @var array $_where Conjunto de condiciones que se aplicarán a la consulta */
	private static $_where = Array();

	/** @var array $_order_by Criterios de ordenamiento de los resultados */
	private static $_order_by = Array();

	/** @var array $_group_by Criterios de agrupación de los resultados */
	private static $_group_by = Array();

	/**
	 * @var int|bool $_offset Punto de partida para muestra de resultados
	 * Puede tomar un número entero mayor que cero o false si no está definido.
	 */
	private static $_offset = false;

	/**
	 * @var int|bool $_limit Número máximo de resultados en una consulta.
	 * Puede tomar un número entero mayor que cero o false si no está definido.
	 */
	private static $_limit = false;

	/**
	 * @var bool $_ommit_status Determina si se debe evaluar el campo status a la hora de la consulta.
	 */
	private static $_ommit_status = false;

	/**
	 * Limpieza de flujo
	 * 
	 * Limpia todas las propiedades estáticas de la consulta, para que se pueda realizar otra
	 * consulta.
	 */
	public static function flush()
	{
		self::$_select = Array();
		self::$_join = Array();
		self::$_where = Array();
		self::$_order_by = Array();
		if(get_called_class() == "DB")
		{
			self::$_table_name = null;
		}
		self::$_modifier = "";
		self::$_extra_select = "";
		self::$_offset = false;
		self::$_limit = false;
		self::$_ommit_status = false;
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
			if(DB_TYPE == 'sqlsrv')
			{
				self::$_db = new PDO(DB_TYPE.':Server='.DB_HOST.','.DB_PORT.';Database='.DB_NAME, DB_USER, DB_PASS);
				self::$_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				# self::$_db->exec('SET NAMES "utf8" COLLATE "utf8_general_ci"');
			}
			else
			{
				self::$_db = new PDO(DB_TYPE.':host='.DB_HOST.';port='.DB_PORT.';dbname='.DB_NAME, DB_USER, DB_PASS);
				self::$_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				self::$_db->exec('SET NAMES "utf8" COLLATE "utf8_general_ci"');
			}
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
		return self::get();
	}

	/**
	 * Encontrar por su llave primaria
	 * 
	 * Obtiene un registro de la tabla indicada por su llave primaria.
	 * @param mixed $id El identificador (Llave primaria) del registro.
	 * @param bool $deleted Obtener un registro aún si ya ha sido eliminado (status = 0)
	 * 
	 * @return object Objeto de la clase que llamó al método
	 */
	public static function find($id, $deleted = false)
	{
		if($deleted && property_exists(new static(), "status"))
		{
			self::$_ommit_status = true;
		}
		return self::where(self::$_primary_key, $id)->get();
	}

	/**
	 * Encontrar por
	 * 
	 * Obtiene un registro de la tabla indicada por un campo específico.
	 * @param string $field El nombre del campo a considerar
	 * @param mixed $value El valor del campo a considerar
	 * @param bool $deleted Obtener un registro aún si ya ha sido eliminado (status = 0 o NULL)
	 * 
	 * @return object Objeto de la clase que llamó al método
	 */
	public static function findBy($field, $value, $deleted = false)
	{
		if($deleted && property_exists(new static(), "status"))
		{
			self::$_ommit_status = true;
		}
		return self::where($field, $value)->get();
	}

	/**
	 * Guardar
	 * 
	 * Inserta o actualiza un registro en la base de datos. Si existe un valor en la propiedad
	 * indicada como llave primaria, el registro se actualiza; sino, se crea uno nuevo.
	 * 
	 * @return void
	 */
	public function save()
	{
		// Las vistas no se guardan
		if(self::$_table_type == "VIEW")
		{
			return 0;
		}
		$primary_key = self::$_primary_key;
		if(!empty($this->{$primary_key}))
		{
			$class = get_called_class();
			$initial = new $class;
			$initial = $initial->find($this->{$primary_key});
			if($this == $initial)
			{
				return 0;
			}
		}
		self::init();
		$now = Date("Y-m-d H:i:s");
		if(self::$_timestamps)
		{
			$user_id = Session::get("user_id") ?? 0;
			if(empty($this->{$primary_key}))
			{
				$this->setCreationUser($user_id);
				$this->setCreationTime($now);
			}
			$this->setEditionUser($user_id);
			$this->setEditionTime($now);
		}
		if(property_exists($this, "entity_id") && is_null($this->entity_id))
		{
			$this->entity_id = Session::get("entity/entity_id");
		}
		$data = get_object_vars($this);
		$sth = null;
		$table_name = self::$_table_name;
		if(empty($this->{$primary_key}))
		{
			unset($data[$primary_key]);
			$fieldNames = implode(',', array_keys($data));
			$fieldValues = ':' . implode(', :', array_keys($data));
			$sth = self::$_db->prepare("INSERT INTO $table_name ($fieldNames) VALUES ($fieldValues)");
		}
		else
		{
			$fieldDetails = "";
			foreach($data as $key => $value)
			{
				if($key != $primary_key)
				{
					$fieldDetails .= "$key=:$key,";
				}
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
	 * Actualizar
	 * 
	 * Actualiza todas las filas coincidentes con las condiciones previamente establecidas
	 * en where. Si no se han establecido condiciones, actualiza todas las filas.
	 * 
	 * @return void
	 */
	public static function update($data)
	{
		# Las vistas no se actualizan
		if(self::$_table_type == "VIEW")
		{
			return 0;
		}

		self::init();
		$now = Date("Y-m-d H:i:s");
		if(self::$_timestamps)
		{
			$user_id = empty(Session::get("user_id")) ? 0 : Session::get("user_id");
			$data["edition_user"] = $user_id;
			$data["edition_time"] = $now;
		}
		$sth = null;
		$table_name = self::$_table_name;
		$fieldDetails = "";
		foreach($data as $key => $value)
		{
			$fieldDetails .= "$key=:$key,";
		}
		$fieldDetails = rtrim($fieldDetails, ',');

		# Where
		$wheres = Array();
		$prefix = "";
		foreach(self::$_where as $value)
		{
			if(is_array($value))
			{
				$var = str_replace(".", "_", $value[0]);
				$var = preg_replace( '/[^a-z0-9]/i', '', $var);
				if($var == "" || is_numeric($var[0]))
				{
					$var = "v" . $var;
				}
				$var = substr($var, 0, 32);
				$number = 1;
				$initial_var = $var;
				while(array_key_exists($var, $data))
				{
					$var = $initial_var . $number;
					$number++;
				}
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
				if($value[0] == "status" || $value[0] == $prefix . "status")
				{
					self::$_ommit_status = true;
				}
				if($value[0] == "entity_id" || $value[0] == $prefix . "entity_id")
				{
					$entity = true;
				}
			}
			elseif(is_string($value))
			{
				$wheres[] = $value;
			}
		}
		if(self::$_soft_delete && !self::$_ommit_status)
		{
			$wheres[] = $prefix . "status != 0";
		}
		if(property_exists(new static(), "entity_id") && !$entity && Session::get("entity") != null && Session::get("entity/entity_id") != null)
		{
			$wheres[] = $prefix . "entity_id = " . Session::get("entity/entity_id");
		}
		$where = implode(" AND ", $wheres);

		if(empty($where))
		{
			$where = 1;
		}

		$sql = "UPDATE $table_name SET $fieldDetails WHERE $where";
		$sth = self::$_db->prepare($sql);
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
	 * Elimina el elemento. Si la propiedad $soft_delete es verdadera, entonces actualiza el estado.
	 * 
	 * En el caso de un borrado suave, para que las llaves únicas (UNIQUE KEY) no tengan conflicto
	 * con los campos eliminados, se recomientda permitir valores nulos en el estado de las tablas
	 * que contengan llaves únicas (Aplica para MySQL).
	 * Por ejemplo: Se desea eliminar el registro de usuario con user_name = juan; pero user_name es
	 * una llave única; entonces al cambiar el estado del registro a cero, la llave única no
	 * permitirá crear un nuevo registro con user_name = juan. Para resolver esto, si el campo
	 * status admite valores nulos, el estado se cambia a NULL en vez de cero.
	 * 
	 * @return int Filas eliminadas
	 */
	public function delete()
	{
		if(self::$_table_type == "VIEW" || !$this->exists())
		{
			return 0;
		}
		$affected = 0;
		if(self::$_soft_delete)
		{
			if(self::$_timestamps)
			{
				$this->setEditionUser(Session::get("user_id"));
				$this->setEditionTime(Date("Y-m-d H:i:s"));
			}
			$this->setStatus(self::$_deleted_status);
			$affected = $this->save();
		}
		else
		{
			$table_name = self::$_table_name;
			$primary_key = self::$_primary_key;
			self::init();
			$sth = self::$_db->prepare("DELETE FROM $table_name WHERE $primary_key = :id");
			$sth->bindValue(":id", $this->{$primary_key});
			$sth->execute();
			self::flush();
			return $sth->rowCount();
		}
		return $affected;
	}

	/**
	 * Seleccionar
	 * 
	 * Recibe como parámetros, lo campos a seleccionar en la consulta. Si no se usa, por defecto
	 * se seleccionan todos los campos (SELECT *)
	 * 
	 * @return object Una instancia de la misma clase
	 */
	public static function select()
	{
		self::$_select = array_merge(self::$_select, func_get_args());
		return new static();
	}

	/**
	 * Modificador
	 * 
	 * Inserta un modificador en la consulta (Por ejemplo DISTINCT o SQL_CALC_FOUND_ROWS)
	 * 
	 * @return object Una instancia de la misma clase
	 */
	public static function modifier($modifier_name)
	{
		self::$_modifier = self::$_modifier . " " . $modifier_name;
		return new static();
	}

	/**
	 * Unir a la izquierda
	 * 
	 * Recibe como parámetros, los datos para unir varias tablas en la consulta con el método
	 * LEFT JOIN
	 * 
	 * @return object Una instancia de la misma clase
	 */
	public static function join()
	{
		self::$_join[] = func_get_args();
		return new static();
	}

	/**
	 * Condiciones
	 * 
	 * Recibe como parámetros, las condiciones a considerar en la consulta.
	 * Se puede llamar a este método varias veces para varias condiciones.
	 * 
	 * @return object Una instancia de la misma clase
	 */
	public static function where()
	{
		$argc = func_num_args();
		$argv = func_get_args();
		if($argc == 1)
		{
			self::$_where[] = (string)$argv[0];
		}
		else
		{
			self::$_where[] = $argv;
		}
		return new static();
	}

	/**
	 * Ordenar por
	 * 
	 * Recibe como parámetro las condiciones de ordenación a utilizarse en la consulta.
	 * 
	 * @return object Una instancia de la misma clase.
	 */
	public static function orderBy()
	{
		self::$_order_by[] = func_get_args();
		return new static();
	}

	/**
	 * Agrupar por
	 * 
	 * Recibe como parámetro las condiciones de agrupación a utilizarse en la consulta.
	 * 
	 * @return object Una instancia de la misma clase.
	 */
	public static function groupBy()
	{
		self::$_group_by[] = func_get_args();
		return new static();
	}

	/**
	 * Límites
	 * 
	 * Establece el punto de partida y el número máximo de filas a mostrar en una consulta.
	 * @param int $offset_or_limit El punto de partida (offset) si el segundo parámetro tiene un
	 * valor entero; o límite, si el segundo parámetro es falso
	 * @param int $limit El número máximo de resultados. Si es falso, se toma este valor del primer
	 * parámetro.
	 * 
	 * Ejemplos:
	 * - ->limit(10) Devuelve los primeros diez reaultados de la consulta
	 * - ->limit(5, 10) Devuelve diez resultados de la consulta, partiendo del quinto en adelante.
	 * 
	 * @return object Una instancia de la misma clase.
	 */

	public static function limit($offset_or_limit, $limit = false)
	{
		if($limit === false)
		{
			self::$_offset = false;
			self::$_limit = intval($offset_or_limit);
		}
		else
		{
			self::$_offset = intval($offset_or_limit);
			self::$_limit = intval($limit);
		}
		return new static();
	}

	/**
	 * Obtener
	 * 
	 * Realiza la consulta con los paràmetros (Condiciones) previamente configurados en otros
	 * métodos, y devuelve los resultados. Puede devolver como resultado lo siguiente:
	 * a) Sin parámetros: Devuelve un opbjeto de la clase que lo ha llamado con el primer
	 * resultado obtenido. En caso de no encontrar resultados, devuelve un objeto de la clase, vacío.
	 * b) $results != FIRST: Devuelve todos los resultados encontrados en un arreglo de objetos.
	 * Excepto cuando previamente se ha configurado select o join, en cuyo caso se devuelve un
	 * array asociativo. Si no hay resultados, devuelve un arreglo vacío.
	 * c) $objects = false: Devuelve un arreglo asociarivo con uno o varios resultados, dependiendo
	 * de $results
	 * 
	 * @param string|int $results Cantidad de resultados a devolver. Por defecto FIRST, devuelve el 
	 * primer resultado encontrado, ALL devuelve todos los resultados, y si es un número, devuelve ese
	 * número de resultados. Por ejemplo ->get(10) es equivalente de LIMIT 10
	 * @param boolean $objects Si es verdadero, intenta devolver objetos de la clase, en caso
	 * contrario, devuelve un arreglo asociativo.
	 * 
	 * Resolución de conflictos entre limit() y get():
	 * 
	 * Un valor numérico en get() sobreescribe el existente en limit(). Ejemplos:
	 * - limit(10)->get(20) mostrará veinte resultados, equivalente a get(20), o limit(20)->getAll()
	 * - limit(5,10)->get(20) mostrará veinte resultados partiendo de la quinta fila, equivalente a
	 * limit(5,20)->getAll()
	 * 
	 * @return object|array Resultados de la consulta
	 */
	public static function get($results = "FIRST", $objects = true)
	{
		$table_name = self::$_table_name;
		$prefix = "";
		$entity = false;
		if(strpos($table_name, ",") !== false)
		{
			$objects = false;
			self::$_ommit_status = true;
		}

		# Select
		$select = "*";
		if(count(self::$_select) > 0)
		{
			$select = implode(",", self::$_select);
			$objects = false;
		}

		# Modificador (DISTINCT, SQL_CALC_FOUND_ROWS...)
		$modifier = self::$_modifier;

		# Opciones adicionales (Contar líneas...)
		$extra_select = self::$_extra_select;
		if(!empty($extra_select))
		{
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
						$join .= "LEFT JOIN " . DB_PREFIX . "$value[0] ON $table_name.$value[1] = " . DB_PREFIX . "$value[0].$value[2] ";
					}
					elseif(count($value) == 2)
					{
						if(strpos($value[1], ".") !== false)
						{
							$join .= "LEFT JOIN " . DB_PREFIX . "$value[0] ON $value[1] ";
						}
						else
						{
							$join .= "LEFT JOIN " . DB_PREFIX . "$value[0] ON $table_name.$value[1] = " . DB_PREFIX . "$value[0].$value[1] ";
						}
					}
				}
			}
			$objects = false;
			$prefix = $table_name . ".";
		}

		# Where
		$wheres = Array();
		$data = Array();
		foreach(self::$_where as $value)
		{
			if(is_array($value))
			{
				$var = str_replace(".", "_", $value[0]);
				$var = preg_replace( '/[^a-z0-9]/i', '', $var);
				if($var == "" || is_numeric($var[0]))
				{
					$var = "v" . $var;
				}
				$var = substr($var, 0, 32);
				$number = 1;
				$initial_var = $var;
				while(array_key_exists($var, $data))
				{
					$var = $initial_var . $number;
					$number++;
				}
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
				if($value[0] == "status" || $value[0] == $prefix . "status")
				{
					self::$_ommit_status = true;
				}
				if($value[0] == "entity_id" || $value[0] == $prefix . "entity_id")
				{
					$entity = true;
				}
			}
			elseif(is_string($value))
			{
				if($value == $prefix . "status IS NULL")
				{
					self::$_ommit_status = true;
				}
				$wheres[] = $value;
			}
		}
		if(self::$_soft_delete && !self::$_ommit_status)
		{
			$wheres[] = $prefix . "status != 0";
		}
		if(property_exists(new static(), "entity_id") && !$entity && !empty(Session::get("entity/entity_id")))
		{
			$wheres[] = $prefix . "entity_id = " . Session::get("entity/entity_id");
		}
		$where = implode(" AND ", $wheres);

		if(empty($where))
		{
			$where = '1 = 1';
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

		# Group By
		$group_by = "";
		if(count(self::$_group_by) > 0)
		{
			$group_by .= "GROUP BY ";
			$groups = Array();
			foreach(self::$_group_by as $value)
			{
				if(is_array($value))
				{
					$groups[] = implode(", ", $value);
				}
			}
			$group_by .= implode(", ", $groups);
		}

		# Punto de partida y límite
		# MySQL Limit
		$limit = "";
		# SQL Server TOP
		$top = "";
		if(is_numeric($results))
		{
			self::$_limit = $results;
		}
		if(self::$_limit !== false)
		{
			if(DB_TYPE == "sqlsrv")
			{
				$top = "TOP " . self::$_limit;
			}
			else
			{
				$limit = "LIMIT ";
				if(self::$_offset !== false)
				{
					$limit .= (self::$_offset . ",");
				}
				$limit .= self::$_limit;
			}
		}

		self::init();
		$sql = "SELECT $modifier $top $select $extra_select FROM $table_name $join WHERE $where $order_by $group_by $limit";
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
				$object = $sth->fetchObject($class, Array(false));
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
				return $sth->fetchAll(PDO::FETCH_CLASS, $class, Array(false));
			}
			else
			{
				return $sth->fetchAll(PDO::FETCH_ASSOC);
			}
		}
	}

	/**
	 * Obtener todo
	 * 
	 * Alias de get("ALL")
	 * 
	 * Devuelve un arreglo de objetos con cada uno de los elementos. Si se ha utilizado el método
	 * select o join anteriormente, este método devuelve un arreglo asociativo con los resultados.
	 * 
	 * @return object|array Resultados
	 */
	public static function getAll()
	{
		return self::get("ALL");
	}

	/**
	 * Obtener todo en un array
	 * 
	 * Alias de get("ALL", false)
	 * 
	 * Devuelve un arreglo asociativo con todos los resultados de la consulta
	 * 
	 * @return array Resultados
	 */
	public static function getAllArray()
	{
		return self::get("ALL", false);
	}

	/**
	 * A un arrego
	 * 
	 * Convierte el objeto en un arreglo asociativo con cada una de las propiedades del objeto.
	 * 
	 * @return array Arreglo asociativo
	 */
	public function toArray()
	{
		return get_object_vars($this);
	}

	/**
	 * Introducir valores
	 * 
	 * Introduce varios valores desde un arreglo a cada una de las propiedades del objeto.
	 * 
	 * @return object El mismo objeto, con los valores
	 */
	public function set($array)
	{
		$data = get_object_vars($this);
		foreach($array as $key => $value)
		{
			if(array_key_exists($key, $data))
			{
				$key = str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));
				$this->{"set" . $key}($value);
			}
		}
		return $this;
	}

	/**
	 * Agregar contador
	 * 
	 * Agrega un campo con el nombre especificado por parámetro, en el que se enumeran los resultados.
	 * 
	 * @param int $field Nombre del campo contador (Por defecto num)
	 * 
	 * @return object Una instancia de la misma clase
	 */

	public static function addCounter($field = "num")
	{
		self::init();
		if(DB_TYPE == "sqlsrv")
		{
			self::$_extra_select .= ", ROW_NUMBER() OVER (ORDER BY (SELECT 1)) AS $field";
		}
		else
		{
			$sth = self::$_db->prepare("SET @row_number = 0");
			$sth->execute();
			self::$_extra_select .= ", (@row_number:=@row_number + 1) AS $field";
		}
		return new static();
	}

	/**
	 * Contar
	 * 
	 * Devuelve un número entero que representa el total de los resultados obtenidos en la consulta.
	 * 
	 * @return int El número de resultados
	 */
	public static function count()
	{
		self::$_select = Array("COUNT(*) AS total");
		$result = self::get();
		return $result["total"];
	}

	/**
	 * Listar
	 * 
	 * Devuelve un arreglo asociativo con cada elemento de la forma {id:"id", text:"text"}, donde id
	 * es la llave primaria de la tabla, y text es un campo texto especificado por parámetro.
	 * Esta forma permite utilizar los datos en listas desplegables y autocompletado.
	 * 
	 * Este método recibe parámetros de forma dinámica de la manera siguiente:
	 * Si el último parámetro es booleano, entonces, si es verdadero, se incluyen, además de id y text,
	 * todas las columnas de la consulta.
	 * 1) Si no recibe parámetros list() tomará como id la llave primaria y como text el primer campo
	 * que incluya _name en el nombre, o en su defecto, el siguiente campo de la tabla.
	 * 2) Si recibe un parámetro, tomará como id la llave primaria, y como text el campo especificado
	 * por parámetro.
	 * 3) Si recibe dos parámetros, el primero será el campo que se devolverá como id, y el
	 * segundo se devolverá como text
	 */
	public static function list()
	{
		$argv = func_get_args();
		$argc = func_num_args();
		$select = "";
		if($argc > 0 && is_bool($argv[$argc - 1]) && $argv[$argc - 1])
		{
			$argc--;
			$select = "*, ";
		}
		if($argc == 0)
		{
			$id = self::$_primary_key;
			$class = get_called_class();
			$vars = get_object_vars(new $class);
			$text = "";
			foreach(array_keys($vars) AS $key)
			{
				if($key == $id)
				{
					continue;
				}
				if($text == "" || strpos($key, "_name") !== false)
				{
					$text = $key;
				}
				if(strpos($key, "_name") !== false)
				{
					break;
				}
			}
		}
		elseif($argc == 1)
		{
			$id = self::$_primary_key;
			$text = $argv[0];
		}
		else
		{
			$id = $argv[0];
			$text = $argv[1];
		}
		return self::select($select . "$id AS id, $text AS text")->getAll();
	}

	/**
	 * Es nulo
	 * 
	 * Devuelve verdadero si la propiedad consultada es nula
	 * 
	 * @return boolean Estado nulo de la propiedad
	 */
	public function is_null($property)
	{
		return is_null($this->{$property});
	}

	/**
	 * Existe
	 * 
	 * Devuelve verdadero si la llave primaria tiene asignado un valor no nulo
	 * 
	 * @return boolean Estado de llave primaria
	 */
	public function exists()
	{
		return !empty($this->{self::$_primary_key});
	}

	/**
	 * Filas encontradas
	 * 
	 * Devuelve el número de filas encontradas cuando se usa SQL_CALC_FOUND_ROWS
	 * 
	 * @return int Total de filas encontradas
	 */
	public static function found_rows()
	{
		self::init();
		$sth = self::$_db->prepare("SELECT FOUND_ROWS() as frows");
		$sth->execute();
		$rows = $sth->fetch(PDO::FETCH_ASSOC);
		return $rows["frows"];
	}

	/**
	 * Siguiente número
	 * 
	 * Generador de siguiente número de un campo tomando en cuenta el último número generado en ese campo.
	 * Si la tabla contiene el campo entity_id, se tomará el último número generado por la entidad.
	 * Si la table contiene el campo _year, se tomará el último número generado en ese año.
	 * 
	 * @param string $field Campo a considerar
	 * @param array $conditions Arreglo asociativo conteniendo las condiciones (clave => valor) a
	 * considerar (Por ejemplo: Año, sucursal, etc)
	 * 
	 * @return int El siguiente número generado
	 */
	public static function next($field, $conditions = Array())
	{
		$last_model = self::orderBy($field, "DESC");
		foreach($conditions as $key => $value)
		{
			$last_model->where($key, $value);
		}
		$last = $last_model->first();
		$property = str_replace(' ', '', ucwords(str_replace('_', ' ', $field)));
		$next = $last->is_null($field) ? 1 : $last->{"get" . $property}() + 1;
		return $next;
	}

	/**
	 * Lista de elementos eliminados
	 * 
	 * Devuelve una lista de los elementos eliminados de la clase, evaluando el estado eliminado (NULL o cero, según corresponda).
	 * 
	 * @return array La lista de datos
	*/
	public static function list_deleted($from = "", $to = "")
	{
		if(!self::$_soft_delete)
		{
			return Array();
		}
		$id = self::$_primary_key;
		$class = get_called_class();
		$vars = get_object_vars(new $class);
		$text = "";
		foreach(array_keys($vars) AS $key)
		{
			if($key == $id)
			{
				continue;
			}
			if($text == "" || strpos($key, "_name") !== false)
			{
				$text = $key;
			}
			if(strpos($key, "_name") !== false)
			{
				break;
			}
		}
		$table_name = self::$_table_name;
		$query = self::select("$table_name.*, $table_name.$id AS element_id, $table_name.$text AS description");
		if(self::$_timestamps)
		{
			$query->select("creator.user_name AS creator_name, editor.user_name AS editor_name")->join(DB_PREFIX . "users AS creator", "$table_name.creation_user = creator.user_id")->join(DB_PREFIX . "users AS editor", "$table_name.edition_user = editor.user_id");
		}
		if(self::$_deleted_status === 0)
		{
			$query->where("$table_name.status", 0);
		}
		else
		{
			$query->where("$table_name.status IS NULL");
		}
		if(self::$_timestamps && !empty($from))
		{
			$query->where("$table_name.edition_time", ">=", $from . " 00:00:00");
		}
		if(self::$_timestamps && !empty($to))
		{
			$query->where("$table_name.edition_time", "<=", $to . " 23:59:59");
		}
		return $query->getAll();
	}

	/**
	 * Obtener ID
	 * 
	 * Retorna el valor de la llave primaria del objeto.
	 * 
	 * @return int ID del objeto
	 */
	public function _getId()
	{
		if(empty(self::$_primary_key))
		{
			return 0;
		}
		return $this->{self::$_primary_key};
	}

	/**
	 * Where in
	 * 
	 * Agrega una condición Where In con los valores de un arreglo unidimensional
	 * 
	 * @param Array<string> $list La lista de elementos a ser incluídos.
	 * @param string $field El nombre del campo a evaluar. Si no se especifica, se toma por defecto la llave primaria.
	 * 
	 * @return object Una instancia de la misma clase
	*/
	public static function whereIn($list, $field = null)
	{
		if($field == null)
		{
			if(self::$_primary_key == null)
			{
				return new static();
			}
			$field = self::$_primary_key;
		}
		if(!is_array($list))
		{
			return new static();
		}
		if(count($list) == 0)
		{
			return self::where($field . " IN (NULL)");
		}

		$objects = "'" . implode("','", $list) . "'";
		return self::where($field . " IN ($objects)");
	}

	/**
	 * Where in
	 * 
	 * Agrega una condición Where In excluyendo los valores de un arreglo unidimensional
	 * 
	 * @param Array<string> $list La lista de elementos a ser incluídos.
	 * @param string $field El nombre del campo a evaluar. Si no se especifica, se toma por defecto la llave primaria.
	 * 
	 * @return object Una instancia de la misma clase
	*/
	public static function whereNotIn($list, $field = null)
	{
		if($field == null)
		{
			if(self::$_primary_key == null)
			{
				return new static();
			}
			$field = self::$_primary_key;
		}
		if(!is_array($list))
		{
			return new static();
		}
		if(count($list) == 0)
		{
			return new static();
		}

		$objects = "'" . implode("','", $list) . "'";
		return self::where($field . " NOT IN ($objects)");
	}

	################ Validación de datos
	/**
	 * Validar tamaño de cadenas
	 * 
	 * Valida el tamaño de una cadena para asegurarse de que un campo char, varchar, text, tinytext, smalltext,
	 * mediumtext o longtext reciba como máximo el número de caracteres que soporta.
	 */
	private static function validateStringSize(&$string, $size)
	{
		if($string != null && strlen($string) > $size)
		{
			$string = substr($string, 0, $size);
		}
	}

	################ Otras utilidades de las clases
	/**
	 * Campos
	 * 
	 * Devuelve un string conteniendo un conjunto de campos separados por comas, unidos al nombre de la tabla correspondiente
	 * a la clase. Por ejemplo: la llamada usersModel('username', 'password'), debería devolver: 'users.username, users.password'.
	 * Esto es muy útil cuando se usan prefijos en la base de datos, y en ese caso, el nombre de la tabla no se corresponde con
	 * el nombre de la clase. De esa manera se evita llamar directamente el nombre de la tabla desde el método select.
	 * 
	 * @return string Cada uno de los campos separados por comas
	 */
	public static function fields()
	{
		$fieldNames = func_get_args();
		$class = new static();
		$result = "";
		foreach($fieldNames as $name)
		{
			if($name == "*" || property_exists($class, $name))
			{
				if(strlen($result) > 0)
				{
					$result .= (", " . self::$_table_name . "." . $name);
				}
				else
				{
					$result = self::$_table_name . "." . $name;
				}
			}
		}
		return $result;
	}
}

/**
 * Clase DB
 * 
 * Forma abstracta de usar ORM.
 * 
 * A diferencia de las tablas generadas en los modelos, esta clase no contiene una tabla ni propiedades
 * asociadas.
 */
class DB
{
	use ORM;

	private static $_table_name = "";

	private static $_soft_delete = false;

	public static function from($table_name)
	{
		if(empty(self::$_table_name))
		{
			self::$_table_name = $table_name;
		}
		else
		{
			self::$_table_name .= ", " . $table_name;
		}
		return new static();
	}
}
?>
