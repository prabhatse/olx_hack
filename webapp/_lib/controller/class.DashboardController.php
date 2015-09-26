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


class DashboardController extends EFCController {
    /**
     * Number of insights to display on a page
     * @var int
     */
    //const PAGE_INSIGHTS_COUNT = 20;
    /**
     * Template name
     * @var string
     */

    //var $tpl_name = 'new_user_profile.tpl';
    var $tpl_name = 'dashboard.tpl';

    public function control() {
        
        if ($this->isLoggedIn()) {
          $config = Config::getInstance();
          $this->setViewTemplate($this->tpl_name);  

          $first_name = SessionCache::get('first_name');
          //$first_name = 'Session';
          $this->addToView('first_name', $first_name);
          //flush();
          return $this->generateView();
        } else {
            $controller = new LoginController(true);
            return $controller->go();
        }
    }
 
    public function getUserLikes() {
  
       $this->facebook->setDefaultAccessToken($this->fb_token);
       $resp = $this->facebook->get('/me');
        

    }



}
