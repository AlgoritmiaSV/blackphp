<?php

class Model
{
	function __construct()
	{
		$this->db = new Database(DB_TYPE, DB_HOST, DB_PORT,DB_NAME, DB_USER, DB_PASS);
	}
	
	function found_rows()
	{
		$found_rows = $this->db->found_rows();
		return $found_rows["frows"];
	}

	function start_row_number()
	{
		$this->db->execute("SET @row_number = 0");
	}
}
?>
