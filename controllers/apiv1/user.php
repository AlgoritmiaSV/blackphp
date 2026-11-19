<?php
trait Apiv1User
{
    public function login()
    {
        switch ($_SERVER["REQUEST_METHOD"]) {
            case "POST":
                $this->execute("User", "TestLogin");
                break;
            default:
                ApiResponse::error(
                    code: "METHOD_NOT_ALLOWED",
                    title: _("Error"),
                    message: _("Bad request")
                );
                break;
        }
    }

    public function user()
    {
        switch ($_SERVER["REQUEST_METHOD"]) {
            case "GET":
                $this->execute("Settings", "user_table_loader");
                break;
            default:
                ApiResponse::error(
                    code: "METHOD_NOT_ALLOWED",
                    title: _("Error"),
                    message: _("Bad request")
                );
                break;
        }
    }
}
