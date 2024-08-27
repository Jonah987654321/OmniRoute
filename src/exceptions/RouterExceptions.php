<?php

namespace OmniRoute\Exceptions\RouterExceptions;
use Exception;

class PathAlreadyRegistered extends Exception {
    public function __construct($path) {
        parent::__construct("Route for path $path already registered", 0, null);
    }
}

?>