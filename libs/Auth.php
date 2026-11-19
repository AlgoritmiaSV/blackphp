<?php
use Fizzday\FizzJWT\FizzJWT;

class Auth
{
    private static string $secretKey = 'vtL0ofj$PsA!6Tm@Ioq@jq{o_bE>tt=T';

    /**
     * Read and validate JWT from Authorization: Bearer <token>
     *
     * @return boolean true si la operación se completa con éxito
     * @throws Exception
     */
    public static function loadJwt(): bool
    {
        $authHeader = $_SERVER['HTTP_AUTHORIZATION']
            ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION']
            ?? '';

        if (empty($authHeader)) {
            return false;
        }

        if (!preg_match('/Bearer\s+(.+)/i', $authHeader, $matches)) {
            return false;
        }

        $token = trim($matches[1]);

        if ($token === '') {
            return false;
        }

        $data = [];
        try {
            $data = FizzJWT::decode($token, self::$secretKey);
        } catch (\Exception $e) {
            return false;
        }

        # Cargando datos en sesión temporal
        $data = self::objectToArray($data);
        foreach ($data as $key => $item) {
            Session::set($key, $item);
        }

        return true;
    }

    public static function createJwt($payload): string
    {
        $jwt = FizzJWT::encode($payload, self::$secretKey);
        return $jwt;
    }

    private static function objectToArray(mixed $data): mixed
    {
        if (is_object($data)) {
            $data = get_object_vars($data);
        }

        if (is_array($data)) {
            return array_map([self::class, 'objectToArray'], $data);
        }

        return $data;
    }
}
