<?php

namespace OmniRoute\utils;

require_once __DIR__."/../exceptions/FileExceptions.php";
use OmniRoute\Exceptions\FileExceptions\FileNotExisting;
use OmniRoute\Exceptions\FileExceptions\FileNotReadable;

require_once __DIR__."/../exceptions/DotenvExceptions.php";
use OmniRoute\Exceptions\DotenvExceptions\InvalidEnvVar;

class Dotenv {
    private static string $path;
    private static array $envVars = [];
    
    public static function loadFile(string $filePath) {
        if (!is_file($filePath)) {
            throw new FileNotExisting($filePath);
        }

        if (!is_readable($filePath)) {
            throw new FileNotReadable($filePath);
        }
        
        self::$path = $filePath;
        self::__loadData();
    }

    private static function __loadData() {
        $lines = file(self::$path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {

            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);
            if ($value == 1) {
                $value = true;
            } else if ($value == 0) {
                $value = false;
            }

            self::$envVars[$name] = $value;
        }
    }

    public static function get($name) {
        if (!isset(self::$envVars[$name])) {
            throw new InvalidEnvVar($name);
        }

        return self::$envVars[$name];
    }
}

?>