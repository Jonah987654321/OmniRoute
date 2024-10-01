<?php

namespace OmniRoute\Exceptions\ExtensionExceptions;
use Exception;

class InvalidExtension extends Exception {
    public function __construct() {
        parent::__construct("Invalid extension, unable to load", 0, null);
    }
}

class MissingSetupData extends Exception {
    public function __construct($extension, $key) {
        parent::__construct("Setup Array is missing required key $key for setup of extension $extension", 0, null);
    }
}

?>