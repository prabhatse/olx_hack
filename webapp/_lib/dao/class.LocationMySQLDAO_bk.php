<?php
/**
 *
 * sternDev/webapp/_lib/dao/class.LocationMySQLDAO.php
 *
 * Copyright (c) 2015-2016 Stern India
 *
 * Location Data Access Object
 * The data access object for retrieving and saving locations in the ThinkUp database
 *
 * @copyright 2015-2016 Stern India
 * @author Prabhat Shankar <prabhat@sternindia.com>
 *
 */

class LocationMySQLDAO extends PDODAO implements LocationDAO  {

    public function getLocation($location) {
        $q = "SELECT  l.* FROM #prefix#encoded_locations l ";
        $q .= " WHERE l.short_name=:name";
        $vars = array(
            ':name'=>$location,
        );
        $ps = $this->execute($q, $vars);
        $row = $this->getDataRowAsArray($ps);
        return $row;
    }

    public function addLocation($vals) {
        $q = "INSERT INTO #prefix#encoded_locations SET short_name = :short_name, full_name = :full_name, ";
        $q .= " latlng = :latlng";
        $vars = array(
            ':short_name'=>$vals['short_name'],
            ':full_name'=>$vals['full_name'],
            ':latlng'=>$vals['latlng']
        );
        $ps = $this->execute($q, $vars);
        $logstatus = "Location (".$vals['short_name'].") added to DB";
        $this->logger->logInfo($logstatus, __METHOD__.','.__LINE__);
        return $this->getUpdateCount($ps);
    }

    public function getAllLocations() {
        $q = "SELECT * FROM #prefix#encoded_locations";
        $ps = $this->execute($q);
        return $this->getDataRowsAsArrays($ps);
    }
}