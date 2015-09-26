<?php
/**
 *
 * sternDev/webapp/_lib/dao/class.CountryMySQLDAO.php
 *
 * Copyright (c) 2015-2016 Stern India
 *
 * DAO for Country
 *
 * @copyright 2015-2016 Stern India
 * @author Prabhat Shankar <prabhat@sternindia.com>
 *
 */
class CountryMySQLDAO extends PDODAO {

	public function insertCountry(array $country_arr) {
        //Processing longlat 
        $this->logger->logDebug("processing country: " . $country_arr['country_name'], __METHOD__.','.__LINE__);
        $bounding_box = array(
        		'coordinates'=> array(
        			 array($country_arr['north'],$country_arr['east']),
        			 array($country_arr['north'],$country_arr['west']),
        			 array($country_arr['south'],$country_arr['west']),
        			 array($country_arr['south'],$country_arr['east'])
        			 ) 
        	);

        //@TODO check that type is polygon
        $coords = $bounding_box['coordinates'];
        // build bbox string
        $points = array();

        foreach ($coords as $coord) {
            $points[] = $coord[0] . " " . $coord[1];
        }
        // complete w/ first point again
        $points[] = $coords[0][0] . " " . $coords[0][1];
        $polystr = 'Polygon((' . join(',', $points) . '))';	

		$country_name = $country_arr['country_name'];
		$added_by = SessionCache::get('user_id');
		$short_name = $country_arr['short_name'];
		$continent_name = $country_arr['continent_name'];

		if ($country_arr['status']) {
			$status = $country_arr['status'];
		} else {
			$status = 1;
		}
	
        $q  = "INSERT IGNORE INTO #prefix#country ";
        $q .= "(country_name, short_name, continent_name, longlat, added_by, added_date, status) ";
        $q .= "VALUES (:country_name, :short_name, :continent_name, Centroid(PolygonFromText(:bounding_box)), :added_by, NOW(), :status )";

		$vars = array(
			':country_name' => $country_name,
			':short_name' => $short_name,
			':continent_name' => $continent_name,
			':added_by' => $added_by,
			':status' => $status,
			':bounding_box' => $polystr
			);

        $ps = $this->execute($q, $vars);
        $logstatus = "Country (".$country_name.") added to DB by (".SessionCache::get('user_name').")";
        $this->logger->logInfo($logstatus, __METHOD__.','.__LINE__);
        return $this->getUpdateCount($ps);
	}

	public function getCountryById($country_id) {

		$q = "SELECT * FROM #prefix#country WHERE id=:country_id";

		$vars = array(
			':country_id' => $country_id
			);

		$ps = $this->execute($q,$vars);
		if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
		$ret = $this->getDataRowAsArray($ps);
        return $ret;

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

