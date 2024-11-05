<?php

namespace OmniRoute\Extensions;
use OmniRoute\Exceptions\TaskExceptions\TaskAlreadyRegistered;
use OmniRoute\Exceptions\TaskExceptions\TaskNotRegistered;

require_once __DIR__."/../exceptions/TaskExceptions.php";
require_once __DIR__."/../utils/functions.php";


class Tasks {
    private static array $tasks;

    public static function create(string $name, callable $callback) {
        if (isset(self::$tasks[$name])) {
            throw new TaskAlreadyRegistered($name);
        }

        self::$tasks[$name] = $callback;
    }

    public static function runTask($name) {
        if (!isset(self::$tasks[$name])) {
            throw new TaskNotRegistered($name);
        }

        return ["function" => ["OmniRoute\\Extensions\\Tasks", "_taskExecution"], "params" => [$name]];
    }

    public static function _taskExecution($route, string $name) {
        call_user_func(self::$tasks[$name]);
    }
}

?>