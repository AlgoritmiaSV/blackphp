<?php

foreach (glob("controllers/apiv1/*") as $file) {
    include $file;
}

/**
 * API Version 1
 * 
 * Acceso por API
 * 
 * Incorporado el 2026-10-03 12:03
 * @author Edwin Fajardo <contacto@edwinfajardo.com>
 * @link https://www.edwinfajardo.com
 */

class Apiv1 extends Controller
{
    use ApiV1User;

    /**
     * Constructor de la clase
     * 
     * Inicializa la propiedad module con el nombre de la clase
     */
    public function __construct()
    {
        parent::__construct();
        $this->module = get_class($this);
        $this->view->data["module"] = $this->module;
    }

    public function index()
    {
        ApiResponse::error(
            code: "ENDPOINT_NOT_FOUND",
            title: _("Error"),
            message: _("Bad request")
        );
    }

    /**
     * Ejecutar
     * 
     * Método genérico para llamar a un método dentro de la aplicación
     * 
     * @param string $module El trait a usar
     * @param string $method El método/función a ejecutar
     * @param array|null $data Parámetros
     * @return void
     */
    private function execute(string $module, string $method, ?array $data = [])
    {
        $controller = new $module();
        if (!method_exists($controller, $method)) {
            return;
        }

        call_user_func_array(array($controller, $method), $data);
    }
}
