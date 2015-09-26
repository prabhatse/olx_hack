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


class ForgotPasswordController extends EFCController {

    public function control() {
        $this->redirectToSternIndiaEndpoint('forgot.php');
        $config = Config::getInstance();
        //$this->addToView('is_registration_open', $config->getValue('is_registration_open'));

       // if (isset($_POST['email']) && $_POST['Submit'] == 'Send Reset') {

        // /$_POST['email'] = 'prabhat@sternindia.com';

        if (isset($_POST['email']) ) {
            $this->disableCaching();

            $dao = DAOFactory::getDAO('UserDAO');
            $user = $dao->getByEmail($_POST['email']);
            if (isset($user)) {
                $token = $user->setPasswordRecoveryToken();

                $es = new ViewManager();
                $es->caching=false;

                //$es->assign('apptitle', $config->getValue('app_title_prefix')."ThinkUp" );
                $es->assign('first_name',$user->first_name);
                $es->assign('recovery_url', "session/reset.php?token=$token");
                $es->assign('application_url', Utils::getApplicationURL(false));
                $es->assign('site_root_path', $config->getValue('site_root_path') );

                $message = $es->fetch('_email.forgotpassword.tpl');
                $subject = $config->getValue('app_title_prefix') . "Stern India Password Recovery";
                
                //Will put the things in queue to mail the things.
                Resque::enqueue('user_mail','Mailer',array($_POST['email'],$subject, $message));

                $this->addToView('link_sent',true);
            } else {
                $this->addErrorMessage('Error: account does not exist.');
            }
        }
        $this->setViewTemplate('Session/forgot.tpl');
        return $this->generateView();
    }
}
