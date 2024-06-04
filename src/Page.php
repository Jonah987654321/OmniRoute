<?php

namespace OmniRoute;

use OmniRoute\Exceptions\PageExceptions;
require_once __DIR__."/exceptions/PageExceptions.php";

use OmniRoute\utils\RenderObjects;
require_once __DIR__."/utils/RenderObjects.php";

class Page {
    private static string $title, $siteDir;
    private static array $stylesheets, $queue;
    private static int $currentQueuePos = 0;

    public static function setTitle(string $title) {
        self::$title = $title;
    }

    public static function getTitle(): string {
        return self::$title;
    }

    public static function loadCSS(string $path) {
        if (self::validatePagePath($path, "assets/css")) {
            self::$stylesheets[] = $path;
        }
    }

    public static function setStylesheets(array $list) {
        self::$stylesheets = $list;
    }

    public static function getStylesheets(): array {
        return self::$stylesheets;
    }

    public static function detectSiteDir() {
        if (!isset(self::$siteDir)) {
            $bt = debug_backtrace();
            self::$siteDir = dirname(end($bt)["file"]);
        }
    }

    public static function getSiteDir() {
        return self::$siteDir;
    }

    public static function addHTML(callable $callback) {
        ob_start();
        $callback();
        $html = html_entity_decode(ob_get_clean());
        ob_end_flush();
        self::$queue[] = new RenderObjects\HTMLRender(self::$currentQueuePos, $html);
        self::$currentQueuePos++;
    }

    public static function setRenderQueue($renderQ) {
        self::$queue = $renderQ;
    }

    public static function getRenderQueue(): array {
        return self::$queue;
    }

    public static function getInstance() {
        $instance = new Page();
        $instance->setTitle(self::$title);
        $instance->setStylesheets(self::$stylesheets);
        $instance->detectSiteDir();
        $instance->setRenderQueue(self::$queue);
        return $instance;
    }

    private static function validatePagePath(string $page, string $type) {
        self::detectSiteDir();
        if(file_exists(self::$siteDir."/$type/$page")) {
            return true;
        } else {
            throw new PageExceptions\InvalidPagePath($page, self::$siteDir."/$type/");
        }
    }
}
?>