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
class ActivateAccountController extends EFCController {
    /**
     * Required query string parameters
     * @var array usr = instance email address, code = activation code
     */
    var $REQUIRED_PARAMS = array('usr', 'code');
    /**
     *
     * @var boolean
     */
    var $is_missing_param = false;
    /**
     * Constructor
     * @param bool $session_started
     * @return ActivateAccountController
     */
    public function __construct($session_started=false) {
        parent::__construct($session_started);
        foreach ($this->REQUIRED_PARAMS as $param) {
            if (!isset($_GET[$param]) || $_GET[$param] == '' ) {
                $this->is_missing_param = true;
            }
        }
    }

    public function control() {
           
        $controller = new LoginController(true);
        if ($this->is_missing_param) {
            $controller->addErrorMessage('Invalid account activation credentials.');
        } else {
            $user_dao = DAOFactory::getDAO('UserDAO');
            $acode = $user_dao->getActivationCode($_GET['usr']);
            if ($_GET['code'] == $acode['activation_code']) {
                $user = $user_dao->getByEmail($_GET['usr']);
                if (isset($user) && isset($user->account_status)) {
                    if ($user->account_status == 11) {
                        $controller->addSuccessMessage("You have already activated your account. Please log in.");
                    } else {
                        $user_dao->activateUser($_GET['usr']);
                        $controller->addSuccessMessage("Success! Your account has been activated. Please log in.");
                    }
                } else {
                    $controller->addErrorMessage('Houston, we have a problem: Account activation failed.');
                }
            } else {
                $controller->addErrorMessage('Houston, we have a problem: Account activation failed.');
            }
        }
        return $controller->go();
    }
}
