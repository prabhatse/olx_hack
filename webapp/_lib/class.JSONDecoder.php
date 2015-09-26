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

class JSONDecoder {
    /**
     * Decode JSON.
     * @param str $json
     * @param bool $assoc Whether or not to return an associative array, defaults to false
     * @return mixed Decoded JSON
     * @throws JSONDecoderException
     */
    public static function decode($json, $assoc = false) {
        if (empty($json)) {
            throw new JSONDecoderException('Cannot decode an empty string');
        }
        $result = json_decode($json, $assoc);
        /*
         http://www.php.net/manual/en/function.json-last-error.php
         JSON_ERROR_NONE  No error has occurred
         JSON_ERROR_DEPTH    The maximum stack depth has been exceeded
         JSON_ERROR_STATE_MISMATCH   Invalid or malformed JSON
         JSON_ERROR_CTRL_CHAR    Control character error, possibly incorrectly encoded
         JSON_ERROR_SYNTAX   Syntax error
         JSON_ERROR_UTF8 Malformed UTF-8 characters, possibly incorrectly encoded    PHP 5.3.3
         */
        if (function_exists('json_last_error')) { //PHP 5.3 and later
            switch (json_last_error()) {
                case JSON_ERROR_DEPTH:
                    $error =  'The maximum stack depth has been exceeded';
                    break;
                case JSON_ERROR_STATE_MISMATCH:
                    $error =  'Invalid or malformed JSON';
                    break;
                case JSON_ERROR_CTRL_CHAR:
                    $error = 'Control character error, possibly incorrectly encoded';
                    break;
                case JSON_ERROR_SYNTAX:
                    $error = 'Syntax error due to malformed JSON';
                    break;
                    //            PHP 5.3.3 only
                    //            case JSON_ERROR_UTF8:
                    //                $error = 'Malformed UTF-8 characters, possibly incorrectly encoded';
                    //                break;
                case JSON_ERROR_NONE:
                default:
                    $error = '';
            }
        }
        if (!empty($error)) {
            throw new JSONDecoderException('JSON Error: '.$error);
        } elseif ($result === null) {
            throw new JSONDecoderException('JSON Error decoding data');
        }
        return $result;
    }
}