<?php
/**
 * Conversor de nombres
 * 
 * Incorporado el: 2026-04-10 15:00
 * 
 * Generado por Copilot
 */
class CaseConverter {
    /**
     * Detect the case style of a string
     * Returns: "snake", "camel", "pascal", "kebab", "constant", or "unknown"
     */
    public static function detectCase(string $string): string {
        if (preg_match('/^[a-z]+(_[a-z0-9]+)*$/', $string)) {
            return "snake";
        } elseif (preg_match('/^[a-z]+([A-Z][a-z0-9]*)*$/', $string)) {
            return "camel";
        } elseif (preg_match('/^[A-Z][a-z0-9]*([A-Z][a-z0-9]*)*$/', $string)) {
            return "pascal";
        } elseif (preg_match('/^[a-z0-9]+(-[a-z0-9]+)*$/', $string)) {
            return "kebab";
        } elseif (preg_match('/^[A-Z]+(_[A-Z0-9]+)*$/', $string)) {
            return "constant";
        }
        return "unknown";
    }

    /**
     * Universal conversion method
     * $toCase must be one of: "snake", "camel", "pascal", "kebab", "constant"
     */
    public static function convert(string $string, string $toCase): string {
        $fromCase = self::detectCase($string);

        // Normalize to words array
        $words = [];
        switch ($fromCase) {
            case "snake":
                $words = explode('_', strtolower($string));
                break;
            case "kebab":
                $words = explode('-', strtolower($string));
                break;
            case "camel":
            case "pascal":
                $words = preg_split('/(?=[A-Z])/', $string);
                $words = array_map('strtolower', $words);
                break;
            case "constant":
                $words = explode('_', strtolower($string));
                break;
            default:
                return $string; // unknown case, return as-is
        }

        // Convert to target case
        switch ($toCase) {
            case "snake":
                return implode('_', $words);
            case "kebab":
                return implode('-', $words);
            case "camel":
                return lcfirst(implode('', array_map('ucfirst', $words)));
            case "pascal":
                return implode('', array_map('ucfirst', $words));
            case "constant":
                return strtoupper(implode('_', $words));
            default:
                return $string;
        }
    }
}

/*
// Example usage:
echo CaseConverter::detectCase("HELLO_WORLD_EXAMPLE") . "\n"; // constant
echo CaseConverter::convert("HELLO_WORLD_EXAMPLE", "camel") . "\n";   // helloWorldExample
echo CaseConverter::convert("helloWorldExample", "constant") . "\n";  // HELLO_WORLD_EXAMPLE
echo CaseConverter::convert("HelloWorldExample", "constant") . "\n";  // HELLO_WORLD_EXAMPLE
echo CaseConverter::convert("hello-world-example", "constant") . "\n"; // HELLO_WORLD_EXAMPLE
*/
