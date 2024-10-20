<?php

namespace OmniRoute;

require __DIR__."/utils/functions.php";
require __DIR__."/utils/constants.php";

require_once __DIR__."/exceptions/ExtensionExceptions.php";
use OmniRoute\Exceptions\ExtensionExceptions\InvalidExtension;
use OmniRoute\Exceptions\ExtensionExceptions\MissingSetupData;

require_once __DIR__."/exceptions/RouterExceptions.php";
use OmniRoute\Exceptions\RouterExceptions\PathAlreadyRegistered;
use OmniRoute\Exceptions\RouterExceptions\StatusCodeNotSupported;

session_start();

class Router {
    private static array $routes = array();
    private static array $errorCallbacks = array();
    private static string $prefix = "/";

    public static function loadExtension(array $extension, array $setup = null) {
        if (!isset($extension["name"]) || !isset($extension["requiredSetup"])) {
            throw new InvalidExtension();
        }

        foreach ($extension["requiredSetup"] as $setupKey) {
            if (!isset($setup[$setupKey])) {
                throw new MissingSetupData($extension["name"], $setupKey);
            }
        }

        switch($extension["name"]) {
            case "OmniLogin":
                require __DIR__."/extensions/Login.php";
                \OmniRoute\Extensions\OmniLogin::setLoginRoute($setup["loginRoute"]);
                break;
            default:
                break;
        }
    }

    public static function registerPrefix(string $prefix) {
        self::__setPrefix(self::$prefix . ltrim(rtrim($prefix, '/'), '/') . '/');
    }

    public static function add(string $path, callable $callback, array $method = ["GET"], array $ext = []) {
        // Combine prefix with path
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
            foreach(self::$routes[$fullPath] as $r) {
                foreach($method as $m) {
                    if (in_array($m, $r["method"])) {
                        throw new PathAlreadyRegistered($fullPath);
                    }
                }
            }

            self::$routes[$fullPath][] = ["callback" => $callback, "method" => $method, "ext" => $ext];
        } else {
            self::$routes[$fullPath] = array(["callback" => $callback, "method" => $method, "ext" => $ext]);
        }
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

        $route = null;

        if (isset(self::$routes[$path])) {
            foreach (self::$routes[$path] as $r) {
                if (in_array($_SERVER["REQUEST_METHOD"], $r["method"])) {
                    $route = $r;
                    $route["args"] = [];
                    break;
                }
            }
            
            $route = ($route==null)?OMNI_405:$route;
        } else {

            //Check for RegEx Arguments
            foreach (array_keys(self::$routes) as $r) {
                $regEx = "'".str_replace("/", "\/", $r)."?'";
                if (preg_match($regEx, $path)) {
                    foreach (self::$routes[$r] as $mR) {
                        if (in_array($_SERVER["REQUEST_METHOD"], $mR["method"])) {
                            $arguments = [];

                            $urlPath = self::__splitPath($path);
                            $storedPath = self::__splitPath($r);

                            for ($i=0; $i<sizeof($urlPath); $i++) {
                                if ($storedPath[$i] == "\w+") {
                                    $arguments[] = $urlPath[$i];
                                }
                            }
                        
                            $route = self::$routes[$r];
                            $route["args"] = $arguments;
                            break;
                       }
                    }
                    $route = ($route==null)?OMNI_405:$route;
                    break;
                }
            }
        }

        if (!$route) {
            self::throwFrontendError(OMNI_404, array($path));
        } else if ($route == OMNI_405) {
            self::throwFrontendError(OMNI_405, array($path, $_SERVER["REQUEST_METHOD"]));
        } else {
            foreach($route["ext"] as $ext) {
                call_user_func_array($ext["function"], array_merge([$path], $ext["params"]));
            }

            call_user_func_array($route["callback"], $route["args"]);
        }
    }

    public static function throwFrontendError($code, $args) {
        $supported = [OMNI_404, OMNI_405];
        if (!in_array($code, $supported)) {
            throw new StatusCodeNotSupported($code);
        }

        http_response_code($code);
        if (isset(self::$errorCallbacks[$code])) {
            call_user_func_array(self::$errorCallbacks[$code], $args);
        } else {
            self::__loadPrerendered(strval($code));
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
