<?php
/**
 *
 * sternDev/webapp/_lib/model/class.User.php
 *
 * Copyright (c) 2015-2016 Stern India
 *
 *
 * @copyright 2015-2016 Stern India
 * @author Prabhat Shankar <prabhat@sternindia.com>
 *
 */

class User { 
    /**
     * @var int 
     */ 
    var $id; 

    /**
     * @var int 
     */ 
    var $parent_id; 
    /**
     * @var str 
     */ 
    var $first_name; 
    /**
     * @var str 
     */ 
    var $last_name; 
    /**
     * @var str 
     */ 
    var $username; 
    /**
     * @var int user type. 
     */ 
    var $user_type; 
    /**
     * @var str 
     */
    var $email;
    /** 
     * @var str Hash of the owner password 
     */
    var $pwd; 
    /**
     * @var str Salt for securely hashing the owner password 
     */ 
    var $pwd_salt; 
    /**
     * @var str
     */ 
    var $address; 
    /** 
     * @var int 
     */

    var $state_id; 
    /**
     * @var int
     */ 
    var $city_id; 
    /**
     * @var int
     */ 
    var $country_id;
    /** 
     * @var str 
     */           
    var $postcode; 
    /**
     * @var int
     */
    var $gender; 
    /** 
     * @var date 
     */ 
    var $birth_date; 
    /**
     * @var str 
     */
    var $phone_no; 
    /**
     * @var str
     */
    var $sec_phone_no; 
    /**
     * @var str
     */ 
    var $photo_url; 
    /** 
     * @var int 
     */ 
    var $email_notification; 
    /**
     * @var str 
     */ 
    var $timezone; 
    /**
     * @var str Date user registered for an account. 
     */ 
    var $created; 
    /**
     * @var str
     */ 
    var $modified; 
    /**
     * @var int If user is activated, 1 for true, 0 for false. 
     */ 
    var $account_status; 
    /**
     * @var int User activation code.
     */ 
    var $activation_code; 
    /**
     * @var str Last time user logged into ThinkUp.
     */ 
    var $last_login; 
    /**
     * @var str 
     */ 
    var $last_ip; 
    /** 
     * @var str Password reset token. 
     */ 
    var $password_token; 
    /** 
     * @var int Current number of failed login attempts. 
     */ 
    var $failed_logins; 
    /** 
     * @var str 
     */ 
    var $sec_email; 

    public function __construct($row = false) { 
        if ($row) { 
            $this->id = $row['id']; 
            $this->parent_id = $row['parent_id']; 
            $this->first_name = $row['first_name']; 
            $this->last_name = $row['last_name']; 
            $this->username = $row['username']; 
            $this->user_type = $row['user_type']; 
            $this->email = $row['email']; 
            $this->pwd = $row['pwd']; 
            $this->pwd_salt = $row['pwd_salt']; 
            $this->address = $row['address']; 
            $this->state = $row['state']; 
            $this->city = $row['city']; 
            $this->postcode = $row['postcode']; 
            $this->country_id = $row['country_id']; 
            $this->gender = $row['gender']; 
            $this->birth_date = $row['birth_date']; 
            $this->phone_no = $row['phone_no']; 
            $this->sec_phone_no = $row['sec_phone_no']; 
            $this->photo_url = $row['photo_url']; 
            $this->email_notification = $row['email_notification']; 
            $this->timezone = $row['timezone']; 
            $this->created = $row['created']; 
            $this->modified = $row['modified']; 
            $this->account_status = $row['account_status']; 
            $this->activation_code = $row['activation_code']; 
            $this->last_login = $row['last_login']; 
            $this->last_ip = $row['last_ip']; 
            $this->password_token = $row['password_token']; 
            $this->failed_logins = $row['failed_logins']; 
            $this->sec_email = $row['sec_email']; 
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

