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
ob_start();  

class CSVReader {

    var $fields;            /** columns names retrieved after parsing */ 
    var $separator = ';';    /** separator used to explode each line */
    var $enclosure = '"';    /** enclosure used to decorate each field */

    var $max_row_size = 4096;    /** maximum row size to be used for decoding */

    public function parse_file($p_Filepath) {
        $file = fopen($p_Filepath, 'r');
        $this->fields = fgetcsv($file, $this->max_row_size, $this->separator, $this->enclosure);
        $keys_values = explode(',',$this->fields[0]);

        $content    =   array();
        $keys   =   $this->escape_string($keys_values);

        $i  =   1;
        while( ($row = fgetcsv($file, $this->max_row_size, $this->separator, $this->enclosure)) != false ) {            
            if( $row != null ) { // skip empty lines
                $values =   explode(',',$row[0]);
                if(count($keys) == count($values)){
                    $arr    =   array();
                    $new_values =   array();
                    $new_values =   $this->escape_string($values);
                    for($j=0; $j<count($keys); $j++){
                        if($keys[$j] != ""){
                            $arr[$keys[$j]] =   $new_values[$j];
                        }
                    }

                    $content[$i] = $arr;
                    $i++;
                }
            }
        }
        fclose($file);
        return $content;
    }

    private function escape_string($data){
        $result =   array();
        foreach($data as $row){
            $result[]   =   str_replace('"', '',$row);
        }
        return $result;
    }   
}
