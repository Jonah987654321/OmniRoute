<?php

namespace OmniRoute;

use OmniRoute\Exceptions\RouterExceptions;
require_once __DIR__."/exceptions/RouterExceptions.php";

require_once __DIR__."/utils/constants.php";

class Router {
    private static array $routes = array();
    private static array $errorCallbacks = array();

    public static function add(string $path, callable $callback, array $method = array("GET")) {
        $path = (str_ends_with($path, "/"))?$path:$path."/";
        self::$routes[$path] = ["callback"=>$callback, "method"=>$method];
    }

    public static function registerErrorCallback(string $errorCode, callable $callback) {
        self::$errorCallbacks[$errorCode] = $callback;
    }

    public static function run() {

        $parsed_url = parse_url($_SERVER['REQUEST_URI']);
        $path = (str_ends_with($parsed_url["path"], "/"))?$parsed_url["path"]:$parsed_url["path"]."/";
        
        if (isset(self::$routes[$path])) {
            $toLoad = self::$routes[$path];
            if (in_array($_SERVER["REQUEST_METHOD"], $toLoad["method"])) {
                $toLoad["callback"]();
            } else {
                http_response_code(405);
                if (isset(self::$errorCallbacks[OMNI_405])) {
                    call_user_func_array(self::$errorCallbacks[OMNI_405], array($path, $_SERVER["REQUEST_METHOD"]));
                } else {
                    self::loadPrerendered("405");
                }
            }
        } else {
            http_response_code(404);
            if (isset(self::$errorCallbacks[OMNI_404])) {
                call_user_func_array(self::$errorCallbacks[OMNI_404], array($path));
            } else {
                self::loadPrerendered("404");
            }
        }
    }

    private static function loadPrerendered(string $type) {
        require_once __DIR__."/prerendered/$type.php";
    }
}

?>