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
class UserController extends EFCController {
    /**
     * Required form submission values
     * @var array
     */
    var $REQUIRED_PARAMS = array(
        'first_name',
        'last_name',
        'email',
        'pwd',
        'cpwd'
        );
    /**
     *
     * @var boolean
     */
    var $is_missing_param = false;
    /**
     * return for user function
     * @var 
     */    
    var $retType = false;

    public function __construct($session_started=false) {
        parent::__construct($session_started);
        //$this->setViewTemplate('_user_register.tpl');
        $this->addToView('first_name',SessionCache::get('first_name'));
        $this->setPageTitle('User Registeration');
    }
    
    public function control() {

    	$this->redirectToEmpoddyLabsEndPoint();

    	$config =Config::getInstance();

    	if ($this->isLoggedIn()) {
	    	//$user_dao = DAOFactory::getDAO('UserDAO');
        $user_dao = DAOFactory::getDAO('UserDAO');

        switch ($_GET['action']) {
            case 'add':

              if($this->checkPermission('add_user')) {
                
                return $this->addUser($user_dao);
              } else {
                //@TODO: handle not permitted case
              }
              break;
            case 'view':
              if($this->checkPermission('view_user')) {
                return $this->viewUser($user_dao);
              } else {
                //@TODO: handle not permitted case
              }
              break;
            case 'modify':
              if($this->checkPermission('modify_user')) {
                return $this->modifyUser($user_dao);
              } else {
                //@TODO: handle not permitted case
              }
              break;
            case 'delete':
              if($this->checkPermission('delete_user')) {
                return $this->deleteUser($user_dao);
              } else {
                //@TODO: handle not permitted case
              }
              break;
            default:
              //@TODO : A error page to be shown
              return "You don't have permission to do the same.";
        }
      } else {
	    	$controller = new LoginController(true);
	    	return $controller->go();
	    }
    }


    private function addUser($user_dao=null) {

        $user_arr = array();
    	$this->disableCaching();
    	$has_been_registered = false;

      $this->setViewTemplate('_user_register.tpl');

    	if (isset($_POST['Submit']) && $_POST['Submit'] == 'Submit' ) {
        
        $msg = array();

            foreach ($this->REQUIRED_PARAMS as $param) {
                if (!isset($_POST[$param]) || $_POST[$param] == '' ) {
                    $msg[] = 'Please fill out all required fields.';
                    $this->sendJsonResponse(500, $msg);

                    $this->addErrorMessage('Please fill out all required fields.');
                    $this->is_missing_param = true;
                    unset($user_arr);
                    break;
                } else {
                    //else make array of all Posts array
                    $user_arr[$param] = $_POST[$param];
                }
            }


            if (!$this->is_missing_param) {
                $valid_input = true;
                if (!Utils::validateEmail($_POST['email']) || !Utils::validateEmail($_POST['reporting'])) {
                    $this->addErrorMessage("Sorry, that email address for user looks wrong. Can you double-check it?", 'email');
                    $valid_input = false;
                }

                if (!$user_dao->doesUserExist($_POST['reporting'])) {
                    $msg[] = "Sorry, that email address for reporting manager looks wrong. Can you double-check it?";
                    $this->sendJsonResponse(500, $msg);
                    $this->addErrorMessage("Sorry, that email address for reporting manager looks wrong. Can you double-check it?", 'email');
                    $valid_input = false;
                }                


                //TODO: check for reporting user type 

                if ($valid_input) {
              	                
                    if ($user_dao->doesUserExist($_POST['email'])) {
                        $msg[] = "User account already exists.";
                        $this->sendJsonResponse(500, $msg);
                        $this->addErrorMessage("User account already exists.", 'email');
                    } else {

                        //$activation_code =  123456;                        
                        $activation_code =  $user_dao->create($user_arr);                        

                        if ($activation_code != false) {
                            $config =Config::getInstance();
                            $msg[] = $user_arr['first_name']." has been registered successfully";
                            $args = array (
                              'subject' => "Activate your account on ".
                                   $config->getValue('app_title_prefix').
                                   " | Registeration !",
                               'data' => array (
                                   'application_url' => Utils::getApplicationURL(false),
                                   //'email' => urlencode($POST['email']),
                                   'email' => $_POST['email'],
                                   'activ_code' => $activation_code['activation_code'],
                                   'password' => $activation_code['password']
                               ));
                            //$this->sendJsonResponse(1, $msg);
                            $this->makeSendMailQueue('_email.registration.tpl', $args);
                            $this->sendJsonResponse(200, $msg);
                        } else {
                            $msg[] = "Unable to register a new user. Please try again.";
                            $this->sendJsonResponse(500, $msg);
                            $this->addErrorMessage("Unable to register a new user. Please try again.");
                        }
                    }
                }
            }

        }
            //$this->addToView('has_been_registered', $has_been_registered);
        return $this->generateView(); 
	}
    
    public function userRegisterMail($send=true,$activation_code=null) {
    	  $config =Config::getInstance();
        $es = new ViewManager();
        $es->caching=false;
        $es->assign('application_url', Utils::getApplicationURL(false) );
        $es->assign('email', urlencode($_POST['email']) );
        $es->assign('activ_code', $activation_code['activation_code'] );
        $es->assign('password', $activation_code['password'] );
        $message = $es->fetch('_email.registration.tpl');
        $subject = "Activate your account on ".$config->getValue('app_title_prefix').
          " | Registeration !";
        $args= array('queue' => 'user_mail',
          'control' => 'UserController',
          'args' => array('userRegisterMail',$message,$subject)
        );
        $this->enqueueResque($args);
        /*
        Mailer::mail($_POST['email'], "Activate Your Account on ".
        $config->getValue('app_title_prefix')." | Registeration !", $message);
        */
    }
    private function viewUser($user_dao) {
        $this->setViewTemplate('_user_grid.tpl');

        $users = $user_dao->getAllUsersInfo();


        $this->addToView('users',$users);

        return $this->generateView();
    }

    private function modifyUser($user_dao) {

        $this->setViewTemplate('new_user_profile.tpl');
        if (isset($_POST['Submit']) && $_POST['Submit'] == 'Update') {

            //Check for the parameters that has to be updated
            foreach ($_POST as $key => $value) {
                # code...
            }


        } else {

        }
        return $this->generateView();



    }

    private function deleteUser() {
    }

}


