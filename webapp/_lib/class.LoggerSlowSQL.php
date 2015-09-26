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

class LoggerSlowSQL {
    var $log;

    public function __construct($location) {
        $this->log = $this->openFile($location, 'a'); # Append to any prior file
    }

    public function setUsername($uname) {
        $this->twitter_username = $uname;
    }

    public function logQuery($query, $time) {
        $log_signature = date("Y-m-d H:i:s", time())." | ".(string) number_format(round(memory_get_usage() / 1024000,
        2), 2)." MB | ";
        if (strlen($query) > 0) {
            $this->writeFile($this->log, $log_signature.$query." | ".$time." Seconds"); # Write status to log
        }
    }

    private function addBreaks() {
        $this->writeFile($this->log, ""); # Add a little whitespace
    }

    public function close() {
        $this->addBreaks();
        $this->closeFile($this->log);
    }

    public function openFile($filename, $type) {
        if (array_search($type, array('w', 'a')) < 0) {
            $type = 'w';
        }
        $filehandle = fopen($filename, $type);// or die("can't open file $filename");
        return $filehandle;
    }

    public function writeFile($filehandle, $message) {
        return fwrite($filehandle, $message."\n");
    }

    public function closeFile($filehandle) {
        return fclose($filehandle);
    }

    public function deleteFile($filename) {
        return unlink($filename);
    }
}