<?php

namespace TemplaPHP;

class Page {
    private static string $title;

    public static function setTitle(string $title) {
        self::$title = $title;
    }

    public static function getTitle(): string {
        return self::$title;
    }

    public static function getInstance() {
        $instance = new Page();
        $instance->setTitle(self::$title);
        return $instance;
    }
}
?>