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
		if(get_called_class() == "DB")
		{
			self::$_table_name = null;
		}
		self::$_modifier = "";
		self::$_extra_select = "";
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
			self::$_db = new PDO(DB_TYPE.':host='.DB_HOST.';port='.DB_PORT.';dbname='.DB_NAME, DB_USER, DB_PASS);
			self::$_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			self::$_db->exec('SET NAMES "utf8" COLLATE "utf8_general_ci"');
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
	 * 
	 * @return object Objeto de la clase que llamó al método
	 */
	public static function find($id)
	{
		return self::where(self::$_primary_key, $id)->get();
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
		if(self::$_table_type == "VIEW")
		{
			return 0;
		}
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
		$sth = null;
		$table_name = self::$_table_name;
		$primary_key = self::$_primary_key;
		if(empty($this->{$primary_key}))
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
	 * Actualizar
	 * 
	 * Actualiza todas las filas coincidentes con las condiciones previamente establecidas
	 * en where. Si no se han establecido condiciones, actualiza todas las filas.
	 * 
	 * @return void
	 */
	public static function update($data)
	{
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
					$status = true;
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
		if(self::$_soft_delete && !$status)
		{
			$wheres[] = $prefix . "status != 0";
		}
		if(property_exists(new static(), "entity_id") && !$entity && Session::get("entity") != null)
		{
			$wheres[] = $prefix . "entity_id = " . Session::get("entity")["entity_id"];
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
	 * Elimina el elemento. Si la propiedad $soft_delete es verdadera, entonces actualiza el estado
	 * $status = 0
	 * 
	 * @return int Filas eliminadas
	 */
	public function delete()
	{
		if(self::$_table_type == "VIEW")
		{
			return 0;
		}
		$affected = 0;
		if(self::$_soft_delete)
		{
			if(self::$_timestamps)
			{
				$this->setEdition_user(Session::get("user_id"));
				$this->setEdition_time(Date("Y-m-d H:i:s"));
			}
			$this->setStatus(0);
			$affected = $this->save();
		}
		else
		{
			$table_name = self::$_table_name;
			$primary_key = self::$_primary_key;
			$id = $this->{$primary_key};
			self::init();
			$sth = self::$_db->prepare("DELETE FROM $table_name WHERE $primary_key = :id");
			$sth->bindValue(":id", $id);
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
	 * Obtener
	 * 
	 * Realiza la consulta con los paràmetros (Considiones) previamente configurados en otros
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
	 * @return object|array Resultados de la consulta
	 */
	public static function get($results = "FIRST", $objects = true)
	{
		$table_name = self::$_table_name;
		$prefix = "";
		$status = false;
		$entity = false;
		if(strpos($table_name, ",") !== false)
		{
			$objects = false;
			$status = true;
		}

		# Select
		$select = "*";
		if(count(self::$_select) > 0)
		{
			$select = implode(",", self::$_select);
			$objects = false;
		}

		# Modifier
		$modifier = self::$_modifier;

		# Opciones adicionales
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
						$join .= "LEFT JOIN $value[0] ON $table_name.$value[1] = $value[0].$value[2] ";
					}
					elseif(count($value) == 2)
					{
						$join .= "LEFT JOIN $value[0] ON $table_name.$value[1] = $value[0].$value[1] ";
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
					$status = true;
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
		if(self::$_soft_delete && !$status)
		{
			$wheres[] = $prefix . "status != 0";
		}
		if(property_exists(new static(), "entity_id") && !$entity && Session::get("entity") != null)
		{
			$wheres[] = $prefix . "entity_id = " . Session::get("entity")["entity_id"];
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

		#Results
		$limit = "";
		if(is_numeric($results))
		{
			$limit = "LIMIT $results";
		}

		self::init();
		$sql = "SELECT $modifier $select $extra_select FROM $table_name $join WHERE $where $order_by $limit";
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
		$data = get_object_vars($this);
		return $data;
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
				$this->{"set" . ucfirst($key)}($value);
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
		$sth = self::$_db->prepare("SET @row_number = 0");
		$sth->execute();
		self::$_extra_select .= ", (@row_number:=@row_number + 1) AS $field";
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
	 * 1) Si no recibe parámetros list() tomará como id la llave primaria y como text el primer campo
	 * que incluya _name en el nombre, o en su defecto, el siguiente campo de la tabla.
	 * 2) Si recibe un parámetro, tomará como id la llave primarioa, y como text el campo especificado
	 * por parámetro.
	 * 3) Si recibe dos parámetros, el primero será el campo que swe devolverá como id, y el
	 * segundo se devolverá como text
	 */
	public static function list()
	{
		$argv = func_get_args();
		$argc = func_num_args();
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
		return self::select("$id AS id, $text AS text")->getAll();
	}

	public function is_null($property)
	{
		return is_null($this->{$property});
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

	public static function found_rows()
	{
		self::init();
		$sth = self::$_db->prepare("SELECT FOUND_ROWS() as frows");
		$sth->execute();
		$rows = $sth->fetch(PDO::FETCH_ASSOC);
		return $rows["frows"];
	}
}
?>
