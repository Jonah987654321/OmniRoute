<?php

namespace TemplaPHP\Exceptions\RouterExceptions;
use Exception;

class InvalidPagePath extends Exception {
    public function __construct($page, $fullPath) {
        parent::__construct("Page $page does not exist in dir $fullPath", 0, null);
    }
}

?>