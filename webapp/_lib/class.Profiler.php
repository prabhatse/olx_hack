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
class Profiler {
    /**
     *
     * @var Profiler
     */
    private static $instance;
    /**
     *
     * @var array
     */
    private $logged_actions = array();
    /**
     * @var int
     */
    public $total_queries = 0;
    /**
     * Name of class and function about to call Profiler
     * @var str
     */
    public static $dao_method = "";

    /**
     * Name of class and function about to call Profiler
     * @var str
     */
    public static $class_method = "";
    /**
     * Get singleton instance
     * @return Profiler
     */
    public static function getInstance() {
        if (!isset(self::$instance)) {
            self::$instance = new Profiler();
        }
        return self::$instance;
    }
    /**
     * Add action
     * @param float $time
     * @param str $action
     */
    public function add($time, $action, $is_query=false, $num_rows=0 ) {
        if ($is_query) {
            $this->total_queries = $this->total_queries + 1;
        }
        $rounded_time = round($time, 3);
        $this->logged_actions[] =  array('time'=>number_format($rounded_time,3), 'action'=> trim($action),
        'num_rows'=>$num_rows, 'is_query'=>$is_query, 'dao_method'=>self::$dao_method);
        self::$dao_method = ''; //now that it's logged, set the dao_method to empty string
    }
    /**
     * Set DAO method member variable to display in log.
     * @param $dao_method
     */
    public static function setDAOMethod($dao_method) {
        self::$dao_method = $dao_method;
    }

    /**
     * Set class method member variable to display in log.
     * @param $dao_method
     */
    public static function setClassMethod($class_method) {
        self::$class_method = $class_method;
    }

    /**
     * Get sorted profiled actions
     * @return array
     */
    public function getProfile() {
        sort($this->logged_actions);
        return array_reverse($this->logged_actions);
    }

    /**
     * Check if Profiler is enabled; that is, if enabled in config file and running a web page.
     * @return bool Whether the profiler is enabled
     */
    public static function isEnabled() {
        if (isset($_SERVER['HTTP_HOST'])) {
            $config = Config::getInstance();
            return $config->getValue('enable_profiler');
        } else {
            return false;
        }
    }

    /**
     * Clear out all logged items, reset query count to 0
     */
    public function clearLog() {
        $keys = array_keys($this->logged_actions);
        foreach ($keys as $key) {
            unset($this->logged_actions[$key]);
        }
        $this->total_queries = 0;
    }

    public static function debugPoint($what,$func,$file,$line,$param=null) {
        if ($what) {

                var_dump($param);
                print_r($param);
            echo "<br/>exit in function :".$func." in file :".$file." at line :".$line."\n";
           // echo "<br/> exit in function :".$func." in file :".$file." at line :".$line."\n";
            exit;
        }
    } 
}
