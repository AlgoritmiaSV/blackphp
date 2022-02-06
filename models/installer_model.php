<?php

#	Installer model
#	By: Edwin Fajardo
#	Date-time: 2021-09-18 01:54

class installer_Model extends Model
{
	public function get_access($nickname, $password)
	{
		$params = Array("nickname" => $nickname,
					"password" => md5($password),
					"status" => 1);
		$query =	"SELECT *
					FROM app_installers
					WHERE installer_nickname = :nickname
						AND installer_password = :password
						AND status = :status";
		return $this->db->select($query, false, $params);
	}
}
?>
