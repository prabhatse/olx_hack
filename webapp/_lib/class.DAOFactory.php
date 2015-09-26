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
 * Data Access Object Factory
 *
 * Inits a DAO based on the ThinkUp config db_type and $dao_mapping definitions.
 * db_type is defined in webapp/config.inc.php as:
 *
 *     $EFC_CFG['db_type'] = 'somedb';
 *
 * Example of use:
 *
 * <code>
 *  DAOFactory::getDAO('SomeDAO');
 * </code>
 *
 *
 * @copyright 2014-2015 Empoddy Labs.
 * @author Prabhat Shankar <prabhat.singh88[at]gmail.com>
 */

class DAOFactory {

    /**
     * Maps DAO from db_type and defines interface names and class implementation
     */
    static $dao_mapping = array (
    //Test DAO
        'TestDAO' => array(
    //MySQL Version
            'mysql' => 'TestMySQLDAO',
    //faux Version
            'faux' => 'TestFauxDAO' ),
    //User DAO
        'UserDAO' => array(
    //MySQL Version
            'mysql' => 'UserMySQLDAO' ),
    //UserError DAO
        'UserErrorDAO' => array(
    //MySQL Version
            'mysql' => 'UserErrorMySQLDAO' ),
    //Option MySQL DAO
        'OptionDAO' => array (
    //MySQL Version
            'mysql' => 'OptionMySQLDAO'),

    //Mutex MySQL DAO
        'MutexDAO' => array (
    //MySQL Version
            'mysql' => 'MutexMySQLDAO'),
    // Session MySQL DAO
        'SessionDAO' => array(
    //MySQL Version
            'mysql' => 'SessionMySQLDAO' ),
        'CookieDAO' => array(
    //MySQL Version
          'mysql' => 'CookieMySQLDAO' ),
    // Notify MySQL DAO 
        'NotifyDAO' => array (
    // MySQL Version
          'mysql' => 'NotifyMySQLDAO'),
    //Company MySQL DAO    
        'CompanyDAO' => array(
    //MySQL Version
            'mysql' => 'CompanyMySQLDAO' ),
    //Process MySQL DAO    
        'ProcessDAO' => array(
    //MySQL Version
            'mysql' => 'ProcessMySQLDAO' ),
    //Severity MySQL DAO    
        'SeverityDAO' => array(
    //MySQL Version
            'mysql' => 'SeverityMySQLDAO' ),
    //Client MySQL DAO    
        'ClientDAO' => array(
    //MySQL Version
            'mysql' => 'ClientMySQLDAO' ),
    //Client MySQL DAO    
        'LocationDAO' => array(
    //MySQL Version
            'mysql' => 'LocationMySQLDAO' ),
    //Package MySQL DAO    
        'PackageDAO' => array(
    //MySQL Version
            'mysql' => 'PackageMySQLDAO' ),
    // Country MySQL DAO    
        'CountryDAO' => array(
    //MySQL Version
            'mysql' => 'CountryMySQLDAO' ),
        'UserLogonDAO' => array(
    //MySQL Version
            'mysql' => 'UserLogonMySQLDAO'),
        'UserWorkingHourDAO' => array(
    //MySQL Version
            'mysql' => 'UserWorkingHourMySQLDAO')
    );

    /*
     * Creates a DAO instance and returns it
     *
     * @param string $dao_key the name of the dao you wish to init
     * @param array $cfg_vals Optionally override config.inc.php vals; needs 'table_prefix', 'db_type',
     * 'db_socket', 'db_name', 'db_host', 'db_user', 'db_password'
     * @returns PDODAO A concrete dao instance
     */
    public static function getDAO($dao_key, $cfg_vals=null) {

        //$db_type = self::getDBType($cfg_vals);

        $db_type = 'mysql';
        if (!isset(self::$dao_mapping[$dao_key]) ) {
            throw new Exception("No DAO mapping defined for: " . $dao_key);
        }
        if (!isset(self::$dao_mapping[$dao_key][$db_type])) {
            throw new Exception("No db mapping defined for '" . $dao_key . "' with db type: " . $db_type);
        }
        $class_name = self::$dao_mapping[$dao_key][$db_type];
        

        $dao = new $class_name($cfg_vals);

        return $dao;
    }

    /**
     * Gets the db_type for our configured ThinkUp instance, defaults to mysql,
     * db_type can optionally be defined in webapp/config.inc as:
     *
     *<code>
     *     $EFC_CFG['db_type'] = 'somedb';
     *</code>
     *
     * @param array $cfg_vals Optionally override config.inc.php vals; needs 'table_prefix', 'db_type',
     * 'db_socket', 'db_name', 'db_host', 'db_user', 'db_password'
     * @return string db_type, will default to 'mysql' if not defined
     */
    public static function getDBType($cfg_vals=null) {
        if ($cfg_vals != null) {
            Config::destroyInstance();
        }
        $type = Config::getInstance($cfg_vals)->getValue('db_type');
        $type = is_null($type) ? 'mysql' : $type;
        return $type;
    }
}
