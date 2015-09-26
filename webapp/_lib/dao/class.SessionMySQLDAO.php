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
class SessionMySQLDAO extends PDODAO implements SessionDAO {
    /**
     * Open session handler.
     * In this case, does nothing because the database is managed outside this class.
     */
    public function open() {
    }

    /**
     * Close session handler.
     * In this case, does nothing because the database is managed outside this class.
     */
    public function close() {
    }

    public function read($session_id) {
        $q = "SELECT data FROM #prefix#sessions WHERE session_id=:session_id";
        $vars = array( ':session_id'=>$session_id );
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        $data = $this->getDataRowAsArray($ps);

        if (isset($data['data'])) {
            return $data['data'];
        } else {
            return '';
        }
    }

    public function write($session_id, $data) {
        $q = "REPLACE INTO #prefix#sessions (session_id, data, updated) VALUES (:session_id, :data, NOW())";
        $vars = array( ':session_id'=>$session_id, ':data' => $data );
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return true;
    }

    public function destroy($session_id) {
        
        $q = "DELETE FROM #prefix#sessions WHERE session_id=:session_id";
        $vars = array( ':session_id'=>$session_id );
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return true;
    }

    public function gc($max) {
        $q = "DELETE FROM #prefix#sessions WHERE updated < DATE_SUB(NOW(), INTERVAL :max SECOND)";
        $vars = array( ':max'=>$max );
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return true;
    }
}
