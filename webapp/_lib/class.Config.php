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
 *
 * Configuration singleton
 *
 * Singleton acess object for EFC configuration values set in config.inc.php.
 * Never reference $EFC_CFG directly; always do it through this object.
 *
 * Example of use:
 *
 * <code>
 * // get the Config singleton
 * $config = Config::getInstance();
 * // get a value from it
 * $config->getValue('log_location');
 * </code>
 *
 * @copyright 2014-2015 Empoddy Labs.
 * @author Prabhat Shankar <prabhat.singh88[at]gmail.com>
 */
class Config {
    /**
     *
     * @var Config
     */
    private static $instance;
    /**
     *
     * @var array
     */
    var $config = array();

    /**
     *
     * @var array some reasonable defaults if null in config
     */
    protected static $defaults = array(
        'app_title_prefix' => '',
        'upload_dir'=>'upload/'
        );

    protected static $mimetype = array(
        'application/json',
        'text/json',
        'application/excel',
        'text/plain',
        'text/html',
        'text/css',
        'image/jpeg',
        'image/png',
        'application/x-httpd-php'
        );

    /**
     * Private Constructor
     * @param array $vals Optional values to override file config
     * @return Config
     */
    private function __construct($vals = null) {
        if ($vals != null ) {
            $this->config = $vals;
        } else {
            Loader::definePathConstants();

            if (file_exists(EFC_WEBAPP_PATH . 'config.inc.php')) {
                require EFC_WEBAPP_PATH . 'config.inc.php';
                $this->config = $EFC_CFG;
                //set version info...
                /*
                require EFC_WEBAPP_PATH . 'install/version.php';
                $this->config['EFC_VERSION']  = $EFC_VERSION;
                $this->config['EFC_VERSION_REQUIRED'] =
                array('php' => $EFC_VERSION_REQUIRED['php'], 'mysql' => $EFC_VERSION_REQUIRED['mysql']);
                */
            } else {
                throw new ConfigurationException("EFC's configuration file does not exist! ".
                "Try installing EFC.");
            }
        }
        foreach (array_keys(self::$defaults) as $default) {
            if (!isset($this->config[$default])) {
                $this->config[$default] = self::$defaults[$default];
            }
        }
    }
    /**
     * Get the singleton instance of Config
     * @param array $vals Optional values to override file config
     * @return Config
     */
    public static function getInstance($vals = null) {
        if (!isset(self::$instance)) {
            self::$instance = new Config($vals);
        }
        return self::$instance;
    }
    /**
     * Get the configuration value
     * @param    string   $key   key of the configuration key/value pair
     * @return   mixed    value of the configuration key/value pair
     */
    public function getValue($key) {
        // is this config value stored in the db?
        $db_value_config = AppConfig::getConfigValue($key);
        $value = null;

        $value = isset($this->config[$key]) ? $this->config[$key] : null;
/*
        Profiler::debugPoint(false,__METHOD__, __FILE__, __LINE__);

        var_dump($db_value_config);

        if ($db_value_config) {
            $option_dao = DAOFactory::getDAO("OptionDAO");
            $db_value = $option_dao->getOptionValue(OptionDAO::APP_OPTIONS, $key, false );
            $value =  $db_value ? $db_value : $db_value_config['default'];
            // convert db text booleans if needed
            if ($value == 'false') {
                $value = false;
            } else if ($value == 'true') {
                $value = true;
            }
        } else {
            // if not a db config value, get from config file
            $value = isset($this->config[$key]) ? $this->config[$key] : null;
        }
*/
        return $value;
    }
    /**
     * Provided only for use when overriding config.inc.php values in tests
     * @param string $key
     * @param string $value
     * @return string $value
     */
    public function setValue($key, $value) {
        $value = $this->config[$key] = $value;
        return $value;
    }
    /**
     * Provided only for tests that want to kill Config object in tearDown()
     */
    public static function destroyInstance() {
        if (isset(self::$instance)) {
            self::$instance = null;
        }
    }
    /**
     * Provided for tests which expect an array
     */
    public function getValuesArray() {
        return $this->config;
    }
    /**
     * Returns the GMT offset in hours based on the application's defined timezone.
     *
     * If $time is given, gives the offset for that time; otherwise uses the current time.
     *
     * @param int $time The time to base it on, as anything strtotime() takes; leave blank for current time.
     * @return int The GMT offset in hours.
     */
    public function getGMTOffset($time = 0) {
        $time = $time ? $time : 'now';
        $tz = ($this->getValue('timezone')==null)?date('e'):$this->getValue('timezone');
        // this may be currently required for some setups to avoid fatal php timezone complaints when
        // exec'ing off the streaming child processes.
        // date_default_timezone_set($tz);
        return timezone_offset_get( new DateTimeZone($tz), new DateTime($time) ) / 3600;
    }

    public function getMimeTypes() {
        return self::$mimetype;
    }
}
