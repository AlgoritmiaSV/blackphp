<?php
trait Information
{
	/**
	 * Acerca de
	 * 
	 * Muestra información acerca del sistema.
	 * 
	 * @return void
	 */
	public function About()
	{
		$this->session_required("html", $this->module);
		$this->view->data["title"] = sprintf(_("About %s"), $this->system_name);
		$this->view->standard_details();
		$this->view->data["nav"] = $this->view->render("main/nav", true);
		$this->view->data["content_id"] = "info_details";
		$this->view->data["content"] = $this->view->render("content_loader", true);
		$this->view->render('main');
	}

	/**
	 * Carga de información del sistema
	 * 
	 * Imprime, en formato HTML, la información del sistema: datos de la última actualización e 
	 * información de contacto.
	 * 
	 * @return void
	 */
	public function info_details_loader($mode = "embedded")
	{
		$info = Array();
		if(file_exists("app_info.json"))
		{
			$info = json_decode(file_get_contents("app_info.json"), true);
			$info["last_update_ago"] = date_utilities::sql_date_to_ago($info["last_update"]);
			$info["last_update_ago"] = sprintf(_("%s ago"), $info["last_update_ago"]);
			$info["last_update"] = date_utilities::sql_date_to_string($info["last_update"], true);
		}
		else
		{
			$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($_SERVER["DOCUMENT_ROOT"]), RecursiveIteratorIterator::SELF_FIRST);
			$last_modified = 0;
			foreach($files as $file_object)
			{
				$modified = $file_object->getMTime();
				if($modified > $last_modified)
				{
					$last_modified = $modified;
				}
			}
			$info["last_update"] = date_utilities::sql_date_to_string(Date("Y-m-d H:i:s", $last_modified), true);
			$info["last_update_ago"] = date_utilities::sql_date_to_ago(Date("Y-m-d H:i:s", $last_modified));
			$info["last_update_ago"] = sprintf(_("%s ago"), $info["last_update_ago"]);
			$info["version"] = "1.0";
			$info["number"] = "0";
		}
		foreach($info as $key => $item)
		{
			$this->view->data[$key] = $item;
		}
		$this->view->data["dependencies"] = array_merge($this->getNpmDependencies(), $this->getComposerDependencies());
		if($mode == "standalone")
		{
			$this->view->data["title"] = sprintf(_("About %s"), $this->system_name);
			$this->view->standard_details();
			$this->view->add("styles", "css", Array(
				'styles/standalone.css'
			));
			$this->view->restrict[] = "embedded";
			$this->view->data["content"] = $this->view->render('settings/info_details', true);
			$this->view->render('clean_main');
		}
		else
		{
			$this->view->render('settings/info_details');
		}
	}

	/**
	 * Read npm dependencies from package.json and package-lock.json
	 * Returns JSON with: name, version, license
	 */
	function getNpmDependencies() {
		$packageJson = json_decode(file_get_contents('package.json'), true);
		$lockJson    = json_decode(file_get_contents('package-lock.json'), true);

		$dependencies = [];

		if (!empty($packageJson['dependencies'])) {
			foreach ($packageJson['dependencies'] as $name => $versionConstraint) {
				if (isset($lockJson['packages']["node_modules/$name"])) {
					$pkg = $lockJson['packages']["node_modules/$name"];
					$dependencies[] = [
						'name'    => $name,
						'version' => $pkg['version'] ?? $versionConstraint,
						'license' => $pkg['license'] ?? 'unknown'
					];
				}
			}
		}

		return $dependencies;
	}

	/**
	 * Read Composer dependencies from composer.json and composer.lock
	 * Returns JSON with: name, version, license
	 */
	function getComposerDependencies() {
		$composerJson = json_decode(file_get_contents('composer.json'), true);
		$lockJson     = json_decode(file_get_contents('composer.lock'), true);

		$dependencies = [];

		if (!empty($composerJson['require'])) {
			foreach ($composerJson['require'] as $name => $versionConstraint) {
				foreach ($lockJson['packages'] as $pkg) {
					if ($pkg['name'] === $name) {
						$dependencies[] = [
							'name'    => $pkg['name'],
							'version' => $pkg['version'] ?? $versionConstraint,
							'license' => !empty($pkg['license']) ? implode(', ', $pkg['license']) : 'unknown'
						];
						break;
					}
				}
			}
		}

		return $dependencies;
	}
}
?>
