<?php

function redirect($path) {
    if (!preg_match('/^(\/|http)/', $path)) {
        $path = '/' . ltrim($path, '/');
    }
    header("Location: " . $path);
    exit();
}

?>