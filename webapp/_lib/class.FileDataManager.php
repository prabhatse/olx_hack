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
class FileDataManager {
    /**
     * Get the path to a file based on $EFC_CFG['datadir_path'].
     * Default to webapp root folder /data/ directory if
     * config file doesn't exist or it does exist and the datadir_path value is not set.
     * @param str $file File or directory to get the path of
     * @return str Absolute path to file
     */
    public static function getDataPath($file=null) {
        try {
            $path = Config::getInstance()->getValue('datadir_path');
        } catch (ConfigurationException $e) {
            $path = EFC_WEBAPP_PATH.'data/';
        }
        if ($path=='') { //config file exists but datadir_path is not set
            $path = EFC_WEBAPP_PATH.'data/';
        }
        $path = preg_replace('/\/*$/', '', $path);
        if ($file) {
            $path = $path . '/' . $file;
        } else {
            $path = $path.'/';
        }
        return $path;
    }

    /**
     * Get the path to a file based on $EFC_CFG['datadir_path']
     * Default to webapp root folder /data/ directory if
     * config file doesn't exist or it does exist and the datadir_path value is not set.
     * @param str $file File or directory to get the path of
     * @return str Absolute path to file
     */
    public static function getBackupPath($str = null) {
        $path = 'backup/';
        $path = self::getDataPath($path);
        if (!file_exists($path)) {
            mkdir($path);
            @chmod($path, 0777);
        }
        if ($str) {
            $path = $path . $str;
        }
        return $path;
    }
}