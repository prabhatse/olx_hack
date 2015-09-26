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
class PackageController extends EFCController {

  public function control() {
    
    if ($this->isLoggedIn()) {
        switch ($_GET['action']) {
            case 'add':
              if ($this->checkPermission('add_process')) {
                    return $this->addProcess();
              } else {
                    // send response as you don't have permission to do the same.
                }
                break;
            case 'view':
                if ($this->checkPermission('view_process')) {
                    return $this->viewProcess();
                } else {
                    // send response as you don't have permission to do the same.
                }
                break;
            case 'modify':
                if ($this->checkPermission('modify_process')) {
                    return $this->modifyProcess();
                } else {
                  echo "not permitted";exit;
                    // send response as you don't have permission to do the same.
                }
                break;
            case 'delete':
                if ($this->checkPermission('delete_process')) {
                    return $this->deleteProcess();
                } else {
                    // send response as you don't have permission to do the same.
                }
                break;
            case 'map':
                if ($this->checkPermission('map_process')) {
                    return $this->mapProcess();
                } else {
                    // send response as you don't have permission to do the same.
                }
                break;
            default:
                //Some error like url is not ok.
        }

    } else {
        $controller = new LoginController(true);
        echo $controller->go();
    }
	}

  private function addProcess($company = null) {
    if (isset($_POST['package'])) {
        $process_dao = DAOFactory::getDAO('ProcessDAO');
        $process = $_POST['package'];
        if ($process) {
            foreach($process as $value) {
              if(isset($value["name"]) && $value["name"] !=""){
                  $process_data['name'] = $value['name'];
                  $process_data['company_specific'] =  $value['company_specific'];
                  if ($company != null) {
                      $process_data['company_specific'] = 1;
                      $process_data['company_id'] = $company['id'];
                      $process_data['company_branch_id'] = $company['branch_id'];
                  }
                  $process_data['process_type'] = $value['process_type'];
                  $process_data['industry_type'] = $value['industry_type'];
                  $process_data['total_compo'] = $value["total_compo"];
                  $process_data['total_sub_compo'] = $value['total_sub_compo'];
                  $process_data['total_tat'] = $value['total_tat'];
                  $process_data['priority'] = $value['priority'];
                  $process_data['country_id'] = $value['country_id'];
                  $process_data['total_price'] = $value['total_price'];
                  $process_data['created_by'] = SessionCache::get('user_id');
                  
                  //Insert the process details
                  $process_id=$process_dao->insertProcessDetails($process_data);
                  if(isset($value["component"])){
                      foreach ($value["component"] as $compo => $compo_value) {
                          foreach($compo_value as $key => $sub_compo) {
                              $process_config_data = array();
                              $process_config_data["process_id"] = $process_id;
                              $process_config_data["process_compo"] = $compo;
                              $process_config_data["process_sub_compo"] = $key;
                              // insert data in process config table
                              $process_config_data["tat"] = $sub_compo['tat'];
                              $process_config_data['price']= $sub_compo['price'];
                              $process_dao->insertProcessConfigs($process_config_data);
                          }
                      }
                  }
                  //@TODO: Code to return something
               }
            }
        }
    }
    $this->generateView();
  }

	private function viewProcess($params = null) {
      $process_dao = DAOFactory::getDAO('ProcessDAO');
      $process_lists = $process_dao->getProcessList($params);
      
      //@TODO: Have to do work with process lists
	}

  private function mapProcess($company) {
      if(isset($_POST['selected_package']) && isset($company)) {
          $process_dao = DAOFactory::getDAO('ProcessDAO');

          foreach($_POST['selected_package'] as $value) {
              $map_process = array();
              $map_process['process_id'] = $value;
              $map_process['company_id'] = $company['id'];
              $map_process['company_branch_id'] = $company['branch_id'];
              $map_process['selected_by'] = SessionCache::get('user_id');
              $process_dao->mapSelectedProcess($map_process);
          }
      }
      return false;
  }

	private function modifyProcess() {

	}


	private function deleteProcess() {

	}

}
