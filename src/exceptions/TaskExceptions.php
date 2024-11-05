<?php

namespace OmniRoute\Exceptions\TaskExceptions;
use Exception;

class TaskAlreadyRegistered extends Exception {
    public function __construct(string $name) {
        parent::__construct("Task $name was already created", 0, null);
    }
}

class TaskNotRegistered extends Exception {
    public function __construct(string $name) {
        parent::__construct("Task $name is not registered", 0, null);
    }
}

?>