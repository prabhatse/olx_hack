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

abstract class EFCController {
    /**
     * @var ViewManager
     */
    protected $view_mgr;
    /**
     * @var string Smarty template filename
     */
    protected $view_template = null;
    /**
     *
     * @var string cache key separator
     */
    const KEY_SEPARATOR='-';
    /**
     *
     * @var bool
     */
    protected $profiler_enabled = false;
    /**
     *
     * @var float
     */
    private $start_time = 0;
    /**
     *
     * @var araray
     */
    protected $header_scripts = array ();
    /**
     *
     * @var araray
     */
    protected $header_css = array ();
    /**
     *
     * @var array
     */
    protected $json_data = null;
    /**
     * For testing
     * @var str
     */
    public $redirect_destination;
    /*
     * To initiate Redis and for optimization type of things, related to 
     * BaseMem
     */
    public $baseMem;
    public $redis;
    public $memcache;
    public $facebook;
    public $fb_token = null;
    /**
     *
     * @var str
     */
    protected $content_type = 'text/html; charset=UTF-8'; //default
    /**
    *
    * @var boolean if true we will pass a CSRF token to the view
    */
    protected $view_csrf_token = false; //default

    /**
     * Constructs EFCController
     *
     *  Adds email address of currently logged in EFC user, '' if not logged in, to view
     *  {$logged_in_user}
     *  @return EFCController
     */
    public function __construct($session_started=false) {
        try {
            //$this->baseMem = memory_get_usage(true);
            $this->redis = new Redis();
            $this->redis->connect('127.0.0.1',6379);
        
            $this->memcache = new Memcache;
            $this->memcache->connect('127.0.0.1',11211);

            $config = Config::getInstance();
            $this->profiler_enabled = Profiler::isEnabled();
            if ( $this->profiler_enabled) {
                $this->start_time = microtime(true);
            }
            if ($config->getValue('timezone')) {
                date_default_timezone_set($config->getValue('timezone'));
            }
            if (!$session_started) {
                SessionCache::init();
            }
            $this->view_mgr = new ViewManager();

            $this->facebook = new Facebook\Facebook([
              'app_id' => $config->getValue('fb_app_id'),
              'app_secret' => $config->getValue('fb_app_secret'),
              'default_graph_version' => 'v2.2',
              ]);

            if ($this->isLoggedIn()) {
                $this->addToView('logged_in_user', $this->getLoggedInUser());
            }
            if ($this->isSuperAdmin()) {
                $this->addToView('user_is_admin', true);
            }

            $EFC_VERSION = $config->getValue('EFC_VERSION');
            $this->addToView('EFC_VERSION', $EFC_VERSION);

            if (Utils::isEmpoddyLabs()) {
                $empoddy_endpoint = $config->getValue('empoddy_endpoint');
                $this->addToView('empoddy_endpoint', $empoddy_endpoint);
            }

            if (SessionCache::isKeySet('selected_instance_network') &&
            SessionCache::isKeySet('selected_instance_username')) {
                $this->addToView('selected_instance_network', SessionCache::get('selected_instance_network'));
                $this->addToView('selected_instance_username', SessionCache::get('selected_instance_username'));
            }
        } catch (Exception $e) {
            Loader::definePathConstants();
            //echo 'sending this to Smarty:'.EFC_WEBAPP_PATH.'data/';
            $cfg_array =  array(
            'site_root_path'=>Utils::getSiteRootPathFromFileSystem(),
            'source_root_path'=>EFC_ROOT_PATH,
            'datadir_path'=>EFC_WEBAPP_PATH.'data/',
            'debug'=>false,
            'app_title_prefix'=>"",
            'cache_pages'=>false);
            $this->view_mgr = new ViewManager($cfg_array);

            $this->setErrorTemplateState();
            $this->addToView('error_type', get_class($e));
            $disable_xss = false;
            // if we are an installer exception, don't filter XSS, we have markup, and we trust this content
            if (get_class($e) == 'InstallerException') {
                $disable_xss = true;
            }
            $this->addErrorMessage($e->getMessage(), null, $disable_xss);
        }
    }

    /**
     * Handle request parameters for a particular resource and return view markup.
     *
     * @return str Markup which renders controller results.
     */
    abstract public function control();

    /**
     * Returns whether or not ThinkUp user is logged in
     *
     * @return bool whether or not user is logged in
     */
    protected function isLoggedIn() {
        return Session::isLoggedIn();
    }

    /**
     * Returns whether or not a logged-in ThinkUp user is an admin
     *
     * @return bool whether or not logged-in user is an admin
     */
    protected function isSuperAdmin() {
        return Session::isSuperAdmin();
    }

    /**
     * Return email address of logged-in user
     *
     * @return str email
     */
    protected function getLoggedInUser() {
        return Session::getLoggedInUser();
    }

    /**
     * Returns cache key as a string,
     * Preface every key with .ht to make resulting file "forbidden" by request thanks to Apache's default rule
     * <FilesMatch "^\.([Hh][Tt])">
     *    Order allow,deny
     *    Deny from all
     *    Satisfy All
     * </FilesMatch>
     *
     * Set to public for the sake of tests only.
     * @return str cache key
     */
    public function getCacheKeyString() {
        $view_cache_key = array();
        if ($this->getLoggedInUser()) {
            array_push($view_cache_key, $this->getLoggedInuser());
        }
        $keys = array_keys($_GET);
        foreach ($keys as $key) {
            array_push($view_cache_key, $_GET[$key]);
        }
        return '.ht'.$this->view_template.self::KEY_SEPARATOR.(implode($view_cache_key, self::KEY_SEPARATOR));
    }

    /**
     * Generates web page markup
     *
     * @return str view markup
     */
    protected function generateView() {
        // add header javascript if defined
        if ( count($this->header_scripts) > 0) {
            $this->addToView('header_scripts', $this->header_scripts);
        }
        // add header CSS if defined
        if ( count($this->header_css) > 0) {
            $this->addToView('header_css', $this->header_css);
        }
        // add CSRF token if enabled and defined
        if ($this->view_csrf_token) {
            $csrf_token = Session::getCSRFToken();
            if (isset($csrf_token)) { $this->addToView('csrf_token', $csrf_token); }
        }

        $this->sendHeader();
        if (isset($this->view_template)) {
            if ($this->view_mgr->isViewCached()) {
                $cache_key = $this->getCacheKeyString();
                if ($this->profiler_enabled && !isset($this->json_data) &&
                strpos($this->content_type, 'text/javascript') === false) {
                    $view_start_time = microtime(true);
                    $cache_source = $this->shouldRefreshCache()?"DATABASE":"FILE";
                    $results = $this->view_mgr->fetch($this->view_template, $cache_key);
                    $view_end_time = microtime(true);
                    $total_time = $view_end_time - $view_start_time;
                    $profiler = Profiler::getInstance();
                    $profiler->add($total_time, "Rendered view from ". $cache_source . ", cache key: <i>".
                    $this->getCacheKeyString(), false).'</i>';
                    return $results;
                } else {
                    return $this->view_mgr->fetch($this->view_template, $cache_key);
                }
            } else {
                if ($this->profiler_enabled && !isset($this->json_data) &&
                strpos($this->content_type, 'text/javascript') === false) {
                    $view_start_time = microtime(true);
                    $results = $this->view_mgr->fetch($this->view_template);
                    $view_end_time = microtime(true);
                    $total_time = $view_end_time - $view_start_time;
                    $profiler = Profiler::getInstance();
                    $profiler->add($total_time, "Rendered view (not cached)", false);
                    return $results;
                } else  {
                    return $this->view_mgr->fetch($this->view_template);
                }
            }
        } else if (isset($this->json_data) ) {
            $this->setContentType('application/json');
            if ($this->view_mgr->isViewCached()) {
              if ($this->view_mgr->is_cached('json.tpl', $this->getCacheKeyString())) {
                    return $this->view_mgr->fetch('json.tpl', $this->getCacheKeyString());
                } else {
                    $this->prepareJSON();
                    return $this->view_mgr->fetch('json.tpl', $this->getCacheKeyString());
                }
            } else {
              $this->prepareJSON();
                return $this->view_mgr->fetch('json.tpl');
            }
        } else {
            throw new Exception(get_class($this).': No view template specified');
        }
    }

    /**
     * Prepares the JSON data in $this->json_data and adds it to the current view under the key "json".
     *
     * @param bool $indent Whether or not to indent the JSON string. Defaults to true.
     * @param bool $stripslashes Whether or not to strip escaped slashes. Default to true.
     * @param bool $convert_numeric_strings Whether or not to convert numeric strings to numbers. Defaults to true.
     */
    private function prepareJSON($indent = true, $stripslashes = true, $convert_numeric_strings = true) {
        if (isset($this->json_data)) {
            $json = json_encode($this->json_data);
            if ($stripslashes) {
                // strip escaped forwardslashes
                $json = preg_replace("/\\\\\//", '/', $json);
            }
            if ($convert_numeric_strings) {
                // converts numeric strings to numbers
                $json = Utils::convertNumericStrings($json);
            }
            if ($indent) {
                // indents JSON strings so they are human readable
                $json = Utils::indentJSON($json);
            }
            $this->addToView('json', $json);
        }
    }

    /**
     * Send Content-Type header
     */
    protected function sendHeader() {
        if ( !headers_sent() ) { // suppress 'headers already sent' error while testing
            header('Content-Type: ' . $this->content_type, true);
        }
    }

    /**
     * Send Location header
     * @param str $destination
     * @return bool Whether or not redirect header was sent
     */
    protected function redirect($destination=null) {
        if (!isset($destination)) {
            $destination = Utils::getSiteRootPathFromFileSystem();
        }
        $this->redirect_destination = $destination; //for validation
        if ( !headers_sent() ) {
            header('Location: '.$destination);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Sets the view template filename
     *
     * @param str $tpl_filename
     */
    protected function setViewTemplate($tpl_filename) {
        $this->view_template = $tpl_filename;
    }

    /**
     * Sets json data structure to output a json string, and sets Content-Type to appplication/json
     *
     * @param array json data
     */
    protected function setJsonData($data) {
        if ($data != null) {
            $this->setContentType('application/json');
        }

        $this->json_data = $data;
    }

    /**
     * Sets Content Type header
     *
     * @param string Content Type
     */
    protected function setContentType($content_type) {
        if ($content_type != 'image/png') {
            $this->content_type = $content_type.'; charset=UTF-8';
        } else {
            $this->content_type = $content_type;
        }
    }

    /**
     * Gets Content Type header
     *
     * @return string Content Type
     */
    public function getContentType() {
        return $this->content_type;
    }

    /**
     * Add javascript to header
     *
     * @param str javascript path
     */
    public function addHeaderJavaScript($script) {
        array_push($this->header_scripts, $script);
    }
    /**
     * Add CSS to header
     *
     * @param str CSS path
     */
    public function addHeaderCSS($css) {
        array_push($this->header_css, $css);
    }
    /**
     * get CSS scripts
     *
     * @return array List of CSS files
     */
    public function getHeaderCSS() {
        return $this->header_css;
    }

    /**
     * Add data to view template engine for rendering
     *
     * @param str $key
     * @param mixed $value
     */
    protected function addToView($key, $value) {
        $this->view_mgr->assign($key, $value);
    }

    /**
     * Invoke the controller
     *
     * Always use this method, not control(), to invoke the controller.
     * @TODO show get 500 error template on Exception
     * (if debugging is true, pass the exception details to the 500 template)
     */
    public function go() {
        try {
            $this->initalizeApp();
            // are we in need of a database migration?
            $classname = get_class($this);
            /*
            if ($classname != 'InstallerController' && $classname != 'BackupController' &&
            UpgradeDatabaseController::isUpgrading( $this->isAdmin(), $classname) ) {
                $this->setViewTemplate('install.upgradeneeded.tpl');
                $this->disableCaching();
                $option_dao = DAOFactory::getDAO('OptionDAO');
                $option_dao->clearSessionData(OptionDAO::APP_OPTIONS);
                return $this->generateView();
            }
            */ 
            if ($classname) {
                $results = $this->control();
                if ($this->profiler_enabled && !isset($this->json_data)
                && strpos($this->content_type, 'text/javascript') === false
                && strpos($this->content_type, 'text/csv') === false) {
                    $end_time = microtime(true);
                    $total_time = $end_time - $this->start_time;
                    $profiler = Profiler::getInstance();
                    $this->disableCaching();
                    $profiler->add($total_time,
                    "total page execution time, running ".$profiler->total_queries." queries.");
                    $this->setViewTemplate('_profiler.tpl');
                    $this->addToView('profile_items',$profiler->getProfile());
                    return  $results . $this->generateView();
                } else  {
                    return $results;
                }
            }
        } catch (ControllerAuthException $e) {
            Utils::setDefaultTimezonePHPini();
            $this->setErrorTemplateState();
            $this->addToView('error_type', get_class($e));
            $config = Config::getInstance();
            $message = 'You must <a href="'.$config->getValue('site_root_path').
            'session/login.php">log in</a> to do this.';
            $this->addErrorMessage($message, null, true);
            return $this->generateView();
        } catch (ConfigurationException $e) {
            $this->setErrorTemplateState();
            $this->addToView('error_type', get_class($e));
            $message = 'SternIndia\'s configuration file does not exist! Try <a href="'.
            Utils::getSiteRootPathFromFileSystem().'install/">installing ThinkUp.</a>';
            $this->addErrorMessage($message, null, true);
            return $this->generateView();
        } catch (Exception $e) {
            Utils::setDefaultTimezonePHPini();
            $this->setErrorTemplateState();
            $this->addToView('error_type', get_class($e));
            $disable_xss = false;
            // if we are an installer exception, don't filter XSS, we have markup, and we trust this content
            if (get_class($e) == 'InstallerException') {
                $disable_xss = true;
            }
            $this->addErrorMessage($e->getMessage(), null, $disable_xss);
            return $this->generateView();
        }
    }

    /**
     * set proper error message and template
     */
    private function setErrorTemplateState() {
        $content_type = $this->content_type;
        if (strpos($content_type, ';') !== false) {
            $exploded = explode(';', $content_type);
            $content_type = array_shift($exploded);
        }
        switch ($content_type) {
            case 'application/json':
                $this->setViewTemplate('500.json.tpl');
                break;
            case 'text/plain':
                $this->setViewTemplate('500.txt.tpl');
                break;
            default:
                $this->setViewTemplate('500.tpl');
        }
    }
    /**
     * Initalize app
     * Load config file and required plugins
     * @throws Exception
     */
    private function initalizeApp() {
        $classname = get_class($this);
        if ($classname != "InstallerController") {
            //Initialize config
            $config = Config::getInstance();
            if ($config->getValue('debug')) {
                ini_set("display_errors", 1);
                ini_set("error_reporting", E_STRICT);
            }
            /*
            if ($classname != "BackupController") {
                //Init plugins
                $plugin_dao = DAOFactory::getDAO('PluginDAO');
                $active_plugins = $plugin_dao->getActivePlugins();
                Loader::definePathConstants();
                foreach ($active_plugins as $active_plugin) {
                    //add plugin's model and controller folders as Loader paths here
                    Loader::addPath(EFC_WEBAPP_PATH.'plugins/'.$active_plugin->folder_name."/model/");
                    Loader::addPath(EFC_WEBAPP_PATH.'plugins/'.$active_plugin->folder_name.
                    "/controller/");
                    //require the main plugin registration file here
                    if ( file_exists(
                    EFC_WEBAPP_PATH.'plugins/'.$active_plugin->folder_name."/controller/".
                    $active_plugin->folder_name.".php")) {
                        require_once EFC_WEBAPP_PATH.'plugins/'.$active_plugin->folder_name."/controller/".
                        $active_plugin->folder_name.".php";
                    }
                }
            }
            */
        }
    }

    /**
     * Provided for tests only, to assert that proper view values have been set. (Debug must be equal to true.)
     * @return ViewManager
     */
    public function getViewManager() {
        return $this->view_mgr;
    }

    /**
     * Turn off caching
     * Provided in case an individual controller wants to override the application-wide setting.
     */
    protected function disableCaching() {
        $this->view_mgr->disableCaching();
    }

    /**
     * Check if cache needs refreshing
     * @return bool
     */
    protected function shouldRefreshCache() {
        if ($this->view_mgr->isViewCached()) {
            return !$this->view_mgr->is_cached($this->view_template, $this->getCacheKeyString());
        } else {
            return true;
        }
    }

    /**
     * Set web page title
     * This method only works for views that reference _header.tpl.
     * @param str $title
     */
    public function setPageTitle($title) {
        $this->addToView('controller_title', $title);
    }

    /**
     * Add error message to view.
     * Include field if the message goes on a specific place on the page; otherwise leave it null for the message
     * to be page-level.
     * @param str $msg
     * @param str $field Defaults to null for page-level messages.
     * @param bool $disable_xss Disable HTML encoding tags, defaults to false
     */
    public function addErrorMessage($msg, $field=null, $disable_xss=false) {
        $this->disableCaching();
        $this->view_mgr->addErrorMessage($msg, $field, $disable_xss);
    }

    /**
     * Add success message to view
     * Include field if the message goes on a specific place on the page; otherwise leave it null for the message
     * to be page-level.
     * @param str $msg
     * @param str $field Defaults to null for page-level messages.
     * @param bool $disable_xss Disable HTML encoding tags, defaults to false
     */
    public function addSuccessMessage($msg, $field=null, $disable_xss=false) {
        $this->disableCaching();
        $this->view_mgr->addSuccessMessage($msg, $field, $disable_xss);
    }

    /**
     * Add informational message to view
     * Include field if the message goes on a specific place on the page; otherwise leave it null for the message
     * to be page-level.
     * @param str $msg
     * @param str $field Defaults to null for page-level messages.
     * @param bool $disable_xss Disable HTML encoding tags, defaults to false
     */
    public function addInfoMessage($msg, $field=null, $disable_xss=false) {
        $this->disableCaching();
        $this->view_mgr->addInfoMessage($msg, $field, $disable_xss);
    }

    /**
     * Will enable a CSRF token in the view
     */
    public function enableCSRFToken() {
        $this->view_csrf_token = true;
    }

    /**
     * Get the view CSRF token enabled status
     */
    public function isEnableCSRFToken() {
        return $this->view_csrf_token;
    }

    /**
     * Validate the CSRF token passed in the request data.
     * @throws invalid InvalidCSRFTokenException
     * @return bool True if $_POST['csrf_token'] or $_GET['csrf_token'] is valid
     */
    public function validateCSRFToken() {
        $token = 'no token passed';
        if (isset($_POST['csrf_token'])) {
            $token = $_POST['csrf_token'];
        } else if (isset($_GET['csrf_token'])) {
            $token = $_GET['csrf_token'];
        }
        $session_token = Session::getCSRFToken();
        if ($session_token && $session_token == $token) {
            return true;
        } else {
            throw new InvalidCSRFTokenException($token);
        }
    }

    /**
     * Redirect this controller to a Stern India-hosted URL.
     * @param  str $page Optional filename at endpoint
     * @return void
     */

    public function redirectToEmpoddyLabsEndpoint($page=null,$redirect=null) {
        $config = Config::getInstance();
        $empoddy_endpoint = $config->getValue('empoddy_endpoint');
        //var_dump($empoddy_endpoint);exit;
        //$empoddy_endpoint = 'http://192.168.2.102/sternDev/webapp/session/login.php';
        if (isset($empoddy_endpoint)) {
            // ternary operator is being used here
            $this->redirect($empoddy_endpoint.(isset($page)?$page:'').((isset($redirect))?'?redirect='.$redirect:''));
            return true;
        } else {
            return false;
        }
    }

    protected function sendJsonResponse($status, $data) {
        $this->setViewTemplate(null);
        $res=array();
        $res['status'] = $status;
        if ($status == 500) {
          $res['error'] = $data;
        } else {
          $res['results'] = $data;
        }
        $this->setJsonData($res);
    }

    protected function checkValidation(&$field_present, $rules) {


    }

    protected function checkMandatoryFields(&$fields_present, $req_params) {


    }

    public function makeNotifyProcessQueue($tpl, $args) {
    
    }

    public function makeSendMailQueue($tpl,$args) {
    	  $config =Config::getInstance();
        $es = new ViewManager();
        $es->caching=false;
        foreach($args['data'] as $key => $value) {
            $es->assign($key,$value);
        }
        $email = $args['data']['email'];
        $message = $es->fetch($tpl);
        $subject = "Activate your account on ".$config->getValue('app_title_prefix').
          " | Registeration !";
        $args= array('queue' => 'user_mail',
          'control' => 'ResqueController',
          'args' => array('sendUserMail',$email,$message,$subject)
        );
        $this->enqueueResque($args);
    }

    public function enqueueResque($args) {
        Resque::enqueue($args['queue'],$args['control'],$args['args']);
    }

    public function checkPermission($str) {
        $userClass = SessionCache::get('user_class');
        if(isset($userClass)) {
          $checkKey = "stern:user_permission:".$userClass;
        } else {
            return false;
        }
        return $this->redis->sIsMember($checkKey,$str);
    }

    public function getFbLoingUrl() {
        $helper = $this->facebook->getRedirectLoginHelper();
        $permissions = ['email','user_likes']; // optional
         $loginUrl = $helper->getLoginUrl('http://www.olx.hack/EFC/webapp/session/login.php', 
                            $permissions);
         return $loginUrl;
          //echo '<a href="' . $loginUrl . '">Log in with Facebook!</a>';

    }

    public function getFbAccessToken() {
        $helper = $this->facebook->getRedirectLoginHelper();
        if(isset($this->fb_token)) {
             return $this->fb_token;
        } else {
            try {
              $this->fb_token = $helper->getAccessToken();
              return $this->fb_token;
            } catch(Facebook\Exceptions\FacebookResponseException $e) {
              // When Graph returns an error
              return 'Graph returned an error: ' . $e->getMessage();
              exit;
            } catch(Facebook\Exceptions\FacebookSDKException $e) {
              // When validation fails or other local issues
              echo 'Facebook SDK returned an error: ' . $e->getMessage();
              exit;
            }
            if (isset($accessToken)) {
                // Logged in!
                $_SESSION['facebook_access_token'] = (string) $accessToken;
    
                // Now you can redirect to another page and use the
                // access token from $_SESSION['facebook_access_token']
            }
        }
    }

}
