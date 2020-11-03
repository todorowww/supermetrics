<?php

class Autoloader
{
    /**
     * Registers autoloader function to use
     *
     * @throws Exception
     */
    public static function register()
    {
        spl_autoload_register(static function ($class) {
            $file = str_replace("\\", DIRECTORY_SEPARATOR, $class) . ".php";
            $fileLocation = "class/$file";
            if (file_exists($fileLocation)) {
                require($fileLocation);
                return true;
            }
            throw new Exception("Couldn't find $fileLocation. $class not loaded.");
        });
    }
}

Autoloader::register();
