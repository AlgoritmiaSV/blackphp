<?php

#	View
#	Edited By: Edwin Fajardo
#	Date-time: 2017-09-12 14:00

class View
{
	public $data; # Data from the controller to render in the template
	public $restrict; # Hide parts of HTML
	function __construct()
	{
		$this->restrict = Array();
		$this->data = Array();
	}

	public function render($name, $return = false)
	{
		$filename = 'views/' . $name . '.html';
		if (!file_exists($filename)) {
			return "Error loading template file ($filename).";
		}
		$template = file_get_contents($filename);

		/* \1: Temporary value for the new line character to avoid problems with preg_replace */
		$template = str_replace("\r\n", "\1", $template);
		$template = str_replace("\n", "\1", $template);

		foreach ($this->data as $key => $value)
		{
			if(!is_array($value))
			{
				$tagToReplace = "{{ $key }}";
				$template = str_replace($tagToReplace, $value, $template);
			}
			else
			{
				$first = true;
				$text = "";
				$array_key = "[[ $key ]]";
				$begin = strpos($template, $array_key);
				if($begin == false)
				{
					continue;
				}
				$begin += strlen($array_key);
				$end = strpos($template, "[[/ $key ]]");
				$sub = substr($template, $begin, $end - $begin);
				foreach($value as $item_key => $item_value)
				{
					$line = $sub;
					foreach($item_value as $cell_key=>$cell_value)
					{
						$tagToReplace = "{{ $cell_key }}";
						$line = str_replace($tagToReplace, $cell_value, $line);
					}
					$text .= $line;
				}
				$template = preg_replace("/\[\[ $key \]\].*\[\[\/ $key \]\]/", $text, $template);
			}
		}

		# Hide parts of HTML
		foreach($this->restrict as $restrict)
		{
			$template = str_replace("<!-- $restrict -->", "\r\n<!-- $restrict -->", $template);
			$template = preg_replace("/<!-- $restrict -->.*\<!-- \/$restrict -->/", "", $template);
		}

		# Remove the unused vars
		$template = preg_replace("/\[\[ [a-z0-9_]* \]\].*\[\[\/ [a-z0-9_]* \]\]/", "", $template);
		$template = preg_replace("/\{\{ [a-z0-9_]* \}\}/", "", $template);

		# Restore the newline character
		$template = str_replace("\1", "\r\n", $template);

		# Return rendered as string or print to output
		if($return)
		{
			return $template;
		}
		else
		{
			echo $template;
		}
	}

	# Add styles and JS scripts
	public function add($type, $extension, $files)
	{
		foreach($files as $file)
		{
			$filename = "public/themes/" . Session::get("theme_url") . "/" . $file;
			if(!file_exists($filename))
			{
				$filename = "public/" . $file;
			}
			if(file_exists($filename))
			{
				$time = filemtime($filename);
				$this->data[$type][] = Array("$extension" => $filename . "?t=" . $time);
			}
		}
	}
}
?>
