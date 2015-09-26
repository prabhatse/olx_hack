<?php

class NotifyMySQLDAO extends PDODAO {

  public function getAllNotification ($user_id) {
    $q = "SELECT * FROM #prefix#map_nofity WHERE user_id=:user_id ";
    $q .= "ORDER BY created_at DESC;";

    $vars = array(':user_id' => $user_id);

    if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
    $ps = $this->execute($q, $vars);
    $results = $this->getDataRowsAsArrays($ps);

  }

  public function insertNotification($notify) {
    $ret = $this->makeInsertQueryArray($notify);
    $q = "INSERT INTO #prefix#notify SET ".$ret['q'];
    $q .= ", created_at=NOW();";
    if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
    $ps = $this->execute($q, $ret['vars']);
    $result = $this->getUpdateCount($ps);
    return ($result > 0);
  }


  public function insertMapNotification ($makeNotify) {
    $ret = $this->makeInsertQueryArray($makeNotify);
    $q = "INSERT INTO #prefix#map_notify SET ".$ret['q'];
    $q .= ", created_at=NOW();";
    if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
    $ps = $this->execute($q, $ret['vars']);
    $result = $this->getUpdateCount($ps);
    return ($result > 0);
  }

  public function updateMapNotification ($id) {
    $q = "UPDATE #prefix#map_notify SET read_at=NOW(), seen=1 ";
    $q .= "WHERE id=:id;";

    $vars = array(
            ':id' => $id
              );

    if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
    $ps = $this->execute($q, $vars);
    $result = $this->getUpdateCount($ps);
    return ($result > 0);
    
  
  }

}
