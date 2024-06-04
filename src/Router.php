<?php

namespace OmniRoute;

use OmniRoute\Exceptions\RouterExceptions;
require_once __DIR__."/exceptions/RouterExceptions.php";

use OmniRoute\utils\RenderEngine;
require_once __DIR__."/utils/RenderEngine.php";

class Router {
    private static array $routes = array();
    private static string $notFoundAction, $invalidMethodAction;
    private static string $siteDir;

    public static function add(string $path, string $page, string $method = "GET") {
        if(self::validatePagePath($page)) {
            $path = (str_ends_with($path, "/"))?$path:$path."/";
            self::$routes[$path] = ["page"=>$page, "method"=>$method];
        }
    }

    public static function setNotFound(string $page) {
        if(self::validatePagePath($page)) {
            self::$notFoundAction = $page;
        }
    }

    public static function setInvalidMethod(string $page) {
        if(self::validatePagePath($page)) {
            self::$invalidMethodAction = $page;
        }
    }

    public static function run() {

        $parsed_url = parse_url($_SERVER['REQUEST_URI']);
        $path = (str_ends_with($parsed_url["path"], "/"))?$parsed_url["path"]:$parsed_url["path"]."/";
        
        if (isset(self::$routes[$path])) {
            $toLoad = self::$routes[$path];
            if ($_SERVER["REQUEST_METHOD"] == $toLoad["method"]) {
                self::loadPage($toLoad["page"]);
            } else {
                http_response_code(405);
                if (isset(self::$invalidMethodAction)) {
                    self::loadPage(self::$invalidMethodAction);
                } else {
                    self::loadPrerendered("405");
                }
            }
        } else {
            http_response_code(404);
            if (isset(self::$notFoundAction)) {
                self::loadPage(self::$notFoundAction);
            } else {
                self::loadPrerendered("404");
            }
        }
    }

    private static function loadPage(string $page) {
        require_once self::$siteDir."/public/".$page;
        $re = new RenderEngine(Page::getInstance());
        $re->render();
    }

    private static function loadPrerendered(string $type) {
        require_once __DIR__."/prerendered/$type.php";
    }

    private static function validatePagePath(string $page) {
        if (!isset(self::$siteDir)) {
            $bt = debug_backtrace();
            self::$siteDir = dirname(end($bt)["file"]);
        }
        if(file_exists(self::$siteDir."/public/$page")) {
            return true;
        } else {
            throw new RouterExceptions\InvalidPagePath($page, self::$siteDir."/public/");
        }
    }
}

?>