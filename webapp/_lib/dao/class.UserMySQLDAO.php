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
class UserMySQLDAO extends PDODAO {
    /**
     *
     * @var str
     */

    public static $default_salt = "ab194d42da0dff4a5c01ad33cb4f650a7069178b";

    public function getByEmail($email) {
        $q = <<<SQL
SELECT
    id,
    first_name,
    last_name,
    email,
    last_login,
    account_status,
    activation_code,
    created_by,
    account_status,
    failed_logins
FROM #prefix#user AS u 
WHERE email = :email;
SQL;
        $vars = array(
            ':email'=>$email
        );
        
        $ps = $this->execute($q, $vars);
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ret = $this->getDataRowAsObject($ps, 'User');
        return $ret;
    }

    public function getById($id) {


        $q = <<<SQL
SELECT
    id,
    first_name,
    last_name,
    email,
    last_login,
    account_status,
    activation_code,
    created_by,
    account_status,
    failed_logins
FROM #prefix#user AS u 
WHERE id = :id;
SQL;
        $vars = array(
            ':id'=>$id
        );
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return $this->getDataRowAsObject($ps, 'User');
    }

    public function getUserDetails($user_id) {

    }

    public function getAllUsersInfo($page_count=10, $page_number=1) {
        $start_on_record = ($page_number - 1) * $page_count;

        $q="SELECT u.*, s.first_name as parent_first, s.last_name as parent_last FROM #prefix#user_info u ";
        $q .="inner join #prefix#user s on u.parent_id=s.id WHERE u.account_status=10 ";

        //$q = "SELECT * FROM #prefix#user_info WHERE account_status=10 ";
        $q .= "ORDER BY id DESC  LIMIT :start_on_record, :limit;";
        $vars = array(
            ":start_on_record"=>(int)$start_on_record,
            ":limit"=>(int)$page_count
        );
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        $users = $this->getDataRowsAsArrays($ps);

        return $users;        
    }

    public function getAllUsers($params) {
        $vars = array();

        $q = <<<SQL
SELECT
    id,
    parent_id,
    employee_id,
    first_name,
    last_name,
    user_type,
    email,
    sec_email,
    phone_no,
    sec_phone_no,
    address_id1,
    address_id2,
    gender,
    birth_date,
    photo_url,
    account_status
FROM #prefix#user_info AS u 
SQL;
        //$q = "SELECT id,first_name,last_name,email,is_activated, account_status, user_type, last_login,account_status ";
        //$q .= "crated, created_by FROM #prefix#user_info WHERE account_status != 15 ";
        //$q = "SELECT id,first_name,last_name,email,is_activated, account_status, user_type, last_login,account_status ";
        //$q .= "crated, created_by FROM #prefix#user_info WHERE account_status != 15 ";

        $q .= "ORDER BY last_login DESC;";

        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return $this->getDataRowsAsObjects($ps, 'User');
    }

    public function getAdmins() {
        $q = " SELECT id, first_name, last_name,user_type, email, last_login, account_status ";
        $q .= "FROM #prefix#user WHERE user_type =1000 OR user_type =1001 OR user_type =1002 ORDER BY id";
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q);
        $admins = $this->getDataRowsAsObjects($ps, 'User');
        if (count($admins) == 0) { $admins = null; }
        return $admins;
    }

    public function doesUserExist($email) {

        $q = " SELECT email FROM #prefix#user WHERE email=:email";
        $vars = array(
            ':email'=>$email
        );
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);

        return $this->getDataIsReturned($ps);
    }

    public function getPass($email) {
        $q = "SELECT pwd FROM #prefix#user  WHERE email = :email LIMIT 1;";
        $vars = array(
            ':email'=>$email
        );
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        $result = $this->getDataRowAsArray($ps);
        if (isset($result['pwd'])) {
            return $result['pwd'];
        } else {
            return false;
        }
    }

    public function getActivationCode($email) {
        $q = " SELECT activation_code  FROM #prefix#user  WHERE email=:email";
        $vars = array(
            ':email'=>$email
        );
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return $this->getDataRowAsArray($ps);
    }

    public function activateUser($email) {
        $this->updateActivation($email, true);
    }

    public function deactivateUser($email) {
        $this->updateActivation($email, false);
    }

    /**
     * Set the value of the is_activated field.
     * @param str $email
     * @param bool $is_activated
     * @return int Count of affected rows
     */
    private function updateActivation($email, $account_status) {
        $q = " UPDATE #prefix#user SET account_status=:account_status WHERE email=:email";
        $vars = array(
            ':email'=>$email,
            ':account_status'=>($account_status?11:10)
          );
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return $this->getUpdateCount($ps);
    }

    public function updatePassword($email, $pwd) {
        // Generate new unique salt and store it in the database
        $salt = $this->generateSalt($email);
        $this->updateSalt($email, $salt);
        //Hash the password using the new salt
        $hashed_password = $this->hashPassword($pwd, $salt);
        //Store the new hashed password in the database
        $q = " UPDATE #prefix#user SET pwd=:hashed_password WHERE email=:email";
        $vars = array(
            ':email'=>$email,
            ':hashed_password'=>$hashed_password
        );
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return $this->getUpdateCount($ps);
    }

    public function create($user_arr) {
        return $this->createUser($user_arr);
    }

    public function createAdmin($email, $pass, $full_name) {
        return $this->createUser($email, $pass, $full_name, true);
    }

    private function createUserInfo($user_arr) {

        //$info_arr['user_id'] = $this->getInsertId($ps);
        //$info_arr['parent_id'] = $this->getByEmail($reporting)->id;

        $ret = $this->makeInsertQueryArray($user_arr);

        $q = "INSERT INTO #prefix#user_info SET ".$ret['q'];
        $q .= ", created_date=NOW();";

        $ps = $this->execute($q, $ret['vars']);

        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        return $this->getUpdateCount($ps);

    }

    private function createClientInfo($user_arr) {



    }

    private function createUser($user_arr) {
        
        $user_arr['created_by'] = SessionCache::get('user_id');

        $info_arr = $user_arr;
        $info_arr['parent_id'] = $parent_id;

        if (!$this->doesUserExist($email)) {

            
            $user_arr['activation_code'] = rand(100000, 999999);
            //Processing password
            $pwd_salt = $this->generateSalt($email);            
            $pwd = $user_arr['pwd'];
            $user_arr['pwd_salt'] = $pwd_salt;
            $user_arr['pwd'] = $this->hashPassword($pwd, $pwd_salt);


            $ret = $this->makeInsertQueryArray($user_arr);

            $q = "INSERT INTO #prefix#user SET ".$ret['q'];
            $q .= ", created_date=NOW();";

            if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
            $ps = $this->execute($q, $ret['vars']);

            $this->getInsertId($ps);
            return $user_arr['activation_code'];
        } else {
            return false;
        }
    }

    public function updateLastLogin($email) {
        $q = " UPDATE #prefix#user SET last_login=now() WHERE email=:email";
        $vars = array(
            ':email'=>$email
        );
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return $this->getUpdateCount($ps);
    }

    public function updatePasswordToken($email, $token) {
        $q = "UPDATE #prefix#user
              SET password_token=:token
              WHERE email=:email";
        $vars = array(
            ":token" => $token,
            ":email" => $email
        );
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return $this->getUpdateCount($ps);
    }

    public function getByPasswordToken($token) {
        $q = "SELECT * FROM #prefix#user WHERE password_token LIKE :token";
        $vars = array(':token' => $token . '_%');
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return $this->getDataRowAsObject($ps, 'User');
    }

    public function doesAdminExist() {
        $q = "SELECT id FROM #prefix#user WHERE is_admin = 1";
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q);
        return $this->getDataIsReturned($ps);
    }


    public function incrementFailedLogins($email) {
        $q = "UPDATE #prefix#user
              SET failed_logins=failed_logins+1
              WHERE email=:email";
        $vars = array(
            ":email" => $email
        );
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return ( $this->getUpdateCount($ps) > 0 )? true : false;
    }

    public function resetFailedLogins($email) {
        $q = "UPDATE #prefix#user
              SET failed_logins=0
              WHERE email=:email";
        $vars = array(
            ":email" => $email
        );
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return ( $this->getUpdateCount($ps) > 0 )? true : false;
    }

    /**
     * Generate a unique, random salt by appending the users email to a random number and returning the hash of it
     * @param str $email
     * @return str Salt
     */
    private function generateSalt($email){
        return hash('sha256', rand().$email);
    }

    /**
     * Hashes a password with a given salt.
     * @param str $password
     * @param str $salt
     * @param str Hashed password
     */
    private function hashPassword($password, $salt) {
        return hash('sha256', $password.$salt);
    }

    /**
     * Retrives the salt for a given user
     * @param str $email
     * @return str Salt
     */
    private function getSaltByEmail($email){
        $q = "SELECT pwd_salt ";
        $q .= "FROM #prefix#user u ";
        $q .= "WHERE u.email = :email";
        $vars = array(':email'=>$email);
        $ps = $this->execute($q, $vars);
        $query = $this->getDataRowAsArray($ps);
        return $query['pwd_salt'];
    }

    /**
     * Updates the password salt for a given user
     * @param str $email
     * @param str $salt
     * @return int Number of rows updated
     */
    private function updateSalt($email, $salt) {
        $q = " UPDATE #prefix#user SET pwd_salt=:salt WHERE email=:email";
        $vars = array(
            ':email'=>$email,
            ':salt'=>$salt
        );
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return $this->getUpdateCount($ps);
    }

    /**
     * DEPRECATED: This method of password-hashing is no longer used. It's still here for backwards compatibility.
     * @param str $pwd Password
     * @return str MD5-hashed password
     */
    private function md5pwd($pwd) {
        return md5($pwd);
    }

    /**
     * DEPRECATED: This method of password-hashing is no longer used. It's still here for backwards compatibility.
     * @param str $pwd Password
     * @return str SHA1-hashed password
     */
    private function sha1pwd($pwd) {
        return sha1($pwd);
    }
    /**
     * DEPRECATED: This method of password-hashing is no longer used. It's still here for backwards compatibility.
     * @param str $pwd
     * @return str Salted SHA1 password
     */
    private function saltedsha1($pwd) {
        return sha1(sha1($pwd.self::$default_salt).self::$default_salt);
    }

    /**
     * DEPRECATED: This method of password-hashing is no longer used. It's still here for backwards compatibility.
     * Encrypt password
     * @param str $pwd password
     * @return str Encrypted password
     */
    private function pwdCrypt($pwd) {
        return $this->saltedsha1($pwd);
    }

    /**
     * DEPRECATED: This method of password-hashing is no longer used. It's still here for backwards compatibility.
     * Check password
     * @param str $pwd Password
     * @param str $result Result
     * @return bool Whether or submitted password matches check
     */
    private function pwdCheck($pwd, $result) {
        if ($this->saltedsha1($pwd) == $result || $this->sha1pwd($pwd) == $result || $this->md5pwd($pwd) == $result) {
            return true;
        } else {
            return false;
        }
    }

    public function isUserAuthorized($email, $password) {
        // Get salt from the database
        $db_salt = $this->getSaltByEmail($email);
        // Get password from the database
        $db_password = $this->getPass($email);

        if ($db_salt == self::$default_salt) { //using old, default salt
            $hashed_pwd = $this->pwdCrypt($password); // Hash the old way
            return $this->pwdCheck($password, $db_password); //Check the old way
        } else {
            $hashed_pwd = $this->hashPassword($password, $db_salt); // Hash the new way
            // Check if it matches the password stored in the database
            return ($hashed_pwd == $db_password);
        }
    }

    public function setTimezone($email, $timezone) {
        $q = "UPDATE #prefix#owners
             SET timezone=:timezone
             WHERE email=:email";
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $stmt = $this->execute($q, array(':timezone' => $timezone, ':email' => $email));
        return $this->getUpdateCount($stmt);
    }

}
