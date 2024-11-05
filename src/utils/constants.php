<?php

/* == ERROR CODE CONSTANTS == */
define("OMNI_404", 404);
define("OMNI_405", 405);


/* == OmniLogin CONSTANTS == */
define("EXT_LOGIN", ["name" => "OmniLogin", "requiredSetup" => ["loginRoute"]]);
define("LOGIN_REQUIRED", ["function" => ["OmniRoute\\Extensions\\OmniLogin", "loginRequired"], "params" => []]);

/* == Tasks CONSTANTS == */
define("EXT_TASKS", ["name" => "Tasks", "requiredSetup" => []])

?>