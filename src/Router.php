<?php

namespace OmniRoute;

use OmniRoute\Exceptions\RouterExceptions\PathAlreadyRegistered;

require_once __DIR__."/exceptions/RouterExceptions.php";
require_once __DIR__."/utils/constants.php";

class Router {
    private static array $routes = array();
    private static array $errorCallbacks = array();
    private static string $prefix = "/";

    public static function registerPrefix(string $prefix) {
        self::__setPrefix(self::$prefix . ltrim(rtrim($prefix, '/'), '/') . '/');
    }

    public static function add(string $path, callable $callback, array $method = array("GET")) {
        // Kombiniere das Präfix mit dem Pfad
        $fullPath = self::$prefix . ltrim($path, '/');
        $fullPath = (str_ends_with($fullPath, "/")) ? $fullPath : $fullPath . "/";

        //If there are arguments in the URL replace them with RegEx
        $newPaths = array();
        foreach (self::__splitPath($fullPath) as $p) {
            if (str_starts_with($p, "<:") && str_ends_with($p, ":>"))  {
                $newPaths[] = "\w+";
            } else {
                $newPaths[] = $p;
            }
        }

        $fullPath = "/".implode("/", $newPaths)."/";

        if (key_exists($fullPath, self::$routes)) {
            throw new PathAlreadyRegistered($fullPath);
        }

        self::$routes[$fullPath] = ["callback" => $callback, "method" => $method];
    }

    public static function registerErrorCallback(string $errorCode, callable $callback) {
        self::$errorCallbacks[$errorCode] = $callback;
    }

    public static function registerSubRouter($filePath) {
        $savePrefix = self::$prefix;

        require_once $filePath;

        //Prefix clearup
        self::$prefix = $savePrefix;
    }

    public static function run() {
        $parsed_url = parse_url($_SERVER['REQUEST_URI']);
        $path = (str_ends_with($parsed_url["path"], "/")) ? $parsed_url["path"] : $parsed_url["path"] . "/";

        if (isset(self::$routes[$path])) {
            $toLoad = self::$routes[$path];
            if (in_array($_SERVER["REQUEST_METHOD"], $toLoad["method"])) {
                $toLoad["callback"]();
            } else {
                http_response_code(405);
                if (isset(self::$errorCallbacks[OMNI_405])) {
                    call_user_func_array(self::$errorCallbacks[OMNI_405], array($path, $_SERVER["REQUEST_METHOD"]));
                } else {
                    self::__loadPrerendered("405");
                }
            }
        } else {

            //Check for RegEx Arguments
            $regexMatch = false;
            foreach (array_keys(self::$routes) as $r) {
                $regEx = "'".str_replace("/", "\/", $r)."?'";
                if (preg_match($regEx, $path)) {
                    $regexMatch = true;
                    $arguments = [];

                    $urlPath = self::__splitPath($path);
                    $storedPath = self::__splitPath($r);

                    for ($i=0; $i<sizeof($urlPath); $i++) {
                        if ($storedPath[$i] == "\w+") {
                            $arguments[] = $urlPath[$i];
                        }
                    }

                    call_user_func_array(self::$routes[$r]["callback"], $arguments);
                    break;
                }
            }

            if (!$regexMatch) {
                http_response_code(404);
                if (isset(self::$errorCallbacks[OMNI_404])) {
                    call_user_func_array(self::$errorCallbacks[OMNI_404], array($path));
                } else {
                    self::__loadPrerendered("404");
                }
            }
        }
    }

    private static function __loadPrerendered(string $type) {
        require_once __DIR__ . "/prerendered/$type.php";
    }

    /* HELPER FUNCTIONS FOR GETTING INSTANCES */

    public static function __setPrefix(string $prefix) {
        self::$prefix = $prefix;
    }

    public static function __setRoutes(array $routes) {
        self::$routes = $routes;
    }

    public static function __setErrorCallbacks(array $errorCallbacks) {
        self::$errorCallbacks = $errorCallbacks;
    }

    public static function getPrefix() {
        return self::$prefix;
    }

    public static function getRoutes() {
        return self::$routes;
    }

    public static function getErrorCallbacks() {
        return self::$errorCallbacks;
    }

    public static function getInstance() {
        $instance = new Router();
        $instance->__setPrefix(self::$prefix);
        $instance->__setRoutes(self::$routes);
        $instance->__setErrorCallbacks(self::$errorCallbacks);
        return $instance;
    }

    /* END HELPER FUNCTIONS FOR GETTING INSTANCES */

    private static function __splitPath($fullPath) {
        $pathSegments = explode("/", $fullPath);
        $pathSegments = array_slice($pathSegments, 1, sizeof($pathSegments) - 2);
        return $pathSegments;
    }
}

?>
