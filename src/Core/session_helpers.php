<?php
function session_start_if_needed(){
    if (session_status() === PHP_SESSION_NONE) {
        @session_start();
    }
}

function session_get($key, $default = null){
    session_start_if_needed();
    return $_SESSION[$key] ?? $default;
}

function session_set($key, $value){
    session_start_if_needed();
    $_SESSION[$key] = $value;
}

function session_has($key){
    session_start_if_needed();
    return array_key_exists($key, $_SESSION);
}

function session_remove($key){
    session_start_if_needed();
    if (isset($_SESSION[$key])) unset($_SESSION[$key]);
}

function session_all(){
    session_start_if_needed();
    return $_SESSION;
}

function session_clear(){
    session_start_if_needed();
    $_SESSION = [];
}

function session_restart_preserve(array $keys = []){
    session_start_if_needed();
    $preserve = [];
    foreach ($keys as $k) {
        if (array_key_exists($k, $_SESSION)) $preserve[$k] = $_SESSION[$k];
    }
    session_unset();
    session_destroy();
    @session_start();
    foreach ($preserve as $k => $v) {
        $_SESSION[$k] = $v;
    }
}

function session_regenerate(){
    session_start_if_needed();
    // regenerate and delete old session
    $ok = false;
    try {
        $ok = session_regenerate_id(true);
    } catch (Throwable $e) {
        error_log('session_regenerate: exception ' . $e->getMessage());
    }
    error_log('session_regenerate: returned ' . ($ok ? 'true' : 'false'));
    return $ok;
}

?>
