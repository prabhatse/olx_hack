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

class UserInfo { 
	/** * @var int */ 
	var $id; 

	/** * @var int */ 
	var $user_id; 

	/** * @var int reporting to */ 
	var $parent_id; 

	/** * @var str */ 
	
	var $employee_id; 
	/** * @var str */ 
	var $first_name; 
	/** * @var str */ var $last_name; /** * @var int user type. */ var $user_type; 
	/** * @var str */ var $email; /** * @var str */ var $sec_email; 
	/** * @var str */ var $phone_no; /** * @var str */ var $sec_phone_no; 
	/** * @var int */ var $address_id1; /** * @var int */ var $address_id2; 
	/** * @var int */ var $city_id; /** * @var int */ var $state_id; 
	/** * @var int */ var $country_id; /** * @var str */ var $postcode; 
	/** * @var int */ var $gender; /** * @var date */ var $birth_date; 
	/** * @var str */ var $photo_url; /** * @var int */ var $email_notification; 
	/** * @var str */ var $timezone; /** * @var int */ var $account_status; 
	/** * @var str Date user registered for an account. */ 
	var $created_date; 
	/** * @var int User creator */ 
	var $create_by; 
	/** * @var str */ 
	var $modified_date; 
	/** * @var int User creator */ 
	var $modified_by; 

	public function __construct($row = false) { 
		if ($row) { 
			$this->id = $row['id']; 
			$this->user_id = $row['user_id']; 
			$this->parent_id = $row['parent_id']; 
			$this->employee_id = $row['employee_id']; 
			$this->first_name = $row['first_name']; 
			$this->last_name = $row['last_name']; 
			$this->user_type = $row['user_type']; 
			$this->email = $row['email']; 
			$this->sec_email = $row['sec_email']; 
			$this->phone_no = $row['phone_no']; 
			$this->sec_phone_no = $row['sec_phone_no']; 
			$this->address_id1 = $row['address_id1']; 
			$this->address_id2 = $row['address_id2']; 
			$this->city_id = $row['city_id']; 
			$this->state_id = $row['state_id']; 
			$this->country_id = $row['country_id']; 
			$this->postcode = $row['postcode']; 
			$this->gender = $row['gender']; 
			$this->birth_date = $row['birth_date']; 
			$this->photo_url = $row['photo_url']; 
			$this->email_notification = $row['email_notification']; 
			$this->timezone = $row['timezone']; 
			$this->account_status = $row['account_status']; 
			$this->created_date = $row['created_date']; 
			$this->create_by = $row['create_by']; 
			$this->modified_date = $row['modified_date']; 
			$this->modified_by = $row['modified_by']; 
		} 
	} 
} 