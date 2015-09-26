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

class UserErrorMySQLDAO extends PDODAO implements UserErrorDAO {

    public function insertError($id, $error_code, $error_text, $issued_to, $network) {
        $q = "INSERT INTO #prefix#user_errors (user_id, error_code, error_text, error_issued_to_user_id, network) ";
        $q .= "VALUES (:id, :error_code, :error_text, :issued_to, :network) ";
        $vars = array(
            ':id'=>$id,
            ':error_code'=>$error_code,
            ':error_text'=>$error_text,
            ':issued_to'=>(string)$issued_to,
            ':network'=>$network
        );
        $ps = $this->execute($q, $vars);

        return $this->getInsertCount($ps);
    }
}
