<?php

class Log {

  public static function trace($level = "debug", $msg) {
    $tstamp = date('Y-m-d H:i:s');
    error_log("$tstamp :: $level :: $msg");
  }

  public static function __callStatic($fn, $args) {
    call_user_func_array(array('Log', 'trace'), array($fn, $args[0]));
  }
}

?>