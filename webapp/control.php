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
require_once 'init.php';


$CONTROLS_MAP = array(
  'search' => 'SearchController',
  'user' => 'UserController',
  'client' => 'ClientController',
  'company' => 'CompanyController',
  'notify' => 'NotifyController',
  'location' => 'LocationController',
  'university' => 'UniversityController',
  'college' => 'CollegeController',
  'case' => 'CaseController',
  'notify' => 'NotifyController',
  'ajax' => 'AjaxApiController',
  'process'=> 'PackageController'
);


foreach ($CONTROLS_MAP AS $key => $value) {
  if($_GET['service'] == $key) {
    $controller = new $value;
    echo $controller->go();
  }
}

/*
define("USER_CREATE", 1);

if ($_GET['service'] == 'user') {
    
	$controller = new UserController();
	echo $controller->go();
}

if ($_GET['ser'])

 */


