<?php

namespace OmniRoute\utils;

class Post {
    public static function has(string $key): bool {
        return isset($_POST[$key]);
    }

    public static function isEmpty(string $key): bool {
        return !self::has($key) || empty($_POST[$key]);
    }

    public static function get(string $key, string $default = null): ?string {
        return self::has($key)?$_POST[$key]:$default;
    }

    public static function require(...$keys): bool {
        foreach ($keys as $k) {
            if (!self::has($k) || self::isEmpty($k)) {
                return false;
            }
        }
        return true;
    }

    public static function sanitize(string $key, string $type): ?string {
        if (!self::has($key)) {
            return null;
        }
        
        $value = $_POST[$key];
    
        switch ($type) {
            case POST_EMAIL:
                return filter_var($value, FILTER_SANITIZE_EMAIL);
            case POST_URL:
                return filter_var($value, FILTER_SANITIZE_URL);
            case POST_INT:
                return filter_var($value, FILTER_SANITIZE_NUMBER_INT);
            case POST_FLOAT:
                return filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            default:
                return $value;
        }
    }

    public static function validate(string $key, string $type): ?string {
        if (!self::has($key)) {
            return false;
        }
    
        $value = $_POST[$key];
    
        switch ($type) {
            case POST_EMAIL:
                return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
            case POST_URL:
                return filter_var($value, FILTER_VALIDATE_URL) !== false;
            case POST_INT:
                return filter_var($value, FILTER_VALIDATE_INT) !== false;
            case POST_FLOAT:
                return filter_var($value, FILTER_VALIDATE_FLOAT) !== false;
            default:
                return true;
        }
    }    
    
}

?>