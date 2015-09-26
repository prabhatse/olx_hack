<?php
/**
 *
 *
 *
 * Mutex Data Access Object implementation
 *
 *
 */
class MutexMySQLDAO extends PDODAO implements MutexDAO {
    /**
     * NOTE: PDO does not seem to bind params in MySQL functions, so we escape parameters and concat them manually.
     */
    public function getMutex($name, $timeout=1) {
        $lock_name = $this->config->getValue('db_name').'.'.$name;
        /*
         $q = "SELECT GET_LOCK(':name', ':timeout') AS result";
         $vars = array(
         ':name' => $lock_name,
         ':timeout' => $timeout
         );
         $ps = $this->execute($q, $vars);
         */
        $q = "SELECT GET_LOCK('".$lock_name."', ".$timeout. ") AS result";
        $ps = $this->execute($q);
        $row = $this->getDataRowAsArray($ps);
        return $row['result'] === '1';
    }

    /**
     * NOTE: PDO does not seem to bind params in MySQL functions, so we escape parameters and concat them manually.
     */
    public function releaseMutex($name) {
        $lock_name = $this->config->getValue('db_name').'.'.$name;
        /*
         $q = "SELECT RELEASE_LOCK(':name') AS result";
         $vars = array(
         ':name' => $lock_name
         );
         $ps = $this->execute($q, $vars);
         */
        $q = "SELECT RELEASE_LOCK('".$lock_name."') AS result";
        $ps = $this->execute($q);
        $row = $this->getDataRowAsArray($ps);
        return $row['result'] === '1';
    }

    /**
     * NOTE: PDO does not seem to bind params in MySQL functions, so we escape parameters and concat them manually.
     */
    public function isMutexFree($name) {
        $lock_name = $this->config->getValue('db_name').'.'.$name;
        $q = "SELECT IS_FREE_LOCK('".$lock_name."') AS result";
        $ps = $this->execute($q);
        $row = $this->getDataRowAsArray($ps);
        return $row['result'] === '1';
    }

    /**
     * NOTE: PDO does not seem to bind params in MySQL functions, so we escape parameters and concat them manually.
     */
    public function isMutexUsed($name) {
        $lock_name = $this->config->getValue('db_name').'.'.$name;
        $q = "SELECT IS_USED_LOCK('".$lock_name."') AS result";
        $ps = $this->execute($q);
        $row = $this->getDataRowAsArray($ps);
        return $row['result'] != null;
    }
}