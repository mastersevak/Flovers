<?php

spl_autoload_register(function ($className) {
    $baseDir = realpath(dirname(__FILE__) . '/../lib');
    $classFile = $baseDir . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $className) . '.php';
    
    if (is_file($classFile)) {
        require_once $classFile;
    }
});