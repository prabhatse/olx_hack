<?php


class LocationMySQLDAO extends PDODAO {

  public function getLocationById ($loc_id) {
    $q = "SELECT * FROM #prefix#location WHERE id=".$loc_id;
    if($this->profiler_enabled) {Profiler::setDAOMethod(__METHOD__); }
    $ps = $this->execute($q);
    return $this->getDataRowAsArray($ps);
  }

  public function getAllLocation($find_arr,$ajaxReq = false) {
    if ($ajaxReq == true)
        $q = "SELECT location_id,name FROM #prefix#location ";
    else 
        $q = "SELECT * FROM #prefix#location ";
    if(isset($find_arr['prefix'])) {
      $q .= "WHERE  name LIKE '".$find_arr['prefix']."%'";
      if (isset($find_arr['location_type'])) {
        $q .= " AND location_type=".$find_arr['location_type'];
      }
      if (isset($find_arr['parent_id'])) {
        $q .= " AND parent_id=".$find_arr['parent_id'];
      }
      
    }
    $q .= " ORDER BY name ASC;";

    if($this->profiler_enabled) {Profiler::setDAOMethod(__METHOD__); }
    $ps = $this->execute($q);
    return $this->getDataRowsAsArrays($ps);
  }

	public function insertLocation(array $loc_arr) {

		//$loc_arr shoulb be two indexed valur as bounding_box and info
        //Processing longlat 
        $this->logger->logDebug("Processing Location: " . $loc_arr['name']."With Parent-ID: ".
        	$loc_arr['parent_id']."as LocType: ".$loc_arr['location_type'], __METHOD__.','.__LINE__);

        if (isset($loc_arr['bounding_box'])) {
          $polystr = $this->makeLongLatBoundry($loc_arr['bounding_box']);	
        } else {
          $polystr = NULL;
        }
        
        $ret = $this->makeInsertQueryArray( $loc_arr['info'] );

        $vars = $ret['vars'];
        $vars[':bounding_box'] = $polystr;
        $vars[':added_by'] = SessionCache::get('user_id');
        $vars[':status'] = 1;

        $q = "INSERT IGNORE INTO #prefix#location SET ".$ret['q'];
        $q .= ", bounding_box=:bounding_box, longlat=Centroid(PolygonFromText(:bounding_box))";
        $q .= ", added_by=:added_by, added_date=NOW(), status=:status";

        $ps = $this->execute($q, $vars);
        $logstatus = "Location (".$loc_arr['name'].") added to DB by (".SessionCache::get('user_name').")";
        $this->logger->logInfo($logstatus, __METHOD__.','.__LINE__);
        return $this->getUpdateCount($ps);        

	}


  public function enableLocation($location_id) {
    
		$modified_by = SessionCache::get('user_id');

    $q = " UPDATE #prefix#city SET status=:status , modified_by = :modified_by, modified_date = NOW() WHERE id=:city_id";

    $vars=array(
    	':city_id' => $city_id,
      ':modified_by' => $modified_by,
      ':status' => 1
    	);
    if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
    $ps = $this->execute($q, $vars);
    return $this->getUpdateCount($ps);
  }
  
  public function disableLocation($location_id) {
    
    $modified_by = SessionCache::get('user_id');

    $q = " UPDATE #prefix#city SET status=:status , modified_by = :modified_by, modified_date = NOW() WHERE id=:city_id";

    $vars=array(
    	':city_id' => $city_id,
      ':modified_by' => $modified_by,
      ':status' => 0
    	);
    if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }

    $ps = $this->execute($q, $vars);
    return $this->getUpdateCount($ps);

  }

  public function modifyLocation($modify_arr) {
  
  }
  


	public function modifyCity($city_id,$update_arr) {
/*
	    $modified_by = SessionCache::get('user_id');

		$q = " UPDATE #prefix#city SET modified_by = :modified_by, modified_date = NOW()";

		$vars = array();
		foreach ($update_arr as $key => $value) {
			$q .=", ".$key."=:".$value;
			$field = ":".$key;
			$vars[$field] = $value;
		}

		$vars[':modified_by'] = $modified_by;
		$vars[':city_id'] = $city_id;
		$q .=" WHERE id =:city_id";
		if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return $this->getUpdateCount($ps);
 */
  }

	public function deleteLocation($loc_id) {

		$q = "DELETE from #prefix#location WHERE id = :loc_id LIMIT 1";
        $vars = array(
            ':loc_id'=>$loc_id,
        );
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return $this->getUpdateCount($ps);
	}	
}
