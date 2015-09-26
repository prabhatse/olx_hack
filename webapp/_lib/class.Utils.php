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
class Utils {

    /**
     * Indents a flat JSON string to make it more human-readable.
     *
     * @author http://recursive-design.com/blog/2008/03/11/format-json-with-php/
     * @param string $json The original JSON string to process.
     * @return string Indented version of the original JSON string.
     */
    public static function indentJSON($json) {
        $result = '';
        $pos = 0;
        $str_len = strlen($json);
        $indent_str = '    ';
        $new_line = "\n";
        $prev_char = '';
        $prev_prev_char = '';
        $out_of_quotes = true;

        for ($i = 0; $i <= $str_len; $i++) {

            // Grab the next character in the string.
            $char = substr($json, $i, 1);

            // Are we inside a quoted string?
            if ($char == '"') {
                if ( $prev_char != "\\") {
                    $out_of_quotes = !$out_of_quotes;
                } elseif ($prev_prev_char == "\\") {
                    $out_of_quotes = !$out_of_quotes;
                }
                // If this character is the end of an element,
                // output a new line and indent the next line.
            } else if (($char == '}' || $char == ']') && $out_of_quotes) {
                $result .= $new_line;
                $pos--;
                for ($j = 0; $j < $pos; $j++) {
                    $result .= $indent_str;
                }
            }

            // Add the character to the result string.
            $result .= $char;

            // If the last character was the beginning of an element,
            // output a new line and indent the next line.
            if (($char == ',' || $char == '{' || $char == '[') && $out_of_quotes) {
                $result .= $new_line;
                if ($char == '{' || $char == '[') {
                    $pos++;
                }

                for ($j = 0; $j < $pos; $j++) {
                    $result .= $indent_str;
                }
            }

            $prev_prev_char = $prev_char;
            $prev_char = $char;
        }

        return $result;
    }

    /**
     * Becuse PHP doesn't have a data type large enough to hold some of the
     * numbers that Twitter deals with, this function strips the double
     * quotes off every string that contains only numbers inside the double
     * quotes.
     *
     * @param string $encoded_json JSON formatted string.
     * @return string Encoded JSON with numeric strings converted to numbers.
     */
    public static function convertNumericStrings($encoded_json) {
        return preg_replace('/\"((?:-)?[0-9]+(\.[0-9]+)?)\"/', '$1', $encoded_json);
    }

    /**
     * Get percentage
     * @param int $numerator
     * @param int $denominator
     * @return int Percentage
     */
    public static function getPercentage($numerator, $denominator) {
        if ((isset($numerator)) && (isset($denominator))) {
            if ($numerator > 0 && $denominator > 0) {
                return ($numerator * 100) / ($denominator);
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    /**
     * Get the contents of a URL via GET
     * @param str $URL
     * @return str contents
     */
    public static function getURLContents($URL) {
        $c = curl_init();

        curl_setopt($c, CURLOPT_URL, $URL);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_PROXYPORT,3128); 
        curl_setopt($c, CURLOPT_SSL_VERIFYHOST,0); 
        curl_setopt($c, CURLOPT_SSL_VERIFYPEER,0);

        //curl_setopt($c, CURLOPT_HEADER, false);
        //curl_setopt($c, CURLOPT_ENCODING, "");

        $contents = curl_exec($c);
        $status = curl_getinfo($c, CURLINFO_HTTP_CODE);
        curl_close($c);

        //$contents = json_decode($content);

        //echo "URL: ".$URL."\n";
        //echo json_decode($contents);
        //echo "STATUS: ".$status."\n";
        if (isset($contents)) {
            return $contents;
        } else {
            return null;
        }
    }

    /**
     * Get the contents of a URL via POST
     * @param str $URL
     * @param array $fields
     * @return str contents
     */
    public static function getURLContentsViaPost($URL, array $fields) {
        $fields_string = '';
        //url-ify the data for the POST
        foreach($fields as $key=>$value) {
            $fields_string .= $key.'='.$value.'&';
        }
        rtrim($fields_string,'&');

        //open connection
        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch,CURLOPT_URL,$URL);
        curl_setopt($ch,CURLOPT_POST,count($fields));
        curl_setopt($ch,CURLOPT_POSTFIELDS,$fields_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        //execute post
        $contents = curl_exec($ch);

        //close connection
        curl_close($ch);
        if (isset($contents)) {
            return $contents;
        } else {
            return null;
        }
    }

    /**
     * Get plugins that exist in the SternDev plugins directory
     * @param str $dir
     * @return array Plugins
     */
    public static function getPlugins($dir) {
        $dh = @opendir($dir);
        $plugins = array();
        if (!$dh) {
            throw new Exception("Cannot open directory $dir");
        } else {
            while (($file = readdir($dh)) !== false) {
                if ($file != '.' && $file != '..') {
                    $requiredFile = "$dir/$file";
                    if (is_dir($requiredFile)) {
                        array_push($plugins, $file);
                    }
                }
            }
            closedir($dh);
        }

        unset($dh, $dir, $file, $requiredFile);
        return $plugins;
    }

    /**
     * Get plugin view directory
     * @param str $shortname Plugin short name
     * @return str view path
     */
    public static function getPluginViewDirectory($shortname) {
        Loader::definePathConstants();
        $view_path = EFC_WEBAPP_PATH.'plugins/'.$shortname.'/view/';
        return $view_path;
    }

    /**
     * Get URL with params
     * Build URL with params given an array
     * @param str $url
     * @param array $params
     * @return str URL
     */
    public static function getURLWithParams($url, $params){
        $param_str = '';
        foreach ($params as $key=>$value) {
            $param_str .= $key .'=' . $value.'&';
        }
        if ($param_str != '') {
            $url .= '?'.substr($param_str, 0, (strlen($param_str)-1));
        }
        return $url;
    }

    /**
     * Validate email address
     *
     * @param str $email Email address to validate
     * @return bool Whether or not it's a valid address
     */
    public static function validateEmail($email = '') {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Validate URL
     *
     * @param str $url
     * @return bool Whether or not it's a "valid" URL
     */
    public static function validateURL($url) {
        return filter_var($url, FILTER_VALIDATE_URL);
    }

    /**
     * Determine the site_root_path using the file system structure instead of $EFC_CFG['site_root_path'].
     * Only use this function when the config file doesn't yet exist (like in the installer). Otherwise, use
     * $EFC_CFG['site_root_path'].
     * @return str
     */
    public static function getSiteRootPathFromFileSystem() {
        $dirs_under_root = array('account', 'cases', 'session', 'users', 'clients','candidates', 'tests', 'packages');        
        if (isset($_SERVER['PHP_SELF'])) {
            $current_script_path = explode('/', $_SERVER['PHP_SELF']);
        } else {
            $current_script_path = array();
        }
        array_pop($current_script_path);
        if ( in_array( end($current_script_path), $dirs_under_root ) ) {
            array_pop($current_script_path);
        }
        // Account for API calls
        if ( end($current_script_path) == 'v1' ) {
            array_pop($current_script_path);
            if ( end($current_script_path) == 'api' ) {
                array_pop($current_script_path);
            }
        }
        $current_script_path = implode('/', $current_script_path) . '/';
        return $current_script_path;
    }



    public static function getUriGetParams() {
      if (strstr($_SERVER['REQUEST_URI'] ,'?')) {
        $params = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
        parse_str($params, $get);
        return $get;
        //$params = substr($_SERVER['REQUEST_URI'],strpos($_SERVER['REQUEST_URI'],'?')+1);
        //return $params;
      } else {
        return false;
      }
    }


    public static function getCurrentUri()
    {
      $basepath = implode('/', array_slice(explode('/', $_SERVER['SCRIPT_NAME']), 0, -1)) . '/';
      $uri = substr($_SERVER['REQUEST_URI'], strlen($basepath));
      if (strstr($uri, '?')) $uri = substr($uri, 0, strpos($uri, '?'));
      $uri = '/' . trim($uri, '/');
      return $uri;
    }

    /**
     * Wrapper for $_SERVER['REQUEST_URI'] that accounts for site_root_path.
     * Returns the request URI for a ThinkUp file relative to site_root_path.
     * For example, if the request is http://example.com/mythinkup/account/user.php, this will return
     * account/user.php.
     * Use this instead of directly referencing $_SERVER['REQUEST_URI'] to account for web server forwards, symlinks,
     * and other tomfoolery.
     * @return str
     */
    public static function getApplicationRequestURI() {
        $dirs_under_root = array('account', 'cases', 'session', 'users', 'clients','candidates', 'tests', 'packages','search');
        if (isset($_SERVER['REQUEST_URI'])) {
            $current_script_path = explode('/', $_SERVER['REQUEST_URI']);
        } else {
            $current_script_path = array();
        }
        $req_url = array();
        $req_url[] = array_pop($current_script_path);
        if ( in_array( end($current_script_path), $dirs_under_root ) ) {
            $req_url[] = array_pop($current_script_path);
        }
        // Account for API calls
        if ( end($current_script_path) == 'v1' ) {
            $req_url[] = array_pop($current_script_path);
            if ( end($current_script_path) == 'api' ) {
                $req_url[] = array_pop($current_script_path);
            }
        }
        $req_url = implode('/', array_reverse($req_url));
        return $req_url;
    }

    /**
     * Get the application's host name or server name, i.e., example.com.
     * @return str Host name either set by PHP global vars or stored in the database
     */
    public static function getApplicationHostName() {

        //Profiler::debugPoint(true,__METHOD__, __FILE__, __LINE__);

        //First attempt to get the host name without querying the database
        //Try SERVER_NAME

        $server = empty($_SERVER['SERVER_NAME']) ? '' : $_SERVER['SERVER_NAME'];
        //Second, try HTTP_HOST
        if ($server == '' ) {
            $server = empty($_SERVER['HTTP_HOST']) ? '' : $_SERVER['HTTP_HOST'];
        }
        //Finally fall back to stored application setting set by Installer::storeServerName
        $server = '192.168.2.102';
        if ($server == '') {
            
            $option_dao = DAOFactory::getDAO('OptionDAO');
            try {
                $server_app_setting = $option_dao->getOptionByName(OptionDAO::APP_OPTIONS, 'server_name');
                if (isset($server_app_setting)) {
                    $server = $server_app_setting->option_value;
                }
            } catch (PDOException $e) {
                //If retrieving the option doesn't work (ie, the options table doesn't exist), do nothing
            }
        }
        //domain name is always lowercase
        $server = strtolower($server);
        return $server;
    }

    /**
     * Get the application's full URL, i.e., https://example.com/<ur_user_type>/
     * @param $replace_localhost_with_ip Default to false
     * @return str
     */
    public static function getApplicationURL($replace_localhost_with_ip = false) {
        $server = self::getApplicationHostName();
        if ($replace_localhost_with_ip) {
            $server = ($server == 'localhost')?'127.0.0.1':$server;
        }
        $site_root_path = Config::getInstance()->getValue('site_root_path');
        if (!isset($site_root_path)) { //config file not written yet (during install)
            $site_root_path = self::getSiteRootPathFromFileSystem();
        }
        //URLencode everything except spaces in site_root_path
        $site_root_path = preg_replace('/(%2f|%2F)/', '/', urlencode($site_root_path));
        if  (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] != '80') { //non-standard port
            if (isset($_SERVER['HTTPS']) && $_SERVER['SERVER_PORT'] == '443') { //account for standard https port
                $port = '';
            } else {
                $port = ':'.$_SERVER['SERVER_PORT'];
            }
        } else {
            $port = '';
        }
        return 'http'.(empty($_SERVER['HTTPS'])?'':'s').'://'.$server.$port.$site_root_path;
    }

    /**
     * Generate var dump to string.
     * @return str
     */
    public static function varDumpToString($mixed = null) {
        ob_start();
        var_dump($mixed);
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }

    /**
     * FOR TESTING AND DEBUGGING USE ONLY. DO NOT USE IN PRODUCTION APPLICATION CODE.
     * Given a PDO SQL statement with parameters to bind, replaces the :param tokens with the parameters and return
     * a string for display/debugging purposes.
     * @param str $sql
     * @param arr $vars
     * @return str
     */
    public static function mergeSQLVars($sql, $vars) {
        foreach ($vars as $k => $v) {
            $sql = str_replace($k, (is_int($v))?$v:"'".$v."'", $sql);
        }
        $config = Config::getInstance();
        $prefix = $config->getValue('table_prefix');
        $gmt_offset = $config->getGMTOffset();
        $sql = str_replace('#gmt_offset#', $gmt_offset, $sql);
        $sql = str_replace('#prefix#', $prefix, $sql);
        return $sql;
    }

    /**
     * If date.timezone is not set in php.ini, default to America/Los_Angeles to avoid date() warning about
     * using system settings.
     * This method exists to avoid the warning which Smarty triggers in views that don't have access to a
     * EFC_CFG timezone setting yet, like during installation, or when a config file doesn't exist.
     */
    public static function setDefaultTimezonePHPini() {
        if (ini_get('date.timezone') == false) {
            // supress the date_default_timezone_get() warn as php 5.3.* doesn't like when date.timezone is not set in
            // php.ini, but many systems comment it out by default, or have no php.ini by default
            $error_reporting = error_reporting(); // save old reporting setting
            error_reporting( E_ERROR | E_USER_ERROR ); // turn off warning messages
            $tz = date_default_timezone_get(); // get tz if we can
            error_reporting( $error_reporting ); // reset error reporting
            if (!$tz) { // if no $tz defined, use UTC
                $tz = 'UTC';
            }
            ini_set('date.timezone',$tz);
        }
    }

    /**
     * Calculate the number of time units it will take to reach the next count milestone given
     * a trend of upward increments.
     * @param int $current_count
     * @param int $upward_increment
     * @return array 'next_milestone'=> int, 'will_take'=>int
     */
    public static function predictNextMilestoneDate($current_count, $upward_increment) {
        if ($upward_increment > 0 ) {
            $milestones = array(
            1000000,
            750000,
            500000,
            300000,
            250000,
            200000,
            150000,
            100000,
            50000,
            25000,
            10000,
            5000,
            1000,
            500,
            200,
            100
            );

            $goal_count = 0;
            foreach ($milestones as $milestone) {
                if ($current_count < $milestone) {
                    $goal_count = $milestone;
                }
            }
            if ($goal_count == 0) { //group count is over a million
                $float_val = $current_count/10000000;
                $goal_count = round($float_val, 1);
                $goal_count = $goal_count * 10000000;
                if ($current_count > $goal_count) {
                    $goal_count = $goal_count + 500000;
                }
            }
            $prediction = intval(round(($goal_count - $current_count)/$upward_increment));
            return array('next_milestone'=>$goal_count, 'will_take'=>$prediction);
        } else {
            return null;
        }
    }

    public static function getLastSaturday($from_date = null ) {
        $format = 'n/j';
        if (!isset($from_date)) {
            $offset = strtotime(date('Y-m-d'));
        } else {
            $offset = strtotime($from_date);
        }
        if (date('w', $offset) == 6) {
            return date($format,strtotime('-1 week', $offset));
        } else {
            return date($format,strtotime("last Saturday",$offset));
        }
    }
    /**
     * Return whether currently in test mode.
     * @return bool Whether in test mode
     */
    public static function isTest() {
        return (getenv("MODE")=="TESTS" || (isset($_COOKIE['TU_MODE']) && $_COOKIE['TU_MODE']=='TESTS'));
    }

    /**
     * Check if Stern India endpoint is set in the configuration file.
     * @return bool
     */
    public static function isEmpoddyLabs() {
        $cfg = Config::getInstance();
        $empoddy_endpoint = $cfg->getValue('empoddy_endpoint');
        //return ($empoddy_endpoint !== null);
        return true;
    }
    /**
     * Remove URLs from a block of text.
     * @param  str $text Text with URLs in it
     * @return str Text without URLs in it
     */
    public static function stripURLsOutOfText($text) {
        return preg_replace('/\b(https?):\/\/[-A-Z0-9+&@#\/%?=~_|$!:,.;]*[A-Z0-9+&@#\/%=~_|$]/i', '', $text);
    }

    /**
     * Use PorterStemmer library to stem a word.
     * @param str $word The word to stem.
     * @return str The stemmed word
     */
    public static function stemWord($word) {
        require_once(EFC_WEBAPP_PATH.'_lib/extlib/Stemmer/class.PorterStemmer.php');
        return PorterStemmer::Stem($word);
    }

    /**
     * Calculate popularity of post.
     * @param Post $post
     * @return int $popularity_index The popularity index of this post
     */
    public static function getPopularityIndex(Post $post) {
        return
            (5 * $post->reply_count_cache) +
            (3 * $post->retweet_count_cache) +
            (2 * $post->favlike_count_cache);
    }
    
    public static function convert($size) {
       $unit=array('b','kb','mb','gb','tb','pb');
       return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
    }

    public static function processLocation($data) {
        $config = Config::getInstance();
        if (isset($data)) {
            if($data['location_type'] == 0) {
                $adr = $data['name'];
                $url = $config->getValue('gmapJson').$adr."&sensor=false";
                $content = Utils::getURLContents($url);
                $arr = json_decode($content);
                var_dump($arr);
            }
        }
    }

    public function makeLocationUrl() {

    }

}
