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

class CompanyController extends EFCController {
    /**
     * Required form submission values
     * @var array
     */
    static $REQUIRED_PARAMS = array(
        'company_id',
        'address_1',
        'city_id',
        'state_id',
        'country_id',
        'postal_code',
        'company_type' );

    var $is_missing_param = false;
    var $missing_field = array();

    public function control() {
        
    }


    private function addCompanyName($company_name) {
        $company_dao = DAOFactory::getDAO('CompanyDAO');

        if (isset($company_name)) {
            $company_dao->insertCompanyName($company_name);
        }
    }

    public static function addCompany($company_data,$client_setup=false) {


        if (isset($branch_data)) {
            //Checking the required params.
            foreach (self::$REQUIRED_PARAMS as $param) {
                if (!isset($branch_data[$param]) || $branch_data[$param] == '' ) {
                    self::$is_missing_param = true;
                    break;
                }
            }

            if (!$this->is_missing_param) {
                $branch_data['added_by'] = SessionCache::get('user_id');
                $company_dao = DAOFactory::getDAO('CompanyDAO');
                $ret = $company_dao->insertCompanyBranch($branch_data);
                return $ret;
            } else {
                //$this->sendJsonResponse(0,$msg);
            }
        }
    }



    // The below function will add the company name through ajax in client 
    // setup.
    
    public function add_company_name_api($company_name) {
        $ret = $this->addCompanyName($company_name);
        return $ret;
    }

    // will provice the company name like prefix
    public function company_suggest($prefix) {
    
    }

    private function viewCompany() {

    }

    private function modifyCompany() {

    }

    private function deleteCompany() {

    }        
    
}
