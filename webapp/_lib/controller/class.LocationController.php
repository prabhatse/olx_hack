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
class LocationController extends EFCController {

    /**
     * Required form submission values
     * @var array
     */
    var $REQUIRED_PARAMS = array(
        'name',
        'location_type',
        'parent_id',
        'postcode');
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

    public function control() {
      if ($this->isLoggedIn()) {
        $location_dao = DAOFactory::getDAO('LocationDAO');
        if ($_GET['action'] == 'add') {
          if($this->checkPermission('add_location')) {
            return $this->addLocation($location_dao);
          } else {
            //error response
          }
        }
        if($_GET['action'] == 'location_suggest') {
          return $this->suggestLocation($location_dao);
        }
        if($_GET['action'] == 'view') {
          return $this->viewLocation($location_dao);
        }
        if($_GET['action'] == 'modify') {
          if($this->checkPermission('mod_location')) {
            return $this->modifyLocation($location_dao);
          } else {
            // error response
          }
        }
        if($_GET['action'] == 'delete') {
          if($this->checkPermission('del_location')) {
            return $this->deleteLocation($location_dao);
          } else {
            // error response
          }
        }
      } else {
        $controller = new LoginController(true);
        return $controller->go();
      }
    }

    public function suggestLocation($location_dao) {
      $find_arr = array();
      if (isset($_GET['prefix'])) {
        $find_arr['prefix'] = $_GET['prefix'];
        $find_arr['location_type'] = $_GET['location_type'];
      }
      if (isset($_GET['parent_id'])) {
        $find_arr['parent_id'] = $_GET['parent_id'];
      }
      $locations = $location_dao->getLocation($find_arr);
      if ($locations == NULL) {
        $this->sendJsonResponse(500,NULL);
      } else {
        $this->sendJsonResponse(200, $locations);
      }
      return $this->generateView();
    }

    private function addLocation($location_dao) {
      $this->setViewTemplate('add_location.tpl');

      //@TODO: logic to add location
      //make url for the location

      $this->generateView();
    }

    private function viewLocation($location_dao) {
      $this->setViewTemplate('view_location.tpl');

      //@TODO: logic to view location

      $this->generateView();
    }

    private function modifyLocation($location_dao) {
    
    }

    private function deleteLocation($location_dao) {
    
    }

}
