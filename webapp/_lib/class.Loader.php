<?php
/*
 * Copyright 2014 Empodex PHP Framework.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @copyright 2014-2015 Empoddy Labs.
 * @author Prabhat Shankar <prabhat.singh88[at]gmail.com>
 */
class Loader {

    /**
     * Lookup paths for classes and interfaces
     * @var array
     */
    private static $lookup_path;

    /**
     * Classes whose filename doesn't follow the convention
     * @var array
     */
    private static $special_classes = array();

    /**
     * Register
     *
     * Registers the autoloader to enable lazy loading
     *
     * @param array $paths Array of additional lookup path strings
     * @return bool
     */
    public static function register(Array $paths=null) {
        self::setLookupPath($paths);
        return spl_autoload_register(array(__CLASS__, "load"));
    }

    /**
     * Unregister
     *
     * Unregisters the autoloader script, disabling lazy loading
     *
     * @return bool
     */
    public static function unregister() {
        self::$lookup_path = null;
        self::$special_classes = null;
        return spl_autoload_unregister(array(__CLASS__, "load"));
    }

    /**
     * Set Lookup Path
     *
     * Establishes lookup paths, including additional paths if provided
     *
     * @param array $paths Array of additional lookup path strings
     */
    private static function setLookupPath(Array $paths = null) {
        self::definePathConstants();

        // set default lookup paths
        self::$lookup_path = array(
        EFC_WEBAPP_PATH . "config/",
        EFC_WEBAPP_PATH . "_lib/",
        EFC_WEBAPP_PATH . "_lib/model/",
        EFC_WEBAPP_PATH . "_lib/dao/",
        EFC_WEBAPP_PATH . "_lib/extlib/facebook/src/Facebook",
        EFC_WEBAPP_PATH . "_lib/extlib/EFC/",
        EFC_WEBAPP_PATH . "_lib/extlib/Upload1/",
        EFC_WEBAPP_PATH . "_lib/controller/",
        EFC_WEBAPP_PATH . "_lib/exceptions/"
        );

        // set default lookup path for special classes
        //self::$special_classes ["Smarty"] = EFC_WEBAPP_PATH . "_lib/extlib/smarty-3.1.27/libs/Smarty.class.php";
        self::$special_classes ["Smarty"] = EFC_WEBAPP_PATH . "_lib/extlib/Smarty-2.6.28_bk/libs/Smarty.class.php";

        if (isset($paths)) {
            foreach($paths as $path) {
                self::$lookup_path[] = $path;
            }
        }
    }


    /**
     * Define application path constants EFC_ROOT_PATH and EFC_WEBAPP_PATH
     */
    public static function definePathConstants() {
        if ( !defined('EFC_ROOT_PATH') ) {
            if (strpos(__FILE__, 'webapp/_lib' ) !== false) { // root is up 3 directories
                define('EFC_ROOT_PATH', str_replace("\\",'/', dirname(dirname(dirname(__FILE__)))) .'/');
            } else { // root is up 2 directories
                define('EFC_ROOT_PATH', str_replace("\\",'/', dirname(dirname(__FILE__))) .'/');
            }
        }
        if (!defined('EFC_WEBAPP_PATH') ) {
            if (file_exists(EFC_ROOT_PATH . 'webapp')) {
                define('EFC_WEBAPP_PATH', EFC_ROOT_PATH . 'webapp/');
            } else {
                define('EFC_WEBAPP_PATH', EFC_ROOT_PATH);
            }
        }
    }

    /**
     * Add Path
     *
     * Adds another path to crawl for class files
     *
     * @param string $path
     */
    public static function addPath($path) {
        if (!isset(self::$lookup_path)) {
            self::register();
        }
        self::$lookup_path[] = $path;
    }

    /**
     * Get Lookup Path
     *
     * Gets the array of lookup paths
     *
     * @return array
     */
    public static function getLookupPath() {
        return self::$lookup_path;
    }

    /**
     * Get Special Classes
     *
     * Gets the array of special class paths
     *
     * @return array
     */
    public static function getSpecialClasses() {
        return self::$special_classes;
    }

    /**
     * Add Special Classe
     *
     * Add special class information for loading
     *
     * @param str $class_name
     * @param str $path
     */
    public static function addSpecialClass($class_name, $path) {
        self::definePathConstants();
        self::$special_classes[$class_name] = EFC_WEBAPP_PATH.$path;
        require_once(EFC_WEBAPP_PATH.$path);
    }

    /**
     * Load
     *
     * The method registered to run on _autoload. When a class is instantiated, this
     * method will be called to look up the class file if the class is not present.
     * The second instantiation of the same class wouldn't call this method.
     *
     * @param string $class
     * @param bool
     */
    public static function load($class) {
        // check if class is already in scope
        if (class_exists($class, false)) {
            return;
        }

        // if class is a standard EFC object or interface
        foreach (self::$lookup_path as $path) {
            $filename = $path . "config." . $class . ".php";
            if (file_exists($filename)) {
                require_once($filename);
                return;
            }

            $filename = $path . "class." . $class . ".php";
            if (file_exists($filename)) {
                require_once($filename);
                return;
            }

            $filename = $path . "interface." . $class . ".php";
            if (file_exists($filename)) {
                require_once($filename);
                return;
            }

            $filename = $path . $class . ".php";
            if (file_exists($filename)) {
                require_once($filename);
                return;
            }
        }
        // if class is a special class
        if (array_key_exists($class, self::$special_classes)) {
            require_once(self::$special_classes[$class]);
            return;
        }
    }
}
