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

class ClientController extends EFCController {

    /**
     * Required form submission values
     * @var array
     */
    static $REQUIRED_PARAMS = array(
    
        );

    var $PROCESS_MAP = array(
      'setup' => array (
        'checkPermission' => 'client_setup',
        'function' => 'clientSetup'
      ),

      'add' => array (
        'checkPermission' => 'add_client',
        'function' => 'addClient'
      ),
      'view' => array (
        'checkPermission' => 'view_client',
        'function' => 'viewClient'
      ),
      'modify' => array (
        'checkPermission' => 'mod_client',
        'function' => 'modifyClient'
      ),
      'delete' => array (
        'checkPermission' => 'del_client',
        'function' => 'deleteClient'
      )
    );

    var $action_missing = false;
    var $is_missing_param = false;
    var $missing_field = array();

    public function control() {

        $this->redirectToEmpoddyLabsEndPoint();
        $config =Config::getInstance();

        if ($this->isLoggedIn()) {
            //$user_dao = DAOFactory::getDAO('UserDAO');

            foreach ($PROCESS_MAP as $key => $value) {
              if ($_GET['action'] == $key) {
                if ($this->checkPermission($value['checkPermission'])) {
                  return $this->{$value['function']}();
                } else {
                  //@TODO set error response denied permission
                }
              }
            }

            //@TODO : error response action was not there
            
            $user_dao = DAOFactory::getDAO('UserDAO');
            //return $this->addUser($user_dao);
            if ($_GET['action'] == 'setup') {
                if ($this->checkPermission('client_setup')) {
                    return $this->clientSetup();   
                } else {    
                    #code for error message 
                }
            } else if ($_GET['action'] == 'add') {
                if ($this->checkPermission('client_add')) {
                    return $this->addClient($user_dao);   
                } else {    
                    #code for error message 
                }
            } 
        } else {
            //return "Here comes the control";
            $controller = new LoginController(true);
            return $controller->go();
            //return $this->addUser($user_dao);
        }
    }
 
    public function clientSetup() {
        $this->disableCaching();
        $this->setViewTemplate('new_client_setup.tpl');
        if (isset($_POST['Submit']) && $_POST['Submit'] == 'Submit' ) {
          if (isset($_POST['company'])) {
              $company_id = CompanyController::addCompany($_POST['company'], 1);
              
              if (isset($_POST['client']) && $company_id) {
                $_POST['client']['company_branch_id'] = $company_id;
                $client_id = $this->addClient($_POST['client']);
                if(isset($_POST['package']) && $client_id) {
                  //here have to do work for packages, either selection of 
                  //package of creation of package.
                  $package_id = PackageController::makePackage();
                  
                  if(isset($_POST['severity']) && $package_id) {
                    SeverityGridController::addSeverity($_POST['severity']);
                  } else {
                    // error response
                  }
                } else {
                  // error response
                }
              } else {
                // error response
              }
            } else {
              //error_response
            }
            //Add Client into database
            $client_data = $_POST['client'];
            if(!isset($client_data['company_branch_id']) || $client_data['company_branch_id'] == '') {
                $client_data['company_branch_id'] = $company_id;
            }
        }
        return $this->generateView();        
    }

    private function addClient($client_data) {

        foreach ($client_data as $key => $value) {
            if (isset($value)) {

                //Checking the required params.
                foreach (self::$REQUIRED_PARAMS as $param) {
                    if (!isset($value[$param]) || $value[$param] == '' ) {
                        self::$is_missing_param = true;
                        break;
                    }
                }   

                //if not found any missing terms.
                if (!self::$is_missing_param) {
                    $value['added_by'] = SessionCache::get('user_id');
                    $user_dao = DAOFactory::getDAO('UserDAO');
                    $activation_code = $user_dao->create($value);
                    
                    if($activation_code != false) {
                        //add in queue of nodejs to send mail 
                        //@send mail through node.js , we will add the contents in queue.

                    }
                    self::$is_missing_param = false;
                } else {

                    //$this->sendJsonResponse(0,$msg);
                }
            }
        }    
    }

    private function viewClient() {

    }

    private function modifyClient() {

    }

    private function deleteClient() {

    }   
  
}
