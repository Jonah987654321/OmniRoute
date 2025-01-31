<?php

namespace OmniRoute\Exceptions\DotenvExceptions;
use Exception;

class InvalidEnvVar extends Exception {
    public function __construct($name) {
        parent::__construct("Env var $name is not set", 0, null);
    }
}

?>