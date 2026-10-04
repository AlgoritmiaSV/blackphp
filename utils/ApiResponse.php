<?php

class ApiResponse
{
    public static function success(
        string $code = 'SUCCESS',
        string $title = '',
        string $message = '',
        array $data = [],
        array $actions = []
    ): never {
        self::send([
            'success' => true,
            'code' => $code,
            'title' => $title,
            'message' => $message,
            'data' => $data,
            'errors' => [],
            'actions' => self::normalizeActions($actions),
            'meta' => self::meta()
        ], 200);
    }

    public static function warning(
        string $code,
        string $title,
        string $message = '',
        array $data = [],
        array $actions = []
    ): never {
        self::send([
            'success' => true,
            'severity' => 'warning',
            'code' => $code,
            'title' => $title,
            'message' => $message,
            'data' => $data,
            'errors' => [],
            'actions' => self::normalizeActions($actions),
            'meta' => self::meta()
        ], 200);
    }

    public static function error(
        string $code,
        string $title,
        string $message = '',
        array $errors = [],
        int $httpCode = 400
    ): never {
        self::send([
            'success' => false,
            'code' => $code,
            'title' => $title,
            'message' => $message,
            'data' => [],
            'errors' => $errors,
            'actions' => self::normalizeActions(),
            'meta' => self::meta()
        ], $httpCode);
    }

    private static function send(array $response, int $httpCode): never
    {
        http_response_code($httpCode);

        header('Content-Type: application/json; charset=utf-8');

        echo json_encode(
            $response,
            JSON_UNESCAPED_UNICODE |
            JSON_UNESCAPED_SLASHES |
            JSON_PRETTY_PRINT
        );

        exit;
    }

    private static function normalizeActions(array $actions = []): array
    {
        return array_merge([
            'reload' => false,
            'print' => null,
            'redirect' => null,
            'reset' => true
        ], $actions);
    }

    private static function meta(): array
    {
        return [
            'timestamp' => gmdate('c'),
            'request_id' => bin2hex(random_bytes(8))
        ];
    }
}