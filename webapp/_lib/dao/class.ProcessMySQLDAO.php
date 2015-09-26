
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
class ProcessMySQLDAO extends PDODAO {

  public function insertProcessDetails($process_data) {

    if(isset($process_data)) {
      $ret = $this->makeInsertQueryArray($process_data);

      //Make query for the process entry in database.
      $q = "INSERT IGNORE INTO #prefix#process SET ".$ret['q'];
      $q .= ", created_date=NOW(), status=1;";

      $ps = $this->execute($q, $ret['vars']);
      return $this->getInsertId($ps);
    }
    return false;
  }
  
  public function insertProcessConfigs($process_config_data) {
    if(isset($process_config_data)) {
      $ret = $this->makeInsertQueryArray($process_config_data);

      //Make query for the process entry in database.
      $q = "INSERT IGNORE INTO #prefix#process_config SET ".$ret['q'].";";

      $ps = $this->execute($q, $ret['vars']);
      return $this->getUpdateCount($ps);
    }
    return false;
  }


  public function insertSelectedProcessInMap($map_process) {
    if(isset($map_process)) {
      $ret = $this->makeInsertQueryArray($map_process);

      //Make query for the process entry in database.
      $q = "INSERT IGNORE INTO #prefix#map_company_process SET ".$ret['q'];
      $q .= ", selected_date=NOW();";

      $ps = $this->execute($q, $ret['vars']);
      return $this->getUpdateCount($ps);
    }
    return false;
  }

  public function getProcessList($params) {
			$q = "SELECT * FROM #prefix#process ORDER BY country_name WHERE status=:status";
  
			$vars = array(
				':status' => $status
				);
			$ps = $this->execute($q);
			if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
			$ret = $this->getDataRowsAsArrays($ps);
      return $ret;
  }

  public function getProcessConfigDetails($process_id) {
  
  }

  public function modifyProcess() {
  
  }

  public function deleteProcess() {
  
  }


	public function getAllCountry($status=0) {

		$q = "SELECT * FROM #prefix#country ORDER BY country_name WHERE status=:status";

		$vars = array(
			':status' => $status

			);

		$ps = $this->execute($q);
		if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
		$ret = $this->getDataRowsAsArrays($ps);
        return $ret;

	}

	public function makeCountryEnable($country_id) {

	    $modified_by = SessionCache::get('user_id');

		$status = 1;

        $q = " UPDATE #prefix#country SET status=:status, modified_by = :modified_by, modified_date=NOW() WHERE id=:country_id";

        $vars=array(
        	':country_id' => $country_id,
        	':modified_by' =>$modified_by
        	);
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return $this->getUpdateCount($ps);
	}

	public function modifyCountry($country_id,$update_arr) {

	    $modified_by = SessionCache::get('user_id');

		$q = " UPDATE #prefix#country SET modified_by=:modified_by,modified_date=NOW ";

		$vars = array();
		foreach ($update_arr as $key => $value) {
			$q .=", ".$key."=:".$value;
			$field = ":".$key;
			$vars[$field] = $value;
		}
		$vars[':modified_by'] = $modified_by;
		$vars[':country_id'] = $country_id;
		$q .=" WHERE id =:country_id";
		if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return $this->getUpdateCount($ps);
	}

	public function deleteCountry($country_id) {

		$q = "DELETE from #prefix#country WHERE id = :country_id LIMIT 1";
        $vars = array(
            ':country_id'=>$country_id,
        );
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return $this->getUpdateCount($ps);
	}	
}

