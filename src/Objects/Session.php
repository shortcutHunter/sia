<?php

namespace App\Objects;

class Session
{
  public function __construct()
  {
    if (session_status() === PHP_SESSION_NONE) {
     session_start();
    }
    // session_start();
    // session_cache_limiter(false);
  }

  public function set($name, $value) {
    $_SESSION[$name] = $value;
  }

  public function get($name) {

    if (isset($_SESSION[$name])) {
      return $_SESSION[$name];
    }
    return false;
  }

  public function kill($name) {
    unset($_SESSION[$name]);
  }

  public function killAll() {
    session_destroy();
  }
}