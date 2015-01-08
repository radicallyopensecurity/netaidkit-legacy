<?php

class Autoloader
{
    // Restrictive class names, don't allow any special characters.
    static protected $_valid_class = "/^[a-zA-Z_][a-zA-Z0-9_]*$/";

    static public function load($class)
    {
        // Sanitize $class before including file.
        if (!self::valid_classname($class))
            throw new Exception("Invalid class name.");

        // CONTROLLER_DIR and CLASS_DIR are never user controlled.
        $dir = (substr($class, -10) == "Controller") 
               ? CONTROLLER_DIR : CLASS_DIR;
        
        // $dir and $class are sane at this point.
        $filename = "$dir/$class.php";
        if (is_file($filename))
            require_once $filename;
            
        if (!class_exists($class, false))
            throw new Exception("Class not found.");
    }
    
    static public function valid_classname($classname)
    {
        return preg_match(self::$_valid_class, $classname);
    }
}
