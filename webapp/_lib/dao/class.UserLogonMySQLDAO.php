<?php


class UserLogonMySQLDAO extends PDODAO {
	

	public function insertLoginInfo() {

		$user_id = SessionCache::get('user_id');
		$cookie = SessionCache::get('cookie');

		$curTime = date('H:i:s');

		SessionCache::put('login_time',$curTime);

		//$ip_address = $_SERVER['REMOTE_ADDR'];
		$ip_address = '127.0.0.1';

		//echo $ip_address;

		$q = "INSERT INTO #prefix#user_logon_info SET user_id=:user_id, cookie=:cookie, login=NOW(), ip_address=:ip_address"; 

		$vars = array(
			':user_id' => $user_id,
			':cookie' => $cookie,
			':ip_address' => $ip_address

			);


// /var_dump($vars);

		$ps = $this->execute($q, $vars);

	}

	public function updateLoginInfo() {


	}

	public function updateWorkingHour($user_id, $working_time) {

		$q = "INSERT INTO #prefix#user_working_hours   (user_id, working_date, working_hour)";
        $q .= "VALUES (:user_id,CURDATE(), :working_time) ";
        $q .= "ON DUPLICATE KEY UPDATE working_hour=working_hour+:working_time"; 

        $vars = array(
        	':user_id' => $user_id,
        	':working_time' => $working_time
        	);

        $ps = $this->execute($q, $vars);

  
	}

	public function userLogoutUpdate($reason = 1) {
	
		$user_id = SessionCache::get('user_id');
		$cookie = SessionCache::get('cookie');
        
		$q = "UPDATE #prefix#user_logon_info SET logout=NOW(), working_time = (logout-login)/60, logout_reason=:logout_reason ";
		$q .=  "WHERE user_id=:user_id AND cookie=:cookie";

		$vars = array(
				':user_id' => $user_id,
				':cookie' => $cookie,
				':logout_reason' => $reason
			);

		$ps = $this->execute($q, $vars);

		$loginTime = explode(":",SessionCache::get('login_time'));
		$logoutTime = explode(":",date('H:i'));

		$totalTime = (60*$logoutTime[0] + $logoutTime[1])  - (60*$loginTime[0] + $loginTime[1]);

		$this->updateWorkingHour($user_id,$totalTime);

		SessionCache::unsetKey('login_time');
		SessionCache::unsetKey('cookie');
	}


}