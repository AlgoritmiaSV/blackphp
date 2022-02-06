<?php

#	Users controller
#	By: Edwin Fajardo
#	Date-time: 2020-06-13 00:35

class user_Model extends Model
{
	public function get_access($nickname, $password, $entity_id)
	{
		$params = Array("nickname" => $nickname,
					"password" => md5($password),
					"status" => 1,
					"entity_id" => $entity_id);
		$query =	"SELECT u.*,
						t.theme_url
					FROM users AS u
					LEFT JOIN app_themes AS t
						ON u.theme_id = t.theme_id
					WHERE u.entity_id = :entity_id
						AND u.nickname = :nickname
						AND u.password = :password
						AND u.status = :status";
		return $this->db->select($query, false, $params);
	}

	public function get_user($user_id)
	{
		$params = Array("user_id" => $user_id);
		$query =	"SELECT *
					FROM users
					WHERE user_id = :user_id";
		return $this->db->select($query, false, $params);
	}

	public function get_user_by_nickname($nickname, $entity_id)
	{
		$params = Array(
			"nickname" => $nickname,
			"entity_id" => $entity_id
		);
		$query =	"SELECT *
					FROM users
					WHERE entity_id = :entity_id
						AND nickname = :nickname";
		return $this->db->select($query, false, $params);
	}

	public function set_access($data)
	{
		$result = Array();
		$result["affected"] = $this->db->insert_ignore("user_modules", $data);
		$result["id"] = $this->db->lastInsertId();
		return $result;
	}

	public function get_module_access($user_id, $module_id)
	{
		$params = Array("user_id" => $user_id,
					"module_id" => $module_id);
		$query =	"SELECT *
					FROM user_modules
					WHERE user_id = :user_id
						AND module_id = :module_id";
		return $this->db->select($query, false, $params);
	}

	public function update_access($data)
	{
		$result = Array();
		$result["affected"] = $this->db->update("user_modules", $data, "module_id = :module_id AND user_id = :user_id");
		return $result;
	}

	public function revoke_permissions($data)
	{
		$result = Array();
		$result["affected"] = $this->db->update("user_modules", $data, "user_id = :user_id");
		return $result;
	}

	public function revoke_branches($data)
	{
		$result = Array();
		$result["affected"] = $this->db->update("user_branches", $data, "user_id = :user_id");
		return $result;
	}

	public function get_module_by_name($module_url)
	{
		$params = Array("module_url" => $module_url);
		$query =	"SELECT *
					FROM app_modules
					WHERE module_url = :module_url";
		return $this->db->select($query, false, $params);
	}

	public function get_all($entity_id)
	{
		$params = Array(
			"system_user" => "system",
			"status" => 0,
			"entity_id" => $entity_id
		);
		$query =	"SELECT *
					FROM users
					WHERE entity_id = :entity_id
						AND NOT nickname = :system_user
						AND NOT status = :status
					ORDER BY user_name ASC";
		return $this->db->select($query, true, $params);
	}

	public function set_user($data)
	{
		$result = Array();
		$result["affected"] = $this->db->insert("users", $data);
		$result["id"] = $this->db->lastInsertId();
		return $result;
	}

	public function update_user($data)
	{
		$result = Array();
		$result["affected"] = $this->db->update("users", $data, "user_id = :user_id");
		return $result;
	}

	public function get_permissions($user_id, $module)
	{
		$params = Array("user_id" => $user_id,
					"module_url" => $module,
					"status" => 1);
		$query =	"SELECT access_type
					FROM users AS u,
						user_modules AS a,
						app_modules AS m
					WHERE u.user_id = :user_id
						AND u.user_id = a.user_id
						AND a.module_id = m.module_id
						AND m.module_url = :module_url
						AND a.status = :status";
		return $this->db->select($query, false, $params);
	}

	public function get_all_permissions($user_id)
	{
		$params = Array("user_id" => $user_id,
					"status" => 1);
		$query =	"SELECT m.module_id AS id
					FROM users AS u,
						user_modules AS a,
						app_modules AS m
					WHERE u.user_id = :user_id
						AND u.user_id = a.user_id
						AND a.module_id = m.module_id
						AND a.status = :status";
		return $this->db->select($query, true, $params);
	}

	public function get_browser($user_agent)
	{
		$params = Array("user_agent" => $user_agent);
		$query =	"SELECT *
					FROM browsers
					WHERE user_agent = :user_agent";
		return $this->db->select($query, false, $params);
	}

	public function set_browser($data)
	{
		$result = Array();
		$result["affected"] = $this->db->insert("browsers", $data);
		$result["id"] = $this->db->lastInsertId();
		return $result;
	}

	public function set_user_session($data)
	{
		$result = Array();
		$result["affected"] = $this->db->insert("user_sessions", $data);
		$result["id"] = $this->db->lastInsertId();
		return $result;
	}

	public function get_branch($branch_id, $user_id)
	{
		$params = Array(
			"branch_id" => $branch_id,
			"user_id" => $user_id
		);
		$query =	"SELECT b.*
					FROM branches AS b,
						user_branches AS ub
					WHERE b.branch_id = :branch_id
						AND ub.branch_id = b.branch_id
						AND ub.user_id = :user_id
						AND ub.status = 1";
		return $this->db->select($query, false, $params);
	}

	public function get_user_branch($branch_id, $user_id)
	{
		$params = Array(
			"branch_id" => $branch_id,
			"user_id" => $user_id
		);
		$query =	"SELECT *
					FROM user_branches AS ub
					WHERE branch_id = :branch_id
						AND user_id = :user_id
						AND status = 1";
		return $this->db->select($query, false, $params);
	}

	public function get_user_entity_modules($entity_id, $user_id)
	{
		$params = Array(
			"entity_id" => $entity_id,
			"user_id" => $user_id
		);
		$query =	"SELECT m.*,
						um.access_type
					FROM entity_modules AS cm,
						app_modules AS m,
						user_modules AS um
					WHERE cm.entity_id = :entity_id
						AND m.module_id = cm.module_id
						AND cm.status = 1
						AND um.module_id = m.module_id
						AND um.user_id = :user_id
						AND um.status = 1
					ORDER BY module_order ASC";
		return $this->db->select($query, true, $params);
	}

	public function get_theme($theme_id)
	{
		$params = Array("theme_id" => $theme_id);
		$query =	"SELECT *
					FROM app_themes
					WHERE theme_id = :theme_id";
		return $this->db->select($query, false, $params);
	}

	public function get_sessions_by_user($offset, $user_id)
	{
		$params = Array(
			"user_id" => $user_id
		);
		$query =	"SELECT s.ip_address,
						s.date_time,
						b.browser_name,
						b.platform
					FROM user_sessions AS s,
						browsers AS b
					WHERE s.user_id = :user_id
						AND s.browser_id = b.browser_id
					ORDER BY date_time DESC
					LIMIT $offset, 10";
		return $this->db->select($query, true, $params);
	}
}
?>
