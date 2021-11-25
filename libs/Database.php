<?php

class Database extends PDO
{
	public function __construct($DB_TYPE, $DB_HOST,$DB_PORT, $DB_NAME, $DB_USER, $DB_PASS)
	{
		parent::__construct($DB_TYPE.':host='.$DB_HOST.';port='.$DB_PORT.';dbname='.$DB_NAME, $DB_USER, $DB_PASS);
		parent::setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$this->exec('SET NAMES "utf8" COLLATE "utf8_general_ci"');
	}
	
	/**
	 * select
	 * @param string $sql An SQL string
	 * @param array $array Paramters to bind
	 * @param constant $fetchMode A PDO Fetch mode
	 * @return mixed
	 */
	public function select($sql, $multiple = false, $array = array(), $fetchMode = PDO::FETCH_ASSOC)
	{
		$sth = $this->prepare($sql);
		foreach ($array as $key => $value) {
			$sth->bindValue("$key", $value);
		}
		$sth->execute();
		if($multiple)
		{
			return $sth->fetchAll($fetchMode);
		}
		else
		{
			return $sth->fetch($fetchMode);
		}
	}
	
	/**
	 * insert
	 * @param string $table A name of table to insert into
	 * @param string $data An associative array
	 */
	public function insert($table, $data)
	{
		$fieldNames = implode(',', array_keys($data));
		$fieldValues = ':' . implode(', :', array_keys($data));

		$sth = $this->prepare("INSERT INTO $table ($fieldNames) VALUES ($fieldValues)");
		
		foreach ($data as $key => $value) {
			$sth->bindValue(":$key", $value);
		}
		$sth->execute();
		return $sth->rowCount();
	}
	
	/*	Added 2020-06-09 */
	# Inser values from a select
	public function insert_select($table, $query, $extra = "")
	{
		$sth = $this->prepare("INSERT INTO $table $query $extra");
		$sth->execute();
		return $sth->rowCount();
	}

	public function insert_ignore($table, $data)
	{
		$fieldNames = implode(',', array_keys($data));
		$fieldValues = ':' . implode(', :', array_keys($data));

		$sth = $this->prepare("INSERT IGNORE INTO $table ($fieldNames) VALUES ($fieldValues)");

		foreach ($data as $key => $value) {
			$sth->bindValue(":$key", $value);
		}
		$sth->execute();
		return $sth->rowCount();
	}

	/**
	 * update
	 * @param string $table A name of table to insert into
	 * @param string $data An associative array
	 * @param string $where the WHERE query part
	 */
	public function update($table, $data, $where, $vars = "")
	{
		$fieldDetails = "";
		foreach($data as $key=> $value) {
			$fieldDetails .= "$key=:$key,";
		}
		$fieldDetails .= $vars;
		$fieldDetails = rtrim($fieldDetails, ',');
		
		$sth = $this->prepare("UPDATE $table SET $fieldDetails WHERE $where");
		
		foreach ($data as $key => $value) {
			$sth->bindValue(":$key", $value);
		}
		$sth->execute();
		return $sth->rowCount();
	}
	
	public function execute($query, $data = Array())
	{
		$sth = $this->prepare($query);

		foreach ($data as $key => $value) {
			$sth->bindValue(":$key", $value);
		}
		$sth->execute();
		return $sth->rowCount();
	}

	/**
	 * delete
	 * 
	 * @param string $table
	 * @param string $where
	 * @param integer $limit
	 * @return integer Affected Rows
	 */
	public function delete($table, $where)
	{
		return $this->exec("DELETE FROM $table WHERE $where");
	}

	public function printQuery($query, $params) {
		$keys = array();
		$values = $params;

		# build a regular expression for each parameter
		foreach ($params as $key => $value) {
			if (is_string($key)) {
				$keys[] = '/'.$key.'/';
			} else {
				$keys[] = '/[?]/';
			}

			if (is_string($value))
				$values[$key] = "'" . $value . "'";

			if (is_array($value))
				$values[$key] = "'" . implode("','", $value) . "'";

			if (is_null($value))
				$values[$key] = 'NULL';
		}

		$query = preg_replace($keys, $values, $query);

		return $query;
	}
	
	/* Added by Edwin Fajardo */
	# Get the found rows when LIMIT clause is used
	public function found_rows($fetchMode = PDO::FETCH_ASSOC)
	{
		$sth = $this->prepare("SELECT FOUND_ROWS() as frows");
		$sth->execute();
		return $sth->fetch($fetchMode);
	}
}
?>