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
		$this->view->data["dependencies"] = [
			["name" => "BlackPHP (Framework)", "version" => "1.0.0", "authors" => "Red Teleinform&aacute;tica", "link" => "https://github.com/RedTeleinformatica/BlackPHP", "license" => "MIT License"],
				["name" => "Chart.js", "version" => "3.8.0", "authors" => "&copy;2014-2022 Chart.js Contributors", "link" => "https://www.chartjs.org", "license" => "MIT License"],
			["name" => "chartjs-plugin-datalabels", "version" => "2.0.0", "authors" => "chartjs-plugin-datalabels contributors", "link" => "https://chartjs-plugin-datalabels.netlify.app", "license" => "MIT License"],
			["name" => "Image Uploader", "version" => "1.2.3", "authors" => "Christian Bayer", "link" => "https://github.com/christianbayer/image-uploader", "license" => "MIT License"],
			["name" => "jAlert", "version" => "4.5.1", "authors" => "HTMLGuy, LLC", "link" => "https://htmlguyllc.github.io/jAlert/", "license" => "MIT License"],
			["name" => "jQuery", "version" => "3.2.1", "authors" => "JS Foundation and other contributors", "link" => "https://jquery.org", "license" => "MIT License"],
			["name" => "jQuery UI", "version" => "1.12.1", "authors" => "jQuery Foundation and other contributors", "link" => "http://jqueryui.com", "license" => "MIT License"],
			["name" => "jQuery.floatThead", "version" => "2.2.1", "authors" => "Misha Koryak", "link" => "https://mkoryak.github.io/floatThead/", "license" => "MIT License"],
			["name" => "jqPagination", "version" => "1.4.1", "authors" => "Ben Everard", "link" => "http://beneverard.github.com/jqPagination", "license" => "GPL v3"],
			["name" => "printThis", "version" => "1.15.1", "authors" => "Jason Day", "link" => "https://jasonday.github.io/printThis/", "license" => "MIT License"],
			["name" => "Select2", "version" => "4.0.4", "authors" => "Kevin Brown, Igor Vaynberg, and Select2 contributors", "link" => "https://select2.org/", "license" => "MIT License"],
			["name" => "PHP User Agent Parser", "version" => "1.6.0", "authors" => "Jesse Donat", "link" => "https://donatstudios.com/PHP-Parser-HTTP_USER_AGENT", "license" => "MIT License"],
			["name" => "PHP Spread Sheet", "version" => "1.23.0", "authors" => "PhpSpreadsheet Contributors", "link" => "https://phpspreadsheet.readthedocs.io/", "license" => "MIT License"]
		];
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
}
?>
