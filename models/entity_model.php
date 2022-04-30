<?php
/**
* BlackPHP Entity Model
* @author: Edwin Fajardo <contacto@edwinfajardo.com>
* Date-time: 2022-02-05 22:17
 */

class entity_Model extends Model
{
	public function get_entity()
	{
		$query =	"SELECT *
					FROM entities
					LIMIT 1";
		return $this->db->select($query, false);
	}

	public function get_entity_by_id($entity_id)
	{
		$params = Array(
			"entity_id" => $entity_id
		);
		$query =	"SELECT *
					FROM entities
					WHERE entity_id = :entity_id";
		return $this->db->select($query, false, $params);
	}

	public function get_entity_to_update($entity_id)
	{
		$params = Array(
			"entity_id" => $entity_id
		);
		$query =	"SELECT c.*,
						u.user_name,
						u.nickname,
						u.theme_id
					FROM entities AS c
						LEFT JOIN users AS u
							ON c.admin_user = u.user_id
					WHERE c.entity_id = :entity_id";
		return $this->db->select($query, false, $params);
	}

	public function get_entity_by_subdomain($subdomain)
	{
		$params = Array(
			"subdomain" => $subdomain
		);
		$query =	"SELECT *
					FROM entities
					WHERE entity_subdomain = :subdomain";
		return $this->db->select($query, false, $params);
	}
	public function update_entity($data)
	{
		$result = Array();
		$result["affected"] = $this->db->update("entities", $data, "entity_id = :entity_id");
		return $result;
	}

	public function get_all_modules()
	{
		$query =	"SELECT *
					FROM app_modules
					WHERE status = 1
					ORDER BY default_order";
		return $this->db->select($query, true);
	}

	public function get_methods_by_module($module_id)
	{
		$params = Array(
			"module_id" => $module_id
		);
		$query =	"SELECT *
					FROM app_methods
					WHERE module_id = :module_id
						AND status = 1";
		return $this->db->select($query, true, $params);
	}

	public function revoke_entity_modules($entity_id)
	{
		$data = Array(
			"entity_id" => $entity_id,
			"status" => 0
		);
		$result = Array();
		$result["affected"] = $this->db->update("entity_modules", $data, "entity_id = :entity_id");
		return $result;
	}

	public function get_entity_module($entity_id, $module_id)
	{
		$params = Array(
			"entity_id" => $entity_id,
			"module_id" => $module_id
		);
		$query =	"SELECT *
					FROM entity_modules
					WHERE entity_id = :entity_id
						AND module_id = :module_id";
		return $this->db->select($query, false, $params);
	}

	public function set_entity_module($data)
	{
		$result = Array();
		$result["affected"] = $this->db->insert("entity_modules", $data);
		$result["id"] = $this->db->lastInsertId();
		return $result;
	}

	public function update_entity_module($data)
	{
		$result = Array();
		$result["affected"] = $this->db->update("entity_modules", $data, "entity_id = :entity_id AND module_id = :module_id");
		return $result;
	}

	public function revoke_entity_methods($entity_id)
	{
		$data = Array(
			"entity_id" => $entity_id,
			"status" => 0
		);
		$result = Array();
		$result["affected"] = $this->db->update("entity_methods", $data, "entity_id = :entity_id");
		return $result;
	}

	public function get_entity_method($entity_id, $method_id)
	{
		$params = Array(
			"entity_id" => $entity_id,
			"method_id" => $method_id
		);
		$query =	"SELECT *
					FROM entity_methods
					WHERE entity_id = :entity_id
						AND method_id = :method_id";
		return $this->db->select($query, false, $params);
	}

	public function set_entity_method($data)
	{
		$result = Array();
		$result["affected"] = $this->db->insert("entity_methods", $data);
		$result["id"] = $this->db->lastInsertId();
		return $result;
	}

	public function update_entity_method($data)
	{
		$result = Array();
		$result["affected"] = $this->db->update("entity_methods", $data, "entity_id = :entity_id AND method_id = :method_id");
		return $result;
	}

	public function get_entity_modules($entity_id)
	{
		$params = Array(
			"entity_id" => $entity_id
		);
		$query =	"SELECT *,
						m.module_id AS id
					FROM entity_modules AS cm,
						app_modules AS m
					WHERE cm.entity_id = :entity_id
						AND m.module_id = cm.module_id
						AND cm.status = 1
					ORDER BY module_order ASC";
		return $this->db->select($query, true, $params);
	}

	public function get_entity_methods($entity_id, $module_id)
	{
		$params = Array(
			"entity_id" => $entity_id,
			"module_id" => $module_id
		);
		$query =	"SELECT *,
						m.method_id AS label
					FROM entity_methods AS cm,
						app_methods AS m
					WHERE cm.entity_id = :entity_id
						AND m.method_id = cm.method_id
						AND m.module_id = :module_id
						AND cm.status = 1
					ORDER BY method_order ASC";
		return $this->db->select($query, true, $params);
	}

	public function get_all_entity_methods($entity_id)
	{
		$params = Array(
			"entity_id" => $entity_id
		);
		$query =	"SELECT *,
						m.method_id AS id
					FROM entity_methods AS cm,
						app_methods AS m
					WHERE cm.entity_id = :entity_id
						AND m.method_id = cm.method_id
						AND cm.status = 1
					ORDER BY method_order ASC";
		return $this->db->select($query, true, $params);
	}

	public function get_module_by_name($module_url)
	{
		$params = Array("module_url" => $module_url);
		$query =	"SELECT *
					FROM app_modules
					WHERE module_url = :module_url";
		return $this->db->select($query, false, $params);
	}

	public function set_entity($data)
	{
		$result = Array();
		$result["affected"] = $this->db->insert("entities", $data);
		$result["id"] = $this->db->lastInsertId();
		return $result;
	}

	public function get_themes()
	{
		$query =	"SELECT theme_id AS id,
						theme_name AS text
					FROM app_themes";
		return $this->db->select($query, true);
	}

	public function get_first_theme()
	{
		$query =	"SELECT *
					FROM app_themes
					LIMIT 1";
		return $this->db->select($query, false, $params);
	}

	public function get_restrictions($entity_id)
	{
		/*$params = Array(
			"entity_id" => $entity_id
		);
		$query =	"SELECT daily_close,
						product_images,
						barcode,
						out_of_stock,
						cash_control,
						customer_po,
						delivery
					FROM entities
					WHERE comp_id = :entity_id";
		return $this->db->select($query, false, $params);*/
		return Array();
	}
}
?>
