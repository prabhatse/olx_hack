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
abstract class EFCAuthController extends EFCController {
    /**
     * The web app URL this controller maps to.
     * @var str
     */
    var $url_mapping = null;

    public function __construct($session_started=false) {
        parent::__construct($session_started);
        $request_uri = Utils::getApplicationRequestURI();

        if (strpos($request_uri, 'logout.php') === false ) {
            $this->url_mapping = Utils::getApplicationURL().$request_uri;
        }
    }

    public function control() {
        $response = $this->preAuthControl();
        if (!$response) {
                return $this->authControl();
            if ($this->isLoggedIn()) {
                return $this->authControl();
            } else {
                $controller = new LoginController();
                return $controller->go();
                //return $this->bounce();
            }
        } else {
            return $response;
        }
    }

    /**
     * A child class can override this method to define other auth mechanisms.
     * If the return is not false it assumes the child class has validated the user and has called authControl()
     * @return boolean PreAuthed
     */
    protected function preAuthControl() {
        return false;
    }

    /**
     * Bounce user to public page or to error page.
     * @throws ControllerAuthException
     */
    protected function bounce() {
        if ($this->content_type == 'text/html; charset=UTF-8' && $this->url_mapping != null) {
            $this->redirect(Utils::getApplicationURL().'session/login.php?redirect='.$this->url_mapping);
        } else {
            throw new ControllerAuthException('You must log in to access this controller: ' . get_class($this));
        }
    }
}
