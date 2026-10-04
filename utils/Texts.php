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
class Texts
{

	public static function numberToString($number)
	{
		$number = str_replace(",", "", (string) $number);
		$f = new NumberFormatter("es-ES", NumberFormatter::SPELLOUT);
		return $f->format($number);
	}

	public static function spellDocument($number)
	{
		$str = "";
		$prev_number = false;
		$names = array(
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
		foreach ($digits as $digit) {
			if ($prev_number && is_numeric($digit)) {
				$str .= " ";
			}
			$str .= $names[$digit];
			$prev_number = is_numeric($digit);
		}
		return $str;
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
		$encryption = openssl_encrypt(
			$simple_string,
			$ciphering,
			$encryption_key,
			$options,
			$encryption_iv
		);
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
		$decryption = openssl_decrypt(
			$encryption,
			$ciphering,
			$decryption_key,
			$options,
			$decryption_iv
		);
		return $decryption;
	}

	public static function format(&$number, $format = "")
	{
		if (empty($number)) {
			return $number;
		}
		$formats = array(
			"dui" => array(
				"size" => 9,
				"format" => "8-1"
			),
			"nit" => array(
				"size" => 14,
				"format" => "4-6-3-1"
			),
			"nrc" => array(
				"size" => 7,
				"format" => "6-1"
			)
		);
		if (empty($format)) {
			$length = strlen($number);
			foreach ($formats as $key => $f) {
				if ($length == $f["size"]) {
					$format = $key;
					break;
				}
			}
		}
		if (!isset($formats[$format])) {
			return "Format not exists";
		}
		if (strlen($number) != $formats[$format]["size"]) {
			if ($format == "nrc" && strlen($number) > 1) {
				$formats[$format]["format"] = (strlen($number) - 1) . "-1";
			} else {
				return $number;
			}
		}
		$digits = str_split($formats[$format]["format"]);
		$offset = 0;
		$str = "";
		foreach ($digits as $digit) {
			if (is_numeric($digit)) {
				$str .= substr($number, $offset, $digit);
				$offset += $digit;
			} else {
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