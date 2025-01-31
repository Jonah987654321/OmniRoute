<?php

namespace OmniRoute\Exceptions\RouterExceptions;
use Exception;

class PathAlreadyRegistered extends Exception {
    public function __construct($path) {
        parent::__construct("Route for path $path already registered", 0, null);
    }
}

class StatusCodeNotSupported extends Exception {
    public function __construct($code) {
        parent::__construct("HTTP response code $code seems to be not valid as http response code or not yet supported", 0, null);
    }
}

class FileNotExisting extends Exception {
    public function __construct($path) {
        parent::__construct("File in location $path does not exist", 0, null);
    }
}
?>