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
class RegisterController extends EFCController {
    /**
     * Required form submission values
     * @var array
     */
    var $REQUIRED_PARAMS = array('first_name','last_name','email','pwd');
    /**
     *
     * @var boolean
     */
    var $is_missing_param = false;

    public function __construct($session_started=false) {
        parent::__construct($session_started);
        $this->setViewTemplate('register.tpl');

        $this->setPageTitle('User Registeration');
    }

    public function control(){
        $this->redirectToEmpoddyLabsEndpoint();
        if ($this->isLoggedIn()) {
            $controller = new DashboardController(true);
            return $controller->go();
        } else {
            $this->disableCaching();


            $has_been_registered = false;
            $is_registration_open = true;

            if ( !$is_registration_open && !$is_invite_code_valid ){
                $this->addToView('closed', true);
                $disable_xss = true;
                $this->addErrorMessage('Sorry, registration is closed on '.
                         $config->getValue('app_title_prefix')."EFC Labs. ".
                         'Try <a href="https://EFC">EFC</a>.'
                         , null, $disable_xss);
            } else {
                 $user_arr = array();
                $user_dao = DAOFactory::getDAO('UserDAO');
                $this->addToView('closed', false);
                $captcha = new Captcha();
                if (isset($_POST['Submit']) && $_POST['Submit'] == 'Register' ) {
                    foreach ($this->REQUIRED_PARAMS as $param) {
                        if (!isset($_POST[$param]) || $_POST[$param] == '' ) {
                            $this->addErrorMessage('Please fill out all required fields.');
                            $this->is_missing_param = true;
                        } else {
                            $user_arr[$param] = $_POST[$param];
                        }
                    }
                    if (!$this->is_missing_param) {
                        $valid_input = true;
                        if (!Utils::validateEmail($_POST['email'])) {
                            $this->addErrorMessage("Sorry, that email address looks wrong. Can you double-check it?", 'email');
                            $valid_input = false;
                        }

                        if (strcmp($_POST['pwd'], $_POST['cpwd']) || empty($_POST['pwd'])) {
                            $this->addErrorMessage("Passwords do not match.", 'password');
                            $valid_input = false;
                        } else if (!preg_match("/(?=.{8,})(?=.*[a-zA-Z])(?=.*[0-9])/", $_POST['pass1'])) {
                            $this->addErrorMessage("Password must be at least 8 characters and contain both numbers ".
                            "and letters.", 'password');
                            $valid_input = false;
                        }

                        if ($valid_input) {
                            if ($user_dao->doesUserExist($_POST['email'])) {
                                $this->addErrorMessage("User account already exists.", 'email');
                            } else {
                                // Insert the details into the database
                                $activation_code =  $user_dao->create($user_arr);

                                if ($activation_code != false) {
                                    /*
                                    $es = new ViewManager();
                                    $es->caching=false;
                                    $es->assign('application_url', Utils::getApplicationURL(false) );
                                    $es->assign('email', urlencode($_POST['email']) );
                                    $es->assign('activ_code', $activation_code );
                                    $message = $es->fetch('_email.registration.tpl');

                                    Mailer::mail($_POST['email'], "Activate Your Account on ".
                                    $config->getValue('app_title_prefix')."EFC", $message);

                                    $this->addSuccessMessage("Success! Check your email for an activation link.");
                                    //delete invite code
                                    if ( $is_invite_code_valid ) {
                                        $invite_dao->deleteInviteCode($invite_code);
                                    }
                                    */
                                    $has_been_registered = true;
                                    $this->addToView('success', $has_been_registered);
                                } else {
                                    $this->addErrorMessage("Unable to register a new user. Please try again.");
                                }
                            }
                        }
                    }
                    if (isset($_POST["first_name"])) {
                        $this->addToView('first_name', $_POST["first_name"]);
                    }
                    
                }
                
            }
            
            return $this->generateView();
        }
    }
}
