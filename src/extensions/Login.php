<?php

namespace OmniRoute\Extensions;

use OmniRoute\Exceptions\LoginExceptions\NoUserLoggedIn;
use OmniRoute\Exceptions\LoginExceptions\UserAlreadyLoggedIn;
use OmniRoute\Exceptions\LoginExceptions\UserCheckAlreadyRegistered;
use OmniRoute\Exceptions\LoginExceptions\UserCheckNotRegistered;

require_once __DIR__."/../exceptions/LoginExceptions.php";
require_once __DIR__."/../utils/functions.php";

class OmniLogin {

    private static string $loginRoute;
    private static array $loginChecks;

    public static function setLoginRoute(string $route) {
        self::$loginRoute = $route;
    }

    public static function loginUser($user) {
        if (isset($_SESSION["OmniRoute"]["ext"]["OmniLogin"]["user"])) {
            throw new UserAlreadyLoggedIn();
        }

        $_SESSION["OmniRoute"]["ext"]["OmniLogin"]["user"] = $user;
    }

    public static function loginUserAndRedirect($user) {
        self::loginUser($user);

        return isset($_SESSION["OmniRoute"]["ext"]["OmniLogin"]["afterLogin"])?redirect($_SESSION["OmniRoute"]["ext"]["OmniLogin"]["afterLogin"]):redirect("/");
    }

    public static function logoutUser() {
        if (!isset($_SESSION["OmniRoute"]["ext"]["OmniLogin"]["user"])) {
            throw new NoUserLoggedIn();
        }

        unset($_SESSION["OmniRoute"]["ext"]["OmniLogin"]["user"]);
    }

    public static function getUser() {
        return (isset($_SESSION["OmniRoute"]["ext"]["OmniLogin"]["user"])?$_SESSION["OmniRoute"]["ext"]["OmniLogin"]["user"]:null);
    }

    public static function updateUser($user) {
        $_SESSION["OmniRoute"]["ext"]["OmniLogin"]["user"] = $user;
    }

    public static function isUserLoggedIn() {
        return isset($_SESSION["OmniRoute"]["ext"]["OmniLogin"]["user"]); 
    }

    public static function loginRequired($route) {
        if (!self::isUserLoggedIn()) {
            $_SESSION["OmniRoute"]["ext"]["OmniLogin"]["afterLogin"] = $route;
            return redirect(self::$loginRoute."?next=".urlencode($route));
        }
    }

    public static function registerUserCheck(string $name,  callable $check) {
        if (isset(self::$loginChecks[$name])) {
            throw new UserCheckAlreadyRegistered($name);
        }

        self::$loginChecks[$name] = $check;
    }

    public static function runUserCheck(string $name) {
        if (!isset(self::$loginChecks[$name])) {
            throw new UserCheckNotRegistered($name);
        }

        return ["function" => ["OmniRoute\\Extensions\\OmniLogin", "userCheckExecute"], "params" => [$name]];
    }

    public static function userCheckExecute($route, string $name) {
        call_user_func_array(self::$loginChecks[$name], [self::getUser()]);
    }
}

?>
