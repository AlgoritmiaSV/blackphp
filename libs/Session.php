<?php
/**
 * Manejo de la sesión
 * 
 * Esta clase proporciona funcionalidad sobre en manejo de la sesión.
 */
class Session
{
    /**
     * @var array $data Almacenamiento temporal de datos para consumir en peticiones con uso de sesión o API
     */
    private static array $data;

    /**
     * Inicio de sesión
     * 
     * @return void
     */
    public static function init()
    {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
            self::$data = $_SESSION;
        }
    }

    /**
     * Insertar valor
     * 
     * Inserta un valor de cualquier tipo dentro de una clave de la variable $_SESSION
     * 
     * @param string $key La clave de acceso al valor
     * @param string $value El valor a insertar
     * 
     * @return void
     */
    public static function set($key, $value)
    {
        $data = [];
        if (http::isApiRequest()) {
            $data = &self::$data;
        } else {
            $data = &$_SESSION;
        }
        if (strpos($key, '/') === false) {
            $data[$key] = $value;
            return;
        }

        $indexes = explode('/', $key);
        $current = &$data;

        foreach ($indexes as $index) {
            if (!isset($current[$index]) || !is_array($current[$index])) {
                $current[$index] = [];
            }

            $current = &$current[$index];
        }

        $current = $value;
    }

    public static function unset($key)
    {
        unset($_SESSION[$key]);
    }

    /**
     * Obtener valor
     * 
     * Devuelve un valor almacenado en la sesión, o null si no existe.
     * 
     * @param string $key La clave del valor solicitado, que puede estar expresada de manera simple o en multinivel separado
     * por pleca. Por ejemplo:
     * Session::get("user") equivale a $_SESSION["user"]
     * Session::get("user/user_id") equivale a $_SESSION["user"]["user_id"]
     * 
     * @return mixed|null El valor solicitado, o null, si no existe.
     */
    public static function get(string $key)
    {
        $data = http::isApiRequest() ? self::$data : $_SESSION;
        if (strpos($key, "/") !== false) {
            $indexes = explode("/", $key);
            $result = $data;
            foreach ($indexes as $index) {
                if (!isset($result[$index])) {
                    return null;
                }
                $result = $result[$index];
            }
            return $result;
        }

        return $data[$key] ?? null;
    }

    /**
     * Cerrar la sesión
     */
    public static function destroy()
    {
        @session_destroy();
    }
}
