<?php
/**
 *
 * sternDev/webapp/<file_name>
 *
 * Copyright (c) 2015-2016 Stern India
 *
 * Cookie Data Access Object
 * The data access object for creating and deleting Cookie records from the database
 *
 * @copyright 2015-2016 Stern India
 * @author Prabhat Shankar <prabhat@sternindia.com>
 *
 */
class CookieMySQLDAO extends PDODAO implements CookieDAO {
    /**
     * Generate a unique cookie for an owner email.
     * @param str $email Email for which to generate cookie
     * @return str Cookie generated
     */
    public function generateForEmail($email) {
        // We generate a cookie string using hash() and the email, time, some randomness
        // We try three times to insert it, because of the unique constraint on the table.  But once will work
        // 99.9999942% of the time.
        for ($i=0; $i<3; $i++) {
            $try = hash('sha256', (time() . $email . mt_rand()));
            $q = "INSERT INTO #prefix#cookies (owner_email, cookie) VALUES (:email, :cookie)";
            $vars = array( ':email' => $email, ':cookie' => $try);
            if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
            try {
                $res = $this->execute($q, $vars);
                if ($this->getInsertCount($res) > 0) {
                    $cookie = $try;
                    break;
                }
            } catch (PDOException $e) {
                if (!preg_match("/Duplicate entry .* for key 'cookie'/", $e->getMessage())) {
                    throw $e;
                }
                $try = null;
                //do nothing, loop will come back around
            }
        }
        return $cookie;
    }

    /**
     * Delete all cookies for a given email.
     * @param str $email Who are we deleting the cookies for?
     * @return bool Did we delete them?
     */
    public function deleteByEmail($email) {
        $q = "DELETE FROM #prefix#cookies WHERE owner_email = :email ";
        $vars = array(':email' => (string) $email);
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $res = $this->execute($q, $vars);
        return $this->getUpdateCount($res) > 0;
    }

    /**
     * Delete a given cookie.
     * @param str $cookie What cookie record to delete
     * @return bool Did we delete it?
     */
    public function deleteByCookie($cookie) {
        $q = "DELETE FROM #prefix#cookies WHERE cookie = :cookie ";
        $vars = array(':cookie' => (string) $cookie);
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $res = $this->execute($q, $vars);
        return $this->getUpdateCount($res) > 0;
    }

    /**
     * Get email associated with a cookie.
     * @param str $cookie Cookie we are attempting to find.
     * @return str Associated email or null
     */
    public function getEmailByCookie($cookie) {
        $q = "SELECT owner_email FROM #prefix#cookies WHERE cookie = :cookie ";
        $vars = array(':cookie' => (string) $cookie);
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $res = $this->execute($q, $vars);
        $data = $this->getDataRowAsArray($res);
        if ($data) {
            return $data['owner_email'];
        }
        return null;
    }
}
