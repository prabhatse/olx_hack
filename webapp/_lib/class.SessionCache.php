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
class SessionCache {
    /**
     * Start the session system running. If use_db_sessions is set to true in the config file, store session data
     * in the datbase.
     * @return void
     */
    public static function init() {
        $config = Config::getInstance();
        if ($config->getValue('use_db_sessions')) {
            $session_dao = DAOFactory::getDAO('SessionDAO');
            session_set_save_handler(
                array($session_dao, 'open'),
                array($session_dao, 'close'),
                array($session_dao, 'read'),
                array($session_dao, 'write'),
                array($session_dao, 'destroy'),
                array($session_dao, 'gc')
            );
            // the following prevents unexpected effects when using objects as save handlers
            register_shutdown_function('session_write_close');
        }
        session_name('EFC');
        session_start();
    }

    /**
     * Put a value in Stern's $_SESSION key.
     * @param str $key
     * @param str $value
     */
    public static function put($key, $value) {
        $config = Config::getInstance();
        $_SESSION[$config->getValue('source_root_path')][$key] = $value;
    }

    /**
     * Get a value from Stern's $_SESSION.
     * @param str $key
     * @return mixed Value
     */
    public static function get($key) {
        $config = Config::getInstance();
        if (self::isKeySet($key)) {
            return $_SESSION[$config->getValue('source_root_path')][$key];
        } else {
            return null;
        }
    }

    /**
     * Check if a key in Stern's $_SESSION has a value set.
     * @param str $key
     * @return bool
     */
    public static function isKeySet($key) {
        $config = Config::getInstance();
        return isset($_SESSION[$config->getValue('source_root_path')][$key]);
    }

    /**
     * Unset key's value in Stern's $_SESSION
     * @param str $key
     */
    public static function unsetKey($key) {
        $config = Config::getInstance();
        unset($_SESSION[$config->getValue('source_root_path')][$key]);
    }

    public static function unsetPermission() {

        $config = Config::getInstance();

        $check = $_SESSION[$config->getValue('source_root_path')];        
        foreach ($check as $key => $value) {
            self::unsetKey($key);
        }
    }
}
