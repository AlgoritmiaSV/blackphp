<?php
/**
 * Modelo
 * 
 * Clase base para el funcionamiento de modelos.
 */

class Model
{
	/**
	 * Constructor de la clase
	 * 
	 * Inicializa la propiedad $db con la conexión a la base de datos
	 */
	function __construct()
	{
		$this->db = new Database(DB_TYPE, DB_HOST, DB_PORT,DB_NAME, DB_USER, DB_PASS);
	}
	
	/**
	 * Filas encontradas
	 * 
	 * Devuelve el total de filas encontradas en una consulta donde se ha utilizado
	 * sql_calc_found_rows
	 * 
	 * @return int Cantidad de registros encontrados
	 */
	public function found_rows()
	{
		$found_rows = $this->db->found_rows();
		return $found_rows["frows"];
	}

	/**
	 * Inicializa la variable row_number en SQL.
	 * 
	 * Util para cuando se quiere incluir el número de cada item dentro de la consulta.
	 */
	public function start_row_number()
	{
		$this->db->execute("SET @row_number = 0");
	}
}
?>
