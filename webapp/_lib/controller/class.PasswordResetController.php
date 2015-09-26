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
class PasswordResetController extends EFCController {

    public function control() {
        $session = new Session();
        $user_dao = DAOFactory::getDAO('UserDAO');
        $this->setViewTemplate('Session/password_reset.tpl');
        $this->disableCaching();

        $config = Config::getInstance();

        //$_GET['token'] = '92ce85a194e2a698766dfe485e46e6a2';

        //$_POST['pwd'] = 'abcde_12345';
        //$_POST['pwd_cnf'] = 'abcde_12345';

        if (!isset($_GET['token']) || !preg_match('/^[\da-f]{32}$/', $_GET['token']) ||
        (!$user = $user_dao->getByPasswordToken($_GET['token']))) {

            //Profiler::debugPoint(true,__METHOD__, __FILE__, __LINE__, $_GET['token']);
            // token is nonexistant or bad
            $this->addErrorMessage('You have reached this page in error.');
            return $this->generateView();
        }

        if (!$user->validateRecoveryToken($_GET['token'])) {
            $this->addErrorMessage('Your token is expired.');
            return $this->generateView();
        }

        if (isset($_POST['pwd'])) {
            if ($_POST['pwd'] == $_POST['pwd_cnf']) {
                //$login_controller = new LoginController(true);
                // Try to update the password
                if ($user_dao->updatePassword($user->email, $_POST['pwd'] ) < 1 ) {
                    //$login_controller->addErrorMessage('Problem changing your password!');
                } else {

                    $appConfig = App::getInstance('AccountStatus');

                    $user_dao->activateUser($user->email);
                    $user_dao->setAccountStatus($user->email,$appConfig->getValue("ACTIVE"));
                    $user_dao->resetFailedLogins($user->email);
                    $user_dao->updatePasswordToken($user->email, '');
                    //$login_controller->addSuccessMessage('You have changed your password.');
                    $this->addToView('pwd_changed',true);
                }
                return $this->generateView();
                //return $login_controller->go();
            } else {
                $this->addErrorMessage("Passwords mismatched.");
            }
        } else if (isset($_POST['Submit'])) {
            $this->addErrorMessage('Please enter a new password.');
        }
        $this->addToView('token',$_GET['token']);
        return $this->generateView();
    }
}
