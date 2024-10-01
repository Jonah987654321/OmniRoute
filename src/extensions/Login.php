<?php

namespace OmniRoute\Extensions;

use OmniRoute\Exceptions\LoginExceptions\NoUserLoggedIn;
use OmniRoute\Exceptions\LoginExceptions\UserAlreadyLoggedIn;
require_once __DIR__."/../exceptions/LoginExceptions.php";
require_once __DIR__."/../utils/functions.php";

class OmniLogin {

    private static string $loginRoute;
     
    public static function setLoginRoute(string $route) {
        self::$loginRoute = $route;
    }

    public static function loginUser($user) {
        if (isset($_SESSION["OMNILOGIN_USER"])) {
            throw new UserAlreadyLoggedIn();
        }

        $_SESSION["OMNILOGIN_USER"] = $user;
    }

    public static function logoutUser() {
        if (!isset($_SESSION["OMNILOGIN_USER"])) {
            throw new NoUserLoggedIn();
        }

        unset($_SESSION["OMNILOGIN_USER"]);
    }

    public static function getUser() {
        return (isset($_SESSION["OMNILOGIN_USER"])?$_SESSION["OMNILOGIN_USER"]:null);
    }

    public static function isUserLoggedIn() {
        return isset($_SESSION["OMNILOGIN_USER"]); 
    }

    public static function loginRequired($route) {
        if (!self::isUserLoggedIn()) {
            return redirect(self::$loginRoute."?next=".urlencode($route));
        }
    }
}

?>
