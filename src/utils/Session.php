<?php

namespace OmniRoute\utils;

class Session  {
    public static function isSet(string $key) {
        return isset($_SESSION["userContent"][$key]);
    }

    public static function get(string $key, $valueIfNotSet = null) {
        return (self::isSet($key))?$_SESSION["userContent"][$key]:$valueIfNotSet;
    }

    public static function set(string $key, mixed $value) {
        $_SESSION["userContent"][$key] = $value;
    }

    public static function unset(string $key) {
        unset($_SESSION["userContent"][$key]);
    }
}

?>
