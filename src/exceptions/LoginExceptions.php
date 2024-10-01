<?php

namespace OmniRoute\Exceptions\LoginExceptions;
use Exception;

class UserAlreadyLoggedIn extends Exception {
    public function __construct() {
        parent::__construct("There is already a user logged in. Please logout before", 0, null);
    }
}

class NoUserLoggedIn extends Exception {
    public function __construct() {
        parent::__construct("Logout not possible: There is no user logged in.", 0, null);
    }
}

?>