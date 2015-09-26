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
class SearchController extends SternAuthController {

    var $ACTION_MAP = array(
        'company' => 'searchCompanyName',
        'location' => 'searchLocation',
        'user' => 'doesUserExist',
        'country_list' => 'searchCountry',
        'state_list' => 'searchState',
        'city_list' => 'searchCity',
        'process_list' => 'searchProcessList',
        'severity_list' => 'searchSeverityList'
    );

    public function AuthControl() {
        $this->disableCaching();
        foreach($this->ACTION_MAP as $key=>$value) {
          if($_GET['action'] == $key) {
            $getParams = Utils::getUriGetParams();
            return $this->{$value}($getParams);
          }
        }
        if ($this->shouldRefreshCache()) {
        
        }
        $get = Utils::getUriGetParams();
    }
    
    /*
     * Search company name function on request of aJaX.
     *
     * */
    private function searchCompanyName($getParams=null) {
      $key = $_SERVER['REQUEST_URI'];
      $ret = $this->memcache->get($key);

      if ($ret)  {
          $this->sendJsonResponse(200, $ret);
      } else {
          $company_dao = DAOFactory::getDAO("CompanyDAO");
          if ($getParams['prefix'] != null) {
            $ret = $company_dao->getAllCompanyName($getParams['prefix']);
            if ($ret) { 
                $this->memcache->set($key, $ret,MEMCACHE_COMPRESSED, 20);
                $this->sendJsonResponse(200,$ret);
            } else {
                $this->sendJsonResponse(500,$ret);
            }
          }
      }
      return $this->generateView();
       
        $company_dao = DAOFactory::getDAO("CompanyDAO");
        if ($getParams['prefix'] != null) {
          $ret = $company_dao->getAllCompanyName($getParams['prefix']);
          $this->sendJsonResponse(200,$ret);
          return $this->generateView();
        }
    }

    private function searchSeverityList() {
        $severity_dao = DAOFactory::getDAO('SeverityDAO');

    }
    /*
     * Search Location name function on request of aJaX.
     *
     * */
    private function searchLocation(array $getParams=null) {
      
      //$this->disableCaching();
      $location_dao = DAOFactory::getDAO('LocationDAO');
      if (isset($getParams['prefix'])) {
        $ret = $location_dao->getAllLocation($getParams,true);
        $this->sendJsonResponse(200,$ret);
        return $this->generateView();
      }
    }

    private function doesUserExist($get=null) {
      $user_dao = DAOFactory::getDAO('UserDAO');
      $ret = $user_dao->doesUserExist($get['user_email']);
      $this->sendJsonResponse(200,$ret);
      return $this->generateView();
    }

    private function viewProcessList($get = null) {
        $process_dao = DAOFactory::getDAO('UserDAO');
        $ret = $process_dao->getProcessList($get);
        $this->sendJsonResponse(200,$ret);
        return $this->generateView();
    }
}


/*
    private function getDataFromCache($key,$dao,$dao_function, $expiry_time,$params ) {
        $ret = $this->memcache->get($key);
        if ($ret) {
            $this->sendJsonResponse(200, $ret);
        } else {
            $dao = DAOFactory::getDAO($dao);
            if ($params)

        }
    
    }


 */

