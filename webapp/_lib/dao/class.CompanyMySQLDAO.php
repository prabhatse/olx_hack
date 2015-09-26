<?php

class CompanyMySQLDAO extends PDODAO {


  public function getAllCompanyName($prefix=NULL,$ajaxReq = false) {
      if ($ajaxReq == true)
        $q = "SELECT id,name FROM #prefix#company ";
      else 
        $q = "SELECT * FROM #prefix#company ";
      if (isset($prefix)) {
          $q .= "WHERE name LIKE '".$prefix."%'";
          //@TODO: have to implement search field query 
      }
      $q .= " ORDER BY name ASC;";

      if($this->profiler_enabled) {Profiler::setDAOMethod(__METHOD__); }
      $ps = $this->execute($q);
      return $this->getDataRowsAsArrays($ps);
      
  }


  public function getCompanyNameById($company_id) {
      $q = "SELECT * FROM #prefix#company WHERE id=".$company_id;
      
      if($this->profiler_enabled) {Profiler::setDAOMethod(__METHOD__); }
      $ps = $this->execute($q);
      return $this->getDataRowAsArray($ps);
  }

  public function getCompanyDetailsById($branch_id) {
      $q = "SELECT * FROM #prefix#company_branch WHERE id=".$branch_id;
      if($this->profiler_enabled) {Profiler::setDAOMethod(__METHOD__); }
      $ps = $this->execute($q);
      return $this->getDataRowAsArray($ps);
  }


  //@TODO: have to do search functionality by search field type
  public function getAllCompaniesDetails($search_field) {
      $vars = array();
      $q = "SELECT * FROM #prefix#company_branch ";
      if (isset($search_field)) {
          //@TODO: have to implement search field query 
      }
      $q .= "ORDER BY name ASC;";
 
      if($this->profiler_enabled) {Profiler::setDAOMethod(__METHOD__); }
      $ps = $this->execute($q,$vars);
      return $this->getDataRowsAsArrays($ps);
  }

  public function insertCompanyName($company_name) {
      $q = "INSERT INTO #prefix#company SET name=:company_name, ";
      $q .= "added_by=:added_by, added_date=NOW();";
 
      $vars = array(
        ':company_name' => $company_name,
        ':added_by' => SessionCache::get('user_id')
      );
 
      if ($this->profiler_enabled) { Profiled::setDAOMethod(__METHOD__); }
      $ps = $this->execute($q,$vars);
      return $this->getUpdateCount($ps);
  }


  //Inserting company details here 
  public function insertCompanyDetails($branch_data) {

      $ret = $this->makeInsertQueryArray($branch_data);

      $q = "INSERT INTO #prefix#user SET ".$ret['q'];
      $q .= ", added_date=NOW();";

      if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
      $ps = $this->execute($q, $ret['vars']);
      return $this->getUpdateCount($ps);
  }

  public function modifyCompanyName($id, $value) {
  
  }

  public function modifyCompanyDetails($id, $company_data) {
  
  }

  
}
