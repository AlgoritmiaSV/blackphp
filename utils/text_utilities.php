<?php
/**
 * Funciones utilitarias para textos.
 * 
 * Este fichero contiene una clase con funciones utilitarias para el manejo de textos, tales
 * como la gestión de entidades HTML, sustitución de caracteres especiales e conversión de 
 * números de documento.
 * 
 * Incorporado el 2016-04-13 17:12
 * @author Edwin Fajardo <contacto@edwinfajardo.com>
 */

/**
 * Utilidades para la gestión de texto
 * 
 * Conjunto de funciones utilitarias para la conversión y sustitución de textos.
 */
	class text_utilities
	{
		private static $url_out = Array("+", "%E1", "%E9", "%ED", "%F3", "%FA", "%BF", "%3F", "%2C", "%F1", "%28", "%29", "%2F", "%C3");
		private static $php_in = Array(" ", "á", "é", "í", "ó", "ú", "¿", "?", ",", "ñ", "(", ")", "/");
		private static $php_out = Array(" ", "&aacute;", "&eacute;", "&iacute;", "&oacute;", "&uacute;", "&iquest", "?", ",", "&ntilde;", "(", ")", "/");
		private static $url_in = Array("_", "a", "e", "i", "o", "u", "", "", "_", "n", "_", "", "_");
		
		public static function list_sql_html($list)
		{
			foreach($list as $key => $value)
			{
					$list[$key] = self::sql_to_html($value);
			}
			return $list;
		}

		public static function array_sql_html($matrix)
		{
			foreach($matrix as $key => $list)
			{
				$matrix[$key] = self::list_sql_html($list);
			}
			return $matrix;
		}

		public static function list_to_utf8($list, $space = false)
		{
			foreach($list as $key => $value)
			{
					$list[$key] = utf8_encode($value);
					if($space)
					{
						$list[$key] = str_replace("\n", "<br>", $list[$key]);
					}
			}
			return $list;
		}

		public static function array_to_utf8($matrix, $space = false)
		{
			foreach($matrix as $key => $list)
			{
				$matrix[$key] = self::list_to_utf8($list, $space);
			}
			return $matrix;
		}
		
		public static function windows_to_utf8($array)
		{
			foreach($array as $key => $item)
			{
				if(is_array($item))
				{
					$array[$key] = self::windows_to_utf8($item);
				}
				else
				{
					$array[$key] = iconv("windows-1251", "UTF-8", $item);
				}
			}
			return $array;
		}
		
		public static function url_entities($string)
		{
			$text = strtolower($string);
			$text = str_replace(self::$php_in, self::$url_in, $text);
			$text = urlencode($text);
			$text = str_replace(self::$url_out, self::$url_in, $text);
			return $text;
		}
		public static function substring($text, $length)
		{
			if(strlen($text) > $length)
			{
				$text = substr($text, 0, $length + 1);
				$last_space = strrpos($text, " ");
				$text = substr($text, 0, $last_space);
				$text .= '...';
			}
			return $text;
		}
		
		public static function sql_to_html($text)
		{
			$sql = Array("\n", "á", "é", "í", "ó", "ú", "Á", "É", "Í", "Ó", "Ú", "ñ", "Ñ", "\"");
			$html = Array("<br>", "&aacute;", "&eacute;", "&iacute;", "&oacute;", "&uacute;", "&Aacute;", "&Eacute;", "&Iacute;", "&Oacute;", "&Uacute;", "&ntilde;", "&Ntilde;", "&quote;");
			$sql_e = Array("<", ">");
			$html_e = Array("&lt;", "&gt;");
			return str_replace($sql, $html, $text);
		}

		/**
		 * From SICOIM old
		 */

		public static function number_to_text($number)
		{
			$number = str_replace(",", "", (string)$number);
			$f = new NumberFormatter("es-ES", NumberFormatter::SPELLOUT);
			return $f->format($number);
		}

		public static function spell_document($number)
		{
			$str = "";
			$prev_number = false;
			$names = Array(
				"0" => "cero",
				"1" => "uno",
				"2" => "dos",
				"3" => "tres",
				"4" => "cuatro",
				"5" => "cinco",
				"6" => "seis",
				"7" => "siete",
				"8" => "ocho",
				"9" => "nueve",
				"-" => "-",
			);
			$digits = str_split($number);
			foreach($digits as $digit)
			{
				if($prev_number && is_numeric($digit))
				{
					$str .= " ";
				}
				$str .= $names[$digit];
				$prev_number = is_numeric($digit);
			}
			return $str;
		}

		public static function spell_date($date)
		{
			$months = Array(1 => "enero", "febrero", "marzo", "abril", "mayo", "junio", "julio", "agosto", "septiembre", "octubre", "noviembre", "diciembre");
			$parts = explode("-", $date);
			return self::number_to_text($parts[2]) . ' de ' . $months[(int)$parts[1]] . ' de ' . self::number_to_text($parts[0]);
		}

		public static function large_date($date)
		{
			$months = Array(1 => "enero", "febrero", "marzo", "abril", "mayo", "junio", "julio", "agosto", "septiembre", "octubre", "noviembre", "diciembre");
			$parts = explode("-", $date);
			return self::number_to_text($parts[2]) . ' d&iacute;as del mes de ' . $months[(int)$parts[1]] . ' del año ' . self::number_to_text($parts[0]);
		}

		public static function encrypt($text)
		{
			$key = "FEncrypt";
			$simple_string = $text;
			$ciphering = "AES-128-CTR";
			$iv_length = openssl_cipher_iv_length($ciphering);
			$options = 0;
			$encryption_iv = '1234567891011121';
			$encryption_key = $key;
			$encryption = openssl_encrypt($simple_string, $ciphering,
						$encryption_key, $options, $encryption_iv);
			$encryption = base64_encode($encryption);
			$encryption = str_replace("=", "", $encryption);
			return $encryption;
		}

		public static function decrypt($encryption)
		{
			while (strlen($encryption) % 4 != 0) {
				$encryption .= "=";
			}
			$ciphering = "AES-128-CTR";
			$iv_length = openssl_cipher_iv_length($ciphering);
			$options = 0;
			$key = "FEncrypt";
			$decryption_iv = '1234567891011121';
			$decryption_key = $key;
			$encryption = base64_decode($encryption);
			$decryption=openssl_decrypt ($encryption, $ciphering,
					$decryption_key, $options, $decryption_iv);
			return $decryption;
		}

		public static function format(&$number, $format = "")
		{
			$formats = Array(
				"dui" => Array(
					"size" => 9,
					"format" => "8-1"
				),
				"nit" => Array(
					"size" => 14,
					"format" => "4-6-3-1"
				),
				"nrc" => Array(
					"size" => 7,
					"format" => "6-1"
				)
			);
			if(empty($format))
			{
				$length = strlen($number);
				foreach($formats as $key => $f)
				{
					if($length == $f["size"])
					{
						$format = $key;
						break;
					}
				}
			}
			if(!isset($formats[$format]))
			{
				return "Format not exists";
			}
			if(strlen($number) != $formats[$format]["size"])
			{
				if($format == "nrc")
				{
					$formats[$format]["format"] = (strlen($number) - 1) . "-1";
				}
				else
				{
					return $number;
				}
			}
			$digits = str_split($formats[$format]["format"]);
			$offset = 0;
			$str = "";
			foreach ($digits as $digit)
			{
				if(is_numeric($digit))
				{
					$str .= substr($number, $offset, $digit);
					$offset += $digit;
				}
				else
				{
					$str .= $digit;
				}
			}
			$number = $str;
			return $number;
		}

		public static function unformat(&$number)
		{
			$number = str_replace("-", "", $number);
			return $number;
		}
	}
?>