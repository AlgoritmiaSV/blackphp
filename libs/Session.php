<?php

class Session
{
	# Start the session
	public static function init()
	{
		@session_start();
	}
	
	# Set a mixed var in the session
	public static function set($key, $value)
	{
		$_SESSION[$key] = $value;
	}
	
	# Get a var from the session
	public static function get($key)
	{
		if (isset($_SESSION[$key]))
		{
			return $_SESSION[$key];
		}
		return null;
	}

	# Close the session
	public static function destroy()
	{
		@session_destroy();
	}
}
?>
