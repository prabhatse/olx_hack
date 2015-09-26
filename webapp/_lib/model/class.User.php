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
class User { 
	/** * @var int */ 
	var $id; 
	/** * @var str */ 
	var $first_name; 
	/** * @var str */ 
	var $last_name; 	 
	/** * @var str */ 
	var $email; 
	/** * @var str Hash of the owner password */ 
	var $pwd; 
	/** * @var str Salt for securely hashing the owner password */ 
	var $pwd_salt; 
	/** * @var str Date user registered for an account. */ 
	var $created; 
	/** * @var str */ 
	var $modified;  
	/** * @var int User activation code. */ 
	var $activation_code; 
	/** * @var str Last time user logged into ThinkUp. */ 
	var $last_login; 
	/** * @var str */ 
	var $last_ip; 
	/** * @var str Password reset token. */ 
	var $password_token; 
	/** * @var int Current number of failed login attempts. */ 
	var $failed_logins; 
	/** * @var int User creator */ 
	var $created_by; 
	public function __construct($row = false) { 
		if ($row) { 
			$this->id = $row['id']; 
			$this->first_name = $row['first_name']; 
			$this->last_name = $row['last_name']; 
			$this->email = $row['email']; 
			$this->pwd = $row['pwd']; 
			$this->pwd_salt = $row['pwd_salt']; 
			$this->created = $row['created']; 
			$this->modified = $row['modified']; 
			$this->activation_code = $row['activation_code']; 
			$this->last_login = $row['last_login']; 
			$this->last_ip = $row['last_ip']; 
			$this->password_token = $row['password_token']; 
			$this->failed_logins = $row['failed_logins']; 
			$this->created_by = $row['created_by']; 
		} 
	}

    /**
     * Generates a new password recovery token and returns it.
     *
     * The internal format of the token is a Unix timestamp of when it was set (for checking if it's stale), an
     * underscore, and then the token itself.
     *
     * @return string A new password token for embedding in a link and emailing a user.
     */
    public function setPasswordRecoveryToken() {
        $token = md5(uniqid(rand()));
        $dao = DAOFactory::getDAO('UserDAO');
        $dao->updatePasswordToken($this->email, $token . '_' . time());
        return $token;
    }


    /**
     * Returns whether a given password recovery token is valid or not.
     *
     * This requires that the token not be stale (older than a day), and that  token itself matches what's in the
     * database.
     *
     * @param string $token The token to validate against the database.
     * @return bool Whether the token is valid or not.
     */
    public function validateRecoveryToken($token) {
        $data = explode('_', $this->password_token);
        return ((time() - $data[1] <= 86400) && ($token == $data[0]));
    }


} 