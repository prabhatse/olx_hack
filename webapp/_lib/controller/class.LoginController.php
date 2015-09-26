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
class LoginController extends EFCController {

    public $reason=0;

    public function control() {
      
        if (isset($_GET['redirect'])) {
            $this->redirectToEmpoddyLabsEndpoint($page=null, $redirect=$_GET['redirect']);
        } else {
            $this->redirectToEmpoddyLabsEndpoint();
        }
        //var_dump($_GET);
        //var_dump($_SERVER);exit;
        $this->setPageTitle('Log in');
        $this->setViewTemplate('login.tpl');
        $this->disableCaching();

        // set var for open registration
        $config = Config::getInstance();

        // Set successful login redirect destination
        if (isset($_GET['redirect'])) {
            $this->addToView('redirect', $_GET['redirect']);
        }
        // If form has been submitted
        if (isset($_POST['redirect'])) {
            $this->addToView('redirect', $_POST['redirect']);
        }

        //don't show login form if already logged in
        if ($this->isLoggedIn()) {

            if ($this->isSuperAdmin()) {
                $controller = new DashboardController(true);
                return $controller->go();
            } else {
                $controller = new DashboardController(true);
                return $controller->go();
            }

        } else {
    
            
            //$user_dao = DAOFactory::getDAO('UserDAO');
           //$_POST['email'] = 'prabhat@sternindia.com';
            //$_POST['pwd'] = 'abcde_12345';
            //if (isset($_POST['Submit']) && $_POST['Submit']=='Log In' && isset($_POST['email']) &&
            //isset($_POST['pwd']) ) {
            if (isset($_POST['email']) && isset($_POST['pwd']) ) {
                 $user_dao = DAOFactory::getDAO('UserDAO');

                if ( $_POST['email']=='' || $_POST['pwd']=='') {
                    if ( $_POST['email']=='') {
                        $this->addErrorMessage("Email must not be empty");
                        return $this->generateView();
                    } else {
                        $this->addErrorMessage("Password must not be empty");
                        return $this->generateView();
                    }
                } else {   

                    $session = new Session();
                    $user_email = $_POST['email'];

                    $user_email = stripslashes($user_email);
                    
                    $this->addToView('email', $user_email);

                    $user = $user_dao->getByEmail($user_email);

                    if (!$user) {
                        $this->addErrorMessage("Hmm, that email seems wrong.");
                        return $this->generateView();
                    } elseif ($user->account_status != 11) {
                        $error_msg = 'Inactive account. ';
                        if ($user->failed_logins == 0) {
                            $error_msg .=
                            '<a href=\"http://localhost/EFC/webapp/session/login.php#activate-your-account\">' .
                            'You must be registered to get login in your account.</a>';
                        } elseif ($owner->failed_logins == 10) {
                            $error_msg .= $user->account_status .
                            '. <a href=\"http://localhost/EFC/webapp/session/forgot.php\">Reset your password.</a>';
                        }
                        $disable_xss = true;
                        $this->addErrorMessage($error_msg, null, $disable_xss);
                        return $this->generateView();
                        // If the credentials supplied by the user are incorrect
                    } elseif (!$user_dao->isUserAuthorized($user_email, $_POST['pwd']) ) {
                        $error_msg = "Hmm, that password seems wrong.";
                        if ($user->failed_logins == 9) { // where 9 represents the 10th attempt!
                            $user_dao->deactivateUser($user_email);
                            $status = 'Account deactivated due to too many failed logins';
                            $user_dao->setAccountStatus($user_email, $status);
                            $error_msg = 'Inactive account. ' . $status .
                            '. <a href=\"http://localhost/EFC/webapp/session/forgot.php\">Reset your password.</a>';
                        }
                        $user_dao->incrementFailedLogins($user_email);
                        $disable_xss = true;
                        $this->addErrorMessage($error_msg, null, $disable_xss);
                        return $this->generateView();
                    } else {
                        // user has logged in sucessfully this sets variables in the session
                        $session->completelogin($user);
                        $user_dao->updatelastlogin($user_email);
                        $user_dao->resetfailedlogins($user_email);

                        //$user_logon = daofactory::getdao('userlogondao');
                        //$user_logon->insertlogininfo();

                        if (isset($_post['redirect']) && $_post['redirect'] != '') {
                            $success_redir = $_post['redirect'];
                        } else {
                            $success_redir = $config->getvalue('site_root_path');
                        }
                        //$_get['action'] = 'add';
                       //$controller = new usercontroller();
                        //$controller = new dashboardcontroller(true);
                        // /return $controller->go();

                        if (!$this->redirect($success_redir)) {
                            if ($this->issuperadmin()) {
                                $controller = new dashboardcontroller(true);
                                return $controller->go();
                            }  else {

                                $controller = new dashboardcontroller(true);
                                return $controller->go();                                
                            }
                        }  
                            
                    }
                }
            } else if ($this->getFbAccessToken()){
                        Session::completeLoginUsingFb($this->fb_token);
                        //echo $this->fb_token;exit;
                        $this->facebook->setDefaultAccessToken($this->fb_token);

                        $resp = $this->facebook->get('/me');

                        var_dump($resp);exit;

                        if (isset($_post['redirect']) && $_post['redirect'] != '') {
                            $success_redir = $_post['redirect'];
                        } else {
                            $success_redir = $config->getvalue('site_root_path');
                        }

                        if (!$this->redirect($success_redir)) {
                                $controller = new DashboardController(true);
                                return $controller->go();                                
                        }  
                        $resp = $this->facebook->get('/me');



                        // user has logged in sucessfully this sets variables in the session
/*
                        $session->completelogin($user);
                        $user_dao->updatelastlogin($user_email);
                        $user_dao->resetfailedlogins($user_email);

                        //$user_logon = daofactory::getdao('userlogondao');
                        //$user_logon->insertlogininfo();

                        if (isset($_post['redirect']) && $_post['redirect'] != '') {
                            $success_redir = $_post['redirect'];
                        } else {
                            $success_redir = $config->getvalue('site_root_path');
                        }
                        if (!$this->redirect($success_redir)) {
                                $controller = new dashboardcontroller(true);
                                return $controller->go();
                            }
                        }  
                        SessionCache::put('fb_token',$this->fb_token);
*/               
                
            } else {
                $this->addToView('fb_login_url',$this->getFbLoingUrl());
                return $this->generateView();
            }
        }
    }
}
