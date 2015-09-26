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
class NotifyController extends EFCController {

  const ONE_2_ONE = 1;
  const ONE_2_MANY = 2;

  public function AuthControl() {
      if ($_GET['action'] == 'get_all') {
        $this->getAllNotification();
      }
      /*
      *@TODO: add notification will be done in some action, like case 
      *updated, user add, user delete, documents uploaded, case added, and so 
      *on.
       */ 
      if ($_POST['add']) {
          $this->addNotify($_POST['what'],$_POST['who']);
          return ;
      }
      
      $this->generateView();
  }

  public function makeNotifyTitle() {
  
  }

  public function makeNotifyBody() {

  }

  public function addNotify($what, $type = 0) {
    $notify_dao = DAOFactory::getDAO('NotifyDAO');
    $notify = array();
    $notify['notify_type'] = $type;
    $notify['user_id'] = SessionCache::get('user_id');
    $notify['title'] = "<a href=#>Prabhat</a> added You a".$what;
    $notify['body'] = makeNotifyBody($what); // will contain user Image + Title + Date/Time.
    if ($notify_dao->insertNotification($notify)) {
      $notify_id = $notify_dao->getInsertId();
      unset($notify['notify_type']);
      unset($notify['event_class']);
      //$notify['user_id'] = $this->getLoggedInUser();
      $notify['user_id'] = $who;
      $notify_dao->insertMakeNotification($notify);
    }
  }

  public function getAllNotification() {
    $notify_dao = DAOFactory::getDAO('NotifyDAO');
    $notify = $notify_dao->getAllNotification($this->getLoggedInUser);
    $this->sendJsonResponse(200,$notify);
  }

  public function viewNotify() {
      
  }

  public function deleteNotify() {
  
  }

  public function updateNotify() {
      
  
  }


}
