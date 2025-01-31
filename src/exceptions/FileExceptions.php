<?php

namespace OmniRoute\Exceptions\FileExceptions;
use Exception;

class FileNotExisting extends Exception {
    public function __construct($path) {
        parent::__construct("File in location $path does not exist", 0, null);
    }
}

class FileNotReadable extends Exception {
    public function __construct($path) {
        parent::__construct("File in location $path is not readable", 0, null);
    }
}

?>
