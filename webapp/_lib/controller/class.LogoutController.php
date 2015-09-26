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
class LogoutController extends EFCAuthController {
    public function authControl() {

        $user_logon = DAOFactory::getDAO('UserLogonDAO');

        if(isset($_GET['reason'])) {
            $reason = 2;
        } else {
            $reason = 1;

        }

        $user_logon->userLogoutUpdate($reason);

        Session::logout();
        
        if (!$this->redirectToSternIndiaEndpoint('logout.php')) {
	        $controller = new LoginController(true);
            if($reason) {
                $controller->reason = $reason;
            } 
	        $controller->addSuccessMessage("You have successfully logged out.");
	        return $controller->go();
    	}
    }
}