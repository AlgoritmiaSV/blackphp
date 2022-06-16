<?php
/**
 * Funciones utilitarias para las fechas
 * 
 * Creation date-time: 2016-03-24 17:43
 * 
 * @version	1.0.0
 * @author	Edwin Fajardo <contacto@edwinfajardo.com>
 * @copyright 2016-2021 Edwin Fajardo. All rights reserved
 */

class date_utilities
{
	public static $months = Array("enero", "febrero", "marzo", "abril", "mayo", "junio", "julio", "agosto", "septiembre", "octubre", "noviembre", "diciembre");
	
	public static function sql_date_to_string($sql_date, $hour = false, $dayname = false)
	{
		$time = strtotime($sql_date);
		$string = self::date_to_string($time, $dayname);
		if($hour)
		{
			$string .= " a las " . self::hour($sql_date);
		}
		return $string;
	}
	
	public static function date_to_string($time, $dayname = false)
	{
		$time_data = Array(Date("Y", $time), Date("n", $time) - 1, Date("d", $time), Date("w", $time));
		$days = Array("Domingo", "Lunes", "Martes", "Mi&eacute;rcoles", "Jueves", "Viernes", "S&aacute;bado");
		$date = "";
		if($dayname)
		{
			$date = $days[$time_data[3]] . ', ';
		}
		$date .= $time_data[2] . ' de ' . self::$months[$time_data[1]] . ' de ' . $time_data[0];
		return $date;
	}
	
	public static function interval_to_string($begin, $end)
	{
		$time_begin = strtotime($begin);
		$time_end = strtotime($end);
		$begin_data = Array(Date("Y", $time_begin), Date("n", $time_begin) - 1, Date("d", $time_begin));
		$end_data = Array(Date("Y", $time_end), Date("n", $time_end) - 1, Date("d", $time_end));
		$interval_str = "";
		if($begin_data[0] == $end_data[0])
		{
			if($begin_data[1] == $end_data[1])
			{
				$interval_str = 'del ' . $begin_data[2] . ' al ' . $end_data[2] . ' de ' . self::$months[$end_data[1]] . ' de ' . $end_data[0];
			}
			else
			{
				$interval_str = 'del ' . $begin_data[2] . ' de ' . self::$months[$begin_data[1]] . ' al ' . $end_data[2] . ' de ' . self::$months[$end_data[1]] . ' de ' . $end_data[0];
			}
		}
		else
		{
			$interval_str = 'del ' . $begin_data[2] . ' de ' . self::$months[$begin_data[1]] . ' de ' . $begin_data[0] . ' al ' . $end_data[2] . ' de ' . self::$months[$end_data[1]] . ' de ' . $end_data[0];
		}
		return $interval_str;
	}
	
	public static function hour($date_str)
	{
		$time = strtotime($date_str);
		return Date("h:i a", $time);
	}
	
	public static function day_of_month($date)
	{
		$time = strtotime($date);
		$time_data = Array(Date("n", $time) - 1, Date("d", $time));
		$day_of_month = $time_data[1] . ' de ' . self::$months[$time_data[0]];
		return $day_of_month;
	}

	/**
	 * Calculate time
	 */
	public static function sql_date_to_ago($time_string)
	{
		$time = new DateTime($time_string);
		$ago = $time->diff(new DateTime());
		$string = "";
		if($ago->y > 0)
		{
			$string = $ago->y . " año";
			if($ago->y != 1)
			{
				$string .= "s";
			}
		}
		if($ago->m > 0 || $string != "")
		{
			if($string != "")
			{
				$string .= ", ";
			}
			$string .= $ago->m . " mes";
			if($ago->m != 1)
			{
				$string .= "es";
			}
		}
		if($ago->d > 0 || $string != "")
		{
			if($string != "")
			{
				$string .= ", ";
			}
			$string .= $ago->d . " día";
			if($ago->d != 1)
			{
				$string .= "s";
			}
		}
		if($ago->h > 0 || $string != "")
		{
			if($string != "")
			{
				$string .= ", ";
			}
			$string .= $ago->h . " hora";
			if($ago->h != 1)
			{
				$string .= "s";
			}
		}
		if($ago->i > 0 || $string != "")
		{
			if($string != "")
			{
				$string .= ", ";
			}
			$string .= $ago->i . " minuto";
			if($ago->i != 1)
			{
				$string .= "s";
			}
		}
		if($string != "")
		{
			$string .= " y ";
		}
		$string .= $ago->s . " segundo";
		if($ago->s != 1)
		{
			$string .= "s";
		}
		return $string;
	}

	public static function sql_date_to_age($time_string)
	{
		$time = new DateTime($time_string);
		$ago = $time->diff(new DateTime());
		$string = "";
		if($ago->y > 0)
		{
			$string = $ago->y . " año";
			if($ago->y != 1)
			{
				$string .= "s";
			}
		}
		if($ago->y < 5)
		{
			if($ago->m > 0 || $string != "")
			{
				if($string != "")
				{
					$string .= ", ";
				}
				$string .= $ago->m . " mes";
				if($ago->m != 1)
				{
					$string .= "es";
				}
			}
			if($ago->y == 0 && $ago->m < 12)
			{
				if($ago->d > 0 || $string != "")
				{
					if($string != "")
					{
						$string .= ", ";
					}
					$string .= $ago->d . " día";
					if($ago->d != 1)
					{
						$string .= "s";
					}
				}
			}
		}
		return $string;
	}
}
?>